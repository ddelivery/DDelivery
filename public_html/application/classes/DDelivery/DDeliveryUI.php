<?php
/**
*
* @package    DDelivery
*
* @author  mrozk
*/
namespace DDelivery;
use DDelivery\DB\ConnectInterface;
use DDelivery\Order\DDStatusProvider;
use DDelivery\Adapter\DShopAdapter;
use DDelivery\Sdk\DCache;
use DDelivery\Sdk\DDeliverySDK;
use DDelivery\Order\DDeliveryOrder;


/**
 * DDeliveryUI - Обертка рабочих классов, для взаимодействия
 * с системой DDelivery
 *
 * @package  DDelivery
 */
    class DDeliveryUI
    {
        /**
         * Поддерживаемые способы доставки
         * @var int[]
         */
        public $supportedTypes;
        /**
         * @var int
         */
        public $deliveryType = 0;

        /**
         * Api обращения к серверу ddelivery
         *
         * @var DDeliverySDK
         */
        public  $sdk;

        /**
         * Адаптер магазина CMS
         * @var DShopAdapter
         */
        private $shop;

        /**
         * Заказ DDelivery
         * @var DDeliveryOrder
         */
        private $order;

        /**
         * @var DCityLocator
         */
        public $cityLocator;

        /**
         *  Кэш
         *  @var DCache
         */

        private $cache;

        /**
         * @var ConnectInterface бд
         */
        private $pdo;
        /**
         * @var string префикс таблицы
         */
        private $pdoTablePrefix;

        /**
         * Запускает движок SDK
         *
         * @param DShopAdapter $dShopAdapter адаптер интегратора
         * @param bool $skipOrder запустить движок без инициализации заказа  из корзины
         * @throws DDeliveryException
         */
        public function __construct(DShopAdapter $dShopAdapter, $skipOrder = false)
        {
            $this->shop = $dShopAdapter;

            $this->sdk = new Sdk\DDeliverySDK($dShopAdapter->getApiKey(), $this->shop->isTestMode());


            // Инициализируем работу с БД
            $this->_initDb($dShopAdapter);

            // Формируем объект заказа
            if(!$skipOrder){
                $productList = $this->shop->getProductsFromCart();
                $this->order = new DDeliveryOrder( $productList );
                $this->order->amount = $this->shop->getAmount();

                $this->cityLocator = new DCityLocator( $this->sdk );

            }
            $this->cache = new DCache( $this->shop->getCacheExpired(), $this->pdo, $this->shop->isCacheEnabled(),
                                        $this->pdoTablePrefix );

        }

        /**
         *
         * Залоггировать ошибку
         *
         * @param \Exception $e
         * @return mixed
         */
        public function logMessage( \Exception $e ){
            $this->shop->logMessage($e);
        }

        public function createTables()
        {
            $cache = new DataBase\Cache($this->pdo, $this->pdoTablePrefix);
            $cache->createTable();
            $order = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            $order->createTable();
        }

        /**
         * Чистим кэш
         */
        public function cleanCache(){
            $this->cache->clean();
        }

        /**
         *
         * Получить статус заказа cms по статусу DD
         *
         * @param $ddStatus
         *
         * @return mixed
         */
        public function getLocalStatusByDD( $ddStatus ){
            return $this->shop->getLocalStatusByDD( $ddStatus );
        }

        /**
         *
         * Получить список незаконченых заказов
         *
         * @return array
         *
         */
        public function getNotFinishedOrders(){
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            $orders = $orderDB->getNotFinishedOrders();
            $ddOrders = array();
            if( count( $orders ) > 0 ){
                foreach( $orders as $item ){
                    $ddOrders[] = $this->initOrder($item->id);
                }
            }
            return $ddOrders;
        }
        /**
         * Получить все пользовательские поля по ID в БД SQLite
         *
         * Необходимо для того чтобы выставлять заглушки в полях
         * CMS если были заполнены поля DD формы. При обращении Нужно
         * будет конвертировать в json и отправлять
         *
         * @param DDeliveryOrder $order
         *
         * @return array
         */
        public function getDDUserInfo( $order )
        {
            return array('firstName' => $order->firstName, 'secondName' => $order->secondName,
                         'toPhone' => $order->toPhone, 'toEmail' => $order->toEmail,
                         'toStreet' => $order->toStreet, 'toHouse' => $order->toHouse,
                         'toFlat' => $order->toFlat, 'toIndex' => $order->toIndex
            );
        }

        /**
         * Функция вызывается при изменении статуса внутри cms для отправки
         *
         * @param $cmsID
         * @param $cmsStatus
         *
         * @return int|false
         */
        public function onCmsChangeStatus( $cmsID, $cmsStatus ){
            $order = $this->getOrderByCmsID( $cmsID );
            if( $order ){
                $order->localStatus = $cmsStatus;
                if( $this->shop->isStatusToSendOrder($cmsStatus) && $order->ddeliveryID == 0 ){
                    if($order->type == DDeliverySDK::TYPE_SELF){
                        return $this->createSelfOrder($order);
                    }elseif( $order->type == DDeliverySDK::TYPE_COURIER ){
                        return $this->createCourierOrder($order);
                    }
                }
            }
            return false;
        }

        /**
         * Отправить order в DD
         *
         * @param DDeliveryOrder $order
         * @return bool|int
         */
        public function sendOrderToDD( $order ){
            if($order->type == DDeliverySDK::TYPE_SELF){
                return $this->createSelfOrder($order);
            }elseif( $order->type == DDeliverySDK::TYPE_COURIER ){
                return $this->createCourierOrder($order);
            }
            return false;
        }


        /**
         *
         * Получить объект заказа из БД SQLite по его ID в CMS
         *
         * @param int $cmsOrderID id заказа в cms
         *
         * @return DDeliveryOrder
         *
         */
        function getOrderByCmsID( $cmsOrderID ){
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            $data = $orderDB->getOrderByCmsOrderID( $cmsOrderID );
            if( count($data) ){
                $id = $data[0]->id;
                $orderArr = $this->initOrder($id);
                return $orderArr;
            }else{
                return null;
            }
        }

        /**
         *
         * Обработчик изменения статуса заказа
         *
         * @param DDeliveryOrder $order  заказа в cms
         *
         * @return array
         *
         */
        public function changeOrderStatus( $order ){
            if( $order->ddeliveryID == 0 ){
                    return array();
            }
            $ddStatus = (int)$this->getDDOrderStatus($order->ddeliveryID);

            if( !$ddStatus ){
                return array();
            }
            $order->ddStatus = $ddStatus;
            $order->localStatus = $this->shop->getLocalStatusByDD( $order->ddStatus );
            $this->saveFullOrder($order);
            $this->shop->setCmsOrderStatus($order->shopRefnum, $order->localStatus);
            return array('cms_order_id' => $order->shopRefnum, 'ddStatus' => $order->ddStatus,
                         'localStatus' => $order->localStatus );
        }

        /**
         *
         * Получает статус заказа на сервере DD
         *
         * @param $ddeliveryOrderID
         *
         * @return int
         */
        public function getDDOrderStatus( $ddeliveryOrderID )
        {
            $response = $this->sdk->getOrderStatus($ddeliveryOrderID);
            if( isset($response->response['status']) ){
                return $response->response['status'];
            }else{
                return false;
            }
        }

        /**
         * После окончания оформления заказа вызывается в cms и передает заказ на обработку в DDelivery
         *
         * @param int $id id заказа в локальной БД SQLLite
         * @param string $shopOrderID id заказа в CMS
         * @param int $status выбираются интегратором произвольные
         * @param int $payment выбираются интегратором произвольные
         * @throws DDeliveryException
         *
         * @return bool
         */
        public function onCmsOrderFinish( $id, $shopOrderID, $status, $payment){
            $order = $this->initOrder( $id );
            if(!isset( $order->localId )){
                return false;
            }
            $order->paymentVariant = $payment;
            $order->shopRefnum = $shopOrderID;
            $order->localStatus = $status;

            $id = $this->saveFullOrder($order);
            return (bool)$id;
        }



        /**
         * Устанавливаем для заказа в таблице Orders SQLLite id заказа в CMS
         *
         * @param int $id id локальной БД SQLLite
         * @param int $shopOrderID id заказа в CMS
         * @param string $paymentVariant  вариант оплаты в CMS
         * @param string $status статус заказа
         *
         * @return bool
         */
        public function setShopOrderID( $id, $paymentVariant, $status, $shopOrderID ){
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            return $orderDB->setShopOrderID($id, $paymentVariant, $status, $shopOrderID);
        }



        /**
         * Инициализирует массив заказов из массива id заказов локальной БД
         *
         * @param int $id идентификатор заказа
         *
         * @throws DDeliveryException
         *
         * @return DDeliveryOrder
         */
        public function initOrder( $id ){
            $id = (int)$id;
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            if(!$id)
                throw new DDeliveryException('Пустой массив для инициализации заказа');
            $order = $orderDB->getOrderById($id);
            if( count($order) ){
                $item = $order[0];
                $productList = unserialize( $item->cart );
                $currentOrder = new DDeliveryOrder( $productList );
                $this->_initOrderInfo( $currentOrder, $item );
            }else{
                throw new DDeliveryException('Заказ DD в локальной БД не найден');
            }

            return $currentOrder;
        }


        /**
         * Получить объект заказа
         * @var string $ip
         *
         * @return DDeliveryOrder;
         */
        public function getOrder( )
        {
            return $this->order;
        }

        /**
         * Проверяем на валидность $order для получение точек доставки
         *
         * @param DDeliveryOrder $order
         *
         * @return bool
         */
        public function _validateOrderToGetPoints( DDeliveryOrder $order )
        {
            if( count($order->getProducts()) > 0 && $order->city )
            {
                return true;
            }
            return false;
        }


        /**
         *
         * Здесь проверяется заполнение всех данных для заказа
         *
         * @param DDeliveryOrder $order заказ ddelivery
         * @throws DDeliveryException
         * @return bool
         */
        public function checkOrderCourierValues( $order ){

            $errors = array();
            $point = $order->getPoint();

            if( $point == null ){
                $errors[] = "Укажите пожалуйста точку";
            }
            if(!strlen( $order->getToName() ))
            {
                $errors[] = "Укажите пожалуйста ФИО";
            }
            if(!$this->isValidPhone( $order->toPhone ))
            {
                $errors[] = "Укажите пожалуйста телефон в верном формате";
            }
            if( $order->type != DDeliverySDK::TYPE_COURIER )
            {
                $errors[] = "Не верный тип доставки";
            }
            if( !strlen( $order->toStreet ) )
            {
                $errors[] = "Укажите пожалуйста улицу";
            }
            if(!strlen( $order->toHouse ))
            {
                $errors[] = "Укажите пожалуйста дом";
            }
            if(!$order->city)
            {
                $errors[] = "Город не определен";
            }
            if( !strlen( $order->toFlat ) )
            {
                $errors[] = "Укажите пожалуйста квартиру";
            }
            if(!empty($order->toEmail))
            {
                if(!$this->isValidEmail($order->toEmail))
                {
                    $errors[] = "Укажите пожалуйста email в верном формате";
                }
            }

            if( empty( $order->paymentVariant ) )
            {
                    $errors[] = "Не указан способ оплаты в CMS";
            }

            if( empty( $order->localStatus ) )
            {
                $errors[] = "Не указан статус заказа в CMS";
            }

            if( ! $order->shopRefnum )
            {
                $errors[] = "Не найден id заказа в CMS";
            }
            $enabled = $this->paymentPriceEnable($order);
            if( !$enabled ){
                if( (count($this->shop->getCourierPaymentVariants( $order ))  > 0) &&
                           in_array( $order->paymentVariant, $this->shop->getCourierPaymentVariants( $order ) ) ){
                    $errors[] = "Нет попадания в список возможных способов оплаты";
                }
            }
            if(count($errors))
            {
                throw new DDeliveryException(implode(', ', $errors));
            }
            return true;
        }

        /**
         *
         * Перед отправкой заказа самовывоза на сервер DDelivery проверяется
         * заполнение всех данных для заказа
         *
         * @param DDeliveryOrder $order заказ ddelivery
         *
         * @throws DDeliveryException
         * @return bool
         */

        public function checkOrderSelfValues( $order ){
            $errors = array();
            $point = $order->getPoint();

            if( $point == null ){
                $errors[] = "Укажите пожалуйста точку";
            }
            if(!strlen( $order->getToName() )){
                $errors[] = "Укажите пожалуйста ФИО";
            }
            if(!$this->isValidPhone( $order->toPhone )){
                $errors[] = "Укажите пожалуйста телефон в верном формате";
            }
            if( $order->type != DDeliverySDK::TYPE_SELF ){
                $errors[] = "Не верный тип доставки";
            }

            if( empty( $order->paymentVariant ) ){
                $errors[] = "Не указан способ оплаты в CMS";
            }

            if( empty( $order->localStatus ) ){
                $errors[] = "Не указан статус заказа в CMS";
            }

            if( ! $order->shopRefnum ){
                $errors[] = "Не найден id заказа в CMS";
            }
            $enabled = $this->paymentPriceEnable($order);
            if( !$enabled ){
                if( (count($this->shop->getSelfPaymentVariants( $order ))  > 0) &&
                         in_array( $order->paymentVariant, $this->shop->getSelfPaymentVariants( $order ) ) ){
                    $errors[] = "Нет попадания в список возможных способов оплаты";
                }
            }
            if(count($errors)){
                throw new DDeliveryException(implode(', ', $errors));
            }
            return true;
        }

        /**
         *
         * Проверка на доступность НПП
         *
         * @param DDeliveryOrder $order
         * @return bool
         *
         * @throws DDeliveryException
         */
        public function paymentPriceEnable( $order ){
            $city = $order->city;
            $company = $order->companyId;
            $enabled = $this->shop->getPaymentFilterEnabled( $order );
            if( $enabled ){
                if( !empty($city) && !empty($company) ){
                    $paymentPrice = $this->sdk->paymentPriceEnable( $city, $company );
                    return $paymentPrice->success;
                }else{
                    throw new DDeliveryException('Не хватает параметров для расчета НПП');
                }
            }else{
                return true;
            }
        }




        /**
         *
         * Сохранить в локальную БД заказ
         *
         * @param DDeliveryOrder $order заказ ddelivery
         *
         * @return int
         */
        public function saveFullOrder( DDeliveryOrder $order ){
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            $id = $orderDB->saveFullOrder( $order );
            return $id;
        }

        /**
         *
         * отправить заказ на курьерку
         *
         * @param DDeliveryOrder $order
         * @throws DDeliveryException
         * @return int
         */
        public function createCourierOrder( $order ){
            if(! $this->shop->sendOrderToDDeliveryServer($order) ){
                return 0;
            } else {

                $order->toPhone = $this->formatPhone( $order->toPhone );
                $cv = $this->checkOrderCourierValues( $order );
                if( !$cv )
                    return 0;

                $point = $order->getPoint();
                $to_city = $order->city;
                $delivery_company = $order->companyId;

                $dimensionSide1 = $order->getDimensionSide1();
                $dimensionSide2 = $order->getDimensionSide2();
                $dimensionSide3 = $order->getDimensionSide3();

                $goods_description = $order->getGoodsDescription();
                $weight = $order->getWeight();
                $confirmed = $this->shop->isConfirmedStatus($order->localStatus);

                $to_name = $order->getToName();
                $to_phone = $order->getToPhone();

                //$orderPrice = $point->getDeliveryInfo()->clientPrice;

                $declaredPrice = $this->shop->getDeclaredPrice( $order );
                $paymentPrice = $this->shop->getPaymentPriceCourier( $order, $this->getClientPrice($point, $order, DDeliverySDK::TYPE_COURIER) );

                $to_street = $order->toStreet;
                $to_house = $order->toHouse;
                $to_flat = $order->toFlat;
                $shop_refnum = $order->shopRefnum;
                $to_email = $order->toEmail;
                $metadata = $order->getJsonOrder();

                $to_index = $order->toIndex;


                $response = $this->sdk->addCourierOrder( $to_city, $delivery_company, $dimensionSide1, $dimensionSide2,
                                                             $dimensionSide3, $shop_refnum, $confirmed, $weight,
                                                             $to_name, $to_phone, $goods_description, $declaredPrice,
                                                             $paymentPrice, $to_street, $to_house, $to_flat, $to_email, $metadata, $to_index );
                if( !$response->response['order'] ){
                    throw new DDeliveryException("Ошибка отправки заказа на сервер DDelivery.ru");
                }
                $ddeliveryOrderID = $response->response['order'];

                $order->ddeliveryID = $ddeliveryOrderID;
                if( $confirmed ){
                    $order->ddStatus = DDStatusProvider::ORDER_CONFIRMED;
                }
                else{
                    $order->ddStatus = DDStatusProvider::ORDER_IN_PROGRESS;
                }
                $this->saveFullOrder( $order );
                return $ddeliveryOrderID;
            }

        }


        /**
         * Отправить заказ на самовывоз
         * @param DDeliveryOrder $order
         * @throws DDeliveryException
         * @return int
         */
        public function createSelfOrder( $order ){

            if(! $this->shop->sendOrderToDDeliveryServer($order) ){
                return 0;
            } else {

                $order->toPhone = $this->formatPhone( $order->toPhone );
                $cv = $this->checkOrderSelfValues( $order );
                if( !$cv )
                    return 0;

                $point = $order->getPoint();
                $pointID = $order->pointID;
                $dimensionSide1 = $order->getDimensionSide1();
                $dimensionSide2 = $order->getDimensionSide2();
                $dimensionSide3 = $order->getDimensionSide3();
                $goods_description = $order->getGoodsDescription();
                $weight = $order->getWeight();
                $confirmed = $this->shop->isConfirmedStatus($order->localStatus);
                $to_name = $order->getToName();
                $to_phone = $order->getToPhone();
                $declaredPrice = $this->shop->getDeclaredPrice( $order );
                $paymentPrice = $this->shop->getPaymentPriceSelf( $order, $this->getClientPrice($point, $order, DDeliverySDK::TYPE_SELF ) );
                $shop_refnum = $order->shopRefnum;

                $to_email = $order->toEmail;
                $metadata = $order->getJsonOrder();

                $response = $this->sdk->addSelfOrder( $pointID, $dimensionSide1, $dimensionSide2,
                                                      $dimensionSide3, $confirmed, $weight, $to_name,
                                                      $to_phone, $goods_description, $declaredPrice,
                                                      $paymentPrice, $shop_refnum, $to_email, $metadata );

                if( !$response->response['order'] ){
                    throw new DDeliveryException("Ошибка отправки заказа на сервер DDelivery.ru");
                }

                $ddeliveryOrderID = $response->response['order'];

                $order->ddeliveryID = $ddeliveryOrderID;
                if( $confirmed ){
                    $order->ddStatus = DDStatusProvider::ORDER_CONFIRMED;
                }
                else{
                    $order->ddStatus = DDStatusProvider::ORDER_IN_PROGRESS;
                }
                $this->saveFullOrder( $order );
                return $ddeliveryOrderID;
            }


        }
        /**
         * Весь список заказов
         *
         */
        public function getAllOrders()
        {
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            return $orderDB->selectAll();
        }

        /**
         * Проверяем правильность Email
         *
         * @param string $email
         *
         * @return boolean
         */
        public function isValidEmail( $email )
        {
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                return true;
            }
            return false;
        }

        /**
         * Вырезаем из номера телефона ненужные символы
         *
         * @param string $phone
         *
         * @return string
         */
        public function formatPhone( $phone )
        {
            return preg_replace( array('/-/', '/\(/', '/\)/', '/\+7/', '/\s\s+/'), '', $phone );
        }

        /**
         * Проверяем правильность телефона
         *
         * @param string $phone
         *
         * @return boolean
         */
        public function isValidPhone( $phone )
        {
            if( preg_match('/^[0-9]{10}$/', $phone) )
            {
                return true;
            }
            return false;
        }

        /**
         * Назначить точку доставки
         *
         */
        public function setOrderPoint( $point )
        {
            $this->order->setPoint( $point );
        }

        /**
         * Назначить номер телефона доставки
         *
         */
        public function setOrderToPhone( $phone )
        {
            $this->order->toPhone = trim( strip_tags( $phone ) );
        }

        /**
         * Назначить ФИО доставки
         *
         */
        public function setOrderToName( $name )
        {
            $this->order->toName = trim( strip_tags( $name ) );
        }

        /**
         * Назначить квартиру доставки
         *
         */
        public function setOrderToFlat( $flat )
        {
            $this->order->toFlat = trim( strip_tags( $flat ) );
        }

        /**
         * Назначить дом для доставки
         *
         */
        public function setOrderToHouse( $house )
        {
            $this->order->toHouse = trim( strip_tags( $house ) );
        }

        /**
         * Назначить email для доставки
         *
         */
        public function setOrderToEmail( $email ){
            $this->order->toEmail = trim( strip_tags( $email ) );
        }



        /**
         *
         * Получить реальную цену доставки без скидок и т.д
         *
         * @param $companyArray
         * @return mixed
         */
        public function getCompanyPrice( $companyArray ){
            $pickup = $this->shop->isPayPickup();
            if( $pickup ){
               $price = $companyArray['total_price'];
            }else{
               $price = $companyArray['delivery_price'];
            }
            return $price;
        }

        /**
         *
         * Получить цену для клиента для массива инфы из калькулятора учитывая
         * настройку забора, проверка вхождения интервалов, + ручная обработка цены из адаптера
         *
         * @param $companyArray
         * @param $order DDeliveryOrder
         * @param $orderType int
         *
         * @return mixed
         */
        public function getClientPrice( $companyArray, $order, $orderType = DDeliverySDK::TYPE_SELF ){
            $pickup = $this->shop->isPayPickup();
            if( $pickup ){
                $price = $companyArray['total_price'];
            }else{
                $price = $companyArray['delivery_price'];
            }
            // интервалы
            $price = $this->shop->preDisplayPointCalc($price, $order->getAmount());
            // Ручное редактирование
            $price = $this->shop->processClientPrice( $order, $price, $orderType, $companyArray );

            return $price;
        }

        /**
         *
         * Калькулятор цены для самовывоза учитывая настройки фильтрации компанийдля города
         *
         * @param DDeliveryOrder $order
         * @return array|bool|mixed
         * @throws DDeliveryException
         */
        public function calculateSelfPrices( DDeliveryOrder $order ){
            if ( ( $order->city > 0) && count( $order->getProducts() ) ){
                $resultCompanies = array();

                // Необходимость ходить за точками на сервер
                if( $this->shop->preGoToFindPoints( $order ) ){
                    $declared_price = (int)$this->shop->getDeclaredPrice($order);
                    $params = array(
                        $order->city, $order->dimensionSide1, $order->dimensionSide2,
                        $order->dimensionSide3, $order->getWeight(), $declared_price
                    );
                    $response = $this->sdk->calculatorPickupForCity( $params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
                    $allowedCompanies = $this->shop->filterCompanyPointSelf();

                    // Фильтруем по настройкам цмс
                    if( count( $response->response ) ){
                        for( $i = 0; $i < count($response->response); $i++ ){
                            if( in_array( $response->response[$i]['delivery_company'], $allowedCompanies) ){
                                $resultCompanies[ $response->response[$i]['delivery_company'] ] = $response->response[$i];
                            }
                        }
                    }
                }
                // Фильтруем по своим правилам
                $resultCompanies = $this->shop->finalFilterSelfCompanies( $resultCompanies, $order );
                $resultCompanies = $this->sortCompanies( $resultCompanies );
                return $resultCompanies;
            }else{
                throw new DDeliveryException('Недостаточно параметров для расчета цены');
            }
        }

        /**
         * Калькулятор цены для самовывоза для точки
         *
         * @param DDeliveryOrder $order
         * @param $pointId
         * @return array|mixed
         */
        public function calculateSelfPointPrice( DDeliveryOrder $order, $pointId ){
            if ( ( $pointId > 0) && count( $order->getProducts() ) ){
                $resultPoint = array();
                if( $this->shop->preGoToFindPoints( $order, $pointId ) ){
                    $declared_price = (int) $this->shop->getDeclaredPrice($order);
                    $params = array(
                        $pointId, $order->dimensionSide1, $order->dimensionSide2,
                        $order->dimensionSide3, $order->getWeight(), $declared_price
                    );
                    $response = $this->sdk->calculatorPickupForPoint( $params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
                    $resultPoint = $response->response;
                }
                $resultPoint = $this->shop->finalFilterSelfCompanies( $resultPoint, $order );
                return $resultPoint;
            }
            return null;
        }

        /**
         *
         * Калькулятор цены для курьерской доставки
         *
         * @param DDeliveryOrder $order
         * @return array|bool|mixed
         * @throws DDeliveryException
         */
        public function calculateCourierPrices( DDeliveryOrder $order ){
            if ( ( $order->city > 0) && count( $order->getProducts() ) ){
                $resultCompanies = array();

                // Необходимость ходить за точками на сервер
                if( $this->shop->preGoToFindPoints( $order ) ){
                    $declared_price = (int) $this->shop->getDeclaredPrice($order);
                    $params = array(
                        $order->city, $order->dimensionSide1, $order->dimensionSide2,
                        $order->dimensionSide3, $order->getWeight(), $declared_price
                    );
                    $response = $this->sdk->calculatorCourier( $params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
                    $allowedCompanies = $this->shop->filterCompanyPointCourier();

                    // Фильтруем по настройкам цмс
                    if( count( $response->response ) ){
                        for( $i = 0; $i < count($response->response); $i++ ){
                            if( in_array( $response->response[$i]['delivery_company'], $allowedCompanies) ){
                                $resultCompanies[ $response->response[$i]['delivery_company'] ] = $response->response[$i];
                            }
                        }
                    }
                }
                // Фильтруем по своим правилам, добавляем точки, не добавляем точки, убираем
                $resultCompanies = $this->shop->finalFilterCourierCompanies( $resultCompanies, $order );
                $resultCompanies = $this->sortCompanies( $resultCompanies );
                return $resultCompanies;
            }else{
                throw new DDeliveryException('Недостаточно параметров для расчета цены');
                return false;
            }
        }

        /**
         *
         * Кеширующий вызов калькулятора цены для курьерки
         *
         * @param $order
         * @return array|bool|mixed
         */
        public function cachedCalculateCourierPrices( $order ){
            $sig = md5( $order->city . $order->goodsDescription );
            $courierCompanyList = $this->order->getCacheValue('calculateCourier', $sig);
            if( !$courierCompanyList ){
                $courierCompanyList = $this->calculateCourierPrices( $this->order );
                $this->order->setCacheValue('calculateCourier', $sig, $courierCompanyList);
            }
            return $courierCompanyList;
        }

        /**
         *
         * Кеширующий вызов калькулятора цены для самовывоза
         *
         * @param $order
         * @return array|bool|mixed
         */
        public function cachedCalculateSelfPrices( $order ){
            $sig = md5( $order->city . $order->goodsDescription );
            $selfCompanies = $order->getCacheValue('calculateSelf', $sig);
            if( !$selfCompanies ){
                $selfCompanies = $this->calculateSelfPrices($order);
                $this->order->setCacheValue('calculateSelf', $sig, $selfCompanies);
            }
            return $selfCompanies;
        }

        /**
         *
         * Сортировка компаний после калькулятора
         *
         * @param $resultCompanies
         * @return mixed
         */
        public function sortCompanies( $resultCompanies ){
            // Признак забора
            $pickup = $this->shop->isPayPickup();
            $sortElement = ( ( $pickup )?'total_price':'delivery_price' );
            if( $sortElement == 'delivery_price' ){
                usort($resultCompanies, function($a, $b){
                    if ($a['delivery_price'] == $b['delivery_price']) {
                        return 0;
                    }
                    return ($a['delivery_price'] < $b['delivery_price']) ? -1 : 1;
                });
            }else{
                usort($resultCompanies, function($a, $b){
                    if ($a['total_price'] == $b['total_price']) {
                        return 0;
                    }
                    return ($a['total_price'] < $b['total_price']) ? -1 : 1;
                });
            }
            return $resultCompanies;
        }

        /**
         *
         * Получить список инфы про точки и закешировать их
         *
         * @param DDeliveryOrder $order
         * @param $resultCompanies
         * @return array
         */
        public function getSelfPointsList( DDeliveryOrder $order, $resultCompanies ){

            $filterCompany = implode(',', $this->shop->filterCompanyPointSelf() );

            $companiesIdsArray = array();

            if( count( $resultCompanies ) > 0 ){
                foreach( $resultCompanies as $item ){

                    if( !empty( $item['delivery_company'] ) ){
                        $companiesIdsArray[] = $item['delivery_company'];
                    }
                }
            }

            if( $this->shop->getCachingFormat() == DShopAdapter::CACHING_TYPE_INDIVIDUAL ){
                $pointsInfo = $this->cache->get( $order->city, $filterCompany );
                if( !count($pointsInfo) ){

                    $pointsResponse = $this->sdk->getSelfDeliveryPoints( $filterCompany, $order->city );
                    if( count($pointsResponse->response) ){
                        $pointsInfo = $pointsResponse->response;
                        $this->cache->set($order->city, $pointsInfo, implode(',', $this->shop->filterCompanyPointSelf()) );
                    }else{
                        $pointsInfo = array();
                    }
                }
            }else if( $this->shop->getCachingFormat() == DShopAdapter::CACHING_TYPE_CENTRAL ){
                $pointsInfo = $this->cache->get( $order->city );
                if( !count($pointsInfo) ){
                    $pointsResponse = $this->sdk->getSelfDeliveryPoints('', $order->city );
                    if( count($pointsResponse->response) ){
                        $pointsInfo = $pointsResponse->response;
                        $this->cache->set($order->city, $pointsInfo, '' );
                    }else{
                        $pointsInfo = array();
                    }
                }
            }

            $resultPoints = array();
            // Фильтруем инфу согласно полученными компаниям в калькуляторе
            foreach ($pointsInfo as $key => $item){
                $company_id = $item['company_id'];
                if( (!in_array($company_id, $companiesIdsArray))){
                    unset($pointsInfo[$key]);
                }else{
                    $resultPoints[] = $pointsInfo[$key];
                }
            }

            $resultPoints = $this->shop->prePointListReturn( $resultPoints, $order, $resultCompanies );

            return $resultPoints;
        }

        /**
         * Получить доступные способы оплаты для объекта заказа
         *
         * @param DDeliveryOrder $order
         * @return array
         * @throws DDeliveryException
         */
        public  function getAvailablePaymentVariants( $order ){
            $enabled = $this->paymentPriceEnable($order);
            if($enabled){
                return array();
            }else{
                if( $order->type == DDeliverySDK::TYPE_SELF ){
                    return $this->shop->getSelfPaymentVariants( $order );
                }else if( $order->type == DDeliverySDK::TYPE_COURIER ){
                    return $this->shop->getCourierPaymentVariants( $order );
                }else{
                    throw new DDeliveryException("Не определен способ доставки");
                }
            }
        }


        /**
         *
         * Найти точку из списка по iD, бинарный поиск
         *
         * @param $pointInfoArray
         * @param $pointID
         * @return bool|int
         * @throws DDeliveryException
         */
        public function findPointIdInArray( &$pointInfoArray, $pointID ){
            $arrayLength = count($pointInfoArray);
            /* Проверка на пустой массив или позицию за пределами массива */
            if (!$arrayLength ||
                $pointID < $pointInfoArray[0]['_id'] ||
                $pointID > $pointInfoArray[$arrayLength-1]['_id']
            )
            {
                throw new DDeliveryException("Проблемы с поиском точки на карте");
                return false;
            }
            $leftPosition = 0;
            $rightPosition = $arrayLength - 1;
            $returnPosition = false;
            $i = 0;
            while ( $leftPosition < $rightPosition ) {
                $middlePosition = (int)floor($leftPosition + ($rightPosition - $leftPosition) / 2);
                if ( $pointID <= $pointInfoArray[$middlePosition]['_id'] )  {
                    $rightPosition = $middlePosition;
                } else {
                    $leftPosition = $middlePosition + 1;
                }
                $i++;
            }
            if ( $pointInfoArray[$rightPosition]['_id'] === $pointID ) {
                $returnPosition = $rightPosition;
            }else{
                throw new DDeliveryException("Проблемы с сортироовкой точек при получении информации");
                return;
            }

            return $returnPosition;
        }

        /**
         * Получить цену на заказа для клиента из объекта типа DDeliveryOrder
         *
         * @param DDeliveryOrder $order
         * @return bool|mixed
         */
        public function getOrderClientDeliveryPrice( DDeliveryOrder $order ){
            $point = $order->getPoint();
            if( is_array($point) ){
                return $this->getClientPrice( $point, $order, $order->type );
            }else{
                return false;
            }
        }

        /**
         * Получить реальную цену доставки без скидок и т.д из объекта типа DDeliveryOrder
         *
         * @param DDeliveryOrder $order
         * @return bool
         */
        public function getOrderRealDeliveryPrice( DDeliveryOrder $order ){
            $point = $order->getPoint();
            if( is_array($point) ){
                return $this->getCompanyPrice( $point );
            }else{
                return false;
            }
        }

        /**
         * Вызывается для рендера текущей странички
         * @param array $request
         * @throws DDeliveryException
         */
        public function render($request)
        {
            if(isset($request['iframe'])) {
                $staticURL = $this->shop->getStaticPath();
                $styleUrl = $this->shop->getStaticPath() . 'tems/' . $this->shop->getTemplate() . '/';
                $scriptURL = $this->shop->getPhpScriptURL();
                $version = DShopAdapter::SDK_VERSION;
                $captions = $this->shop->getCaptions();
                include(__DIR__ . '/../../templates/iframe.php');
                return;
            }

            if( isset($request['dd_plugin']) ){
                $this->renderPlugin($request);
                return;
            }

            if(!empty($request['order_id'])) {
                $order =  $this->initOrder( $request['order_id'] );
                $this->order = $order;
            }


            // если пустой город и нет его в реквесте, пытаемся определить его самостоятельно
            if(!$this->order->city && !isset($request['city_id']) ) {
                $cityId = $this->shop->getClientCityId();
                $cityData = $this->cityLocator->getCity($cityId);
                $this->order->city = $cityData['_id'];
                $this->order->cityName = $cityData['display_name'];
            }

            if( isset($request['city_id']) && ( $this->order->city != $request['city_id'] ) ){
                $cityData = $this->cityLocator->getCity($request['city_id']);
                $this->order->city = $cityData['_id'];
                $this->order->cityName = $cityData['display_name'];
            }

            if( !$this->order->localId ){
                $this->order->localId = $this->saveFullOrder($this->order);
            }

            if($this->order->city && !$this->order->cityName) {
                $cityData = $this->cityLocator->getCity($this->order->city);
                $this->order->cityName = $cityData['display_name'];
            }



            if(isset($request['action'])) {
                switch($request['action']) {

                    case 'searchCity':
                    case 'searchCityMap':
                        if(isset($request['name']) && mb_strlen($request['name']) >= 3){

                            $cityList = $this->cityLocator->getAutoCompleteCity( $request['name'] );
                            $cityId = $this->order->city;

                            $displayData = array();
                            $content = '';
                            if($request['action'] == 'searchCity'){
                                ob_start();
                                include(__DIR__ . '/../../templates/cityHelper.php');
                                $content = ob_get_contents();
                                ob_end_clean();
                            }else{ // searchCityMap
                                foreach($cityList as $cityData){
                                    $displayDataCur = array(
                                        'id'=>$cityData['_id'],
                                        'name'=>$cityData['type'].'. '.$cityData['name'],
                                    );

                                    if($cityData['name'] != $cityData['region']) {
                                        $displayDataCur['name'] .= ', '.$cityData['region'].' обл.';
                                    }
                                    $displayData[] = $displayDataCur;
                                }
                            }

                            echo json_encode(array(
                                'html'=>$content,
                                'displayData'=>$displayData,
                                'request'=>array(
                                    'name'=>$request['name'],
                                    'action'=>'searchCity'
                                )
                            ));
                        }
                        return;
                    case 'mapGetPoint':
                        if(!empty($request['id'])) {

                            $pointSelf = $this->calculateSelfPointPrice( $this->order, (int)$request['id'] );
                            $pointInfo = $this->getSelfPointsList($this->order, $pointSelf);



                            if(empty($pointSelf) || empty($pointInfo)) {
                                echo json_encode(array('point'=>array()));
                                return;
                            }

                            $point = $this->findPointIdInArray( $pointInfo, (int)$request['id'] );

                            echo json_encode(array(
                                'point'=>array(
                                    'description_in' => $pointInfo[$point]['description_in'],
                                    'description_out' => $pointInfo[$point]['description_out'],
                                    'indoor_place' => $pointInfo[$point]['indoor_place'],
                                    'metro' => trim($pointInfo[$point]['metro']),
                                    'schedule' => $pointInfo[$point]['schedule'],
                                    'total_price' => $this->getClientPrice( $pointSelf[0], $this->order ),
                                    'delivery_time_min' => $pointSelf[0]['delivery_time_min'],
                                    'delivery_time_min_str' => Utils::plural($pointSelf[0]['delivery_time_min'], 'дня', 'дней', 'дней', 'дней', false),
                                ),
                            ));
                        }
                        return;
                }
            }




            if(!empty($request['point']) && isset($request['type'])) {
                if ( $request['type'] == DDeliverySDK::TYPE_SELF ) {

                    // set point calculation
                    $this->order->pointID = (int) $request['point'];
                    // Получаем список компаний с ценами из кеша
                    $sig = md5( $this->order->city . $this->order->goodsDescription );
                    $selfCompany = $this->order->getCacheValue('calculateSelfPoint', $sig);
                    if( !$selfCompany ){
                        $selfCompany = $this->calculateSelfPointPrice($this->order, $this->order->pointID);
                        $this->order->setCacheValue('calculateSelfPoint', $sig, $selfCompany);
                    }
                    // Получаем список информации про компании из кеша

                    $pointInfoArray = $this->getSelfPointsList( $this->order, $selfCompany );
                    $pointId = $this->findPointIdInArray( $pointInfoArray, $this->order->pointID);

                    if( $pointInfoArray[$pointId]['company_id'] ){
                        $pointArray = array_merge( $selfCompany[0], $pointInfoArray[$pointId] );
                    }
                    $this->order->setPoint( $pointArray );
                    $this->order->companyId = $pointArray['delivery_company'];

                }elseif($request['type'] == DDeliverySDK::TYPE_COURIER){
                    $this->order->pointID = (int) $request['point'];
                    $courierCompanyList = $this->cachedCalculateCourierPrices( $this->order );

                    if( count( $courierCompanyList ) ){
                        foreach ( $courierCompanyList as $item ){
                            if( $item['delivery_company'] == $this->order->pointID ){
                                $pointArray = $item;
                                break;
                            }
                        }
                    }
                    $this->order->setPoint( $pointArray );
                    $this->order->companyId = $pointArray['delivery_company'];
                }
            }
            if(!empty($request['contact_form']) && is_array($request['contact_form'])) {
                if(!empty($request['contact_form'])) {
                    foreach($request['contact_form'] as $row) {
                        switch($row['name']){
                            case 'email':
                                $this->order->toEmail = $row['value'];
                                break;
                            case 'second_name':
                                $this->order->secondName = $row['value'];
                                break;
                            case 'first_name':
                                $this->order->firstName = $row['value'];
                                break;
                            case 'phone':
                                $this->order->toPhone = $row['value'];
                                break;
                            case 'address':
                                $this->order->toStreet = $row['value'];
                                break;
                            case 'address_house':
                                $this->order->toHouse = $row['value'];
                                break;
                            case 'address_housing':
                                $this->order->toHousing = $row['value'];
                                break;
                            case 'address_flat':
                                $this->order->toFlat = $row['value'];
                                break;
                            case 'index':
                                $this->order->toIndex = $row['value'];
                                break;
                            case 'comment':
                                //@todo Комента нет
                                $this->order->comment = $row['value'];
                                break;
                        }
                    }
                }
            }

            $supportedTypes = $this->shop->getSupportedType();

            if(!is_array($supportedTypes))
                $supportedTypes = array($supportedTypes);

            $this->supportedTypes = $supportedTypes;

            if(empty($request['action'])) {
                $deliveryType = (int) (isset($request['type']) ? $request['type'] : 0);
                // Проверяем поддерживаем ли мы этот тип доставки
                if($deliveryType && !in_array($deliveryType, $supportedTypes)) {
                    $deliveryType = 0;
                }

                // Неизвестно какой экшен, выбираем
                if(count($supportedTypes) > 1 && !$deliveryType) {
                    $request['action'] = 'typeForm';
                }else{
                    if(!$deliveryType)
                        $deliveryType = reset($supportedTypes);
                    $this->deliveryType = $deliveryType;

                    if($deliveryType == DDeliverySDK::TYPE_SELF){
                        $request['action'] = 'map';
                    }elseif($deliveryType == DDeliverySDK::TYPE_COURIER){
                        $request['action'] = 'courier';
                    }else{
                        throw new DDeliveryException('Not support delivery type');
                    }
                }
            }

            switch($request['action']) {
                case 'map':
                    $this->order->type = DDeliverySDK::TYPE_SELF;
                    echo $this->renderMap();
                    break;
                case 'mapDataOnly':
                    $this->order->type = DDeliverySDK::TYPE_SELF;
                    echo $this->renderMap(true);
                    break;
                case 'courier':
                    $this->order->type = DDeliverySDK::TYPE_COURIER;
                    echo $this->renderCourier();
                    break;
                case 'typeForm':
                    echo $this->renderDeliveryTypeForm();
                    break;
                case 'typeFormDataOnly':
                    echo $this->renderDeliveryTypeForm(true);
                    break;
                case 'contactForm':
                    echo $this->renderContactForm();
                    break;
                case 'change':
                    echo $this->renderChange();
                    break;
                default:
                    throw new DDeliveryException('Not support action');
                    break;
            }
            $this->order->localId = $this->saveFullOrder($this->order);
        }

        /**
         * Вызывается для использования api методов сервера ddelivery.ru
         * @param array $request
         */
        public function renderPlugin($request){
            if(isset($request['action'])) {
                switch($request['action']) {
                    case 'getCityIp':
                        $cityList = $this->sdk->getCityByIp( $_SERVER['REMOTE_ADDR'] );
                        if( count($cityList->response)){
                            $cityList->response['city'] = Utils::firstWordLiterUppercase( $cityList->response['city'] );
                            echo json_encode( $cityList->response );
                        }else{
                            echo json_encode(array());
                        }
                        return;
                    case 'searchCity2':
                        if(isset($request['name']) && mb_strlen($request['name']) >= 3){
                            $cityList = $this->sdk->getAutoCompleteCity($request['name']);
                            $cityList = $cityList->response;
                            $resultCity = array();
                            if( count( $cityList ) ){
                                foreach($cityList as $key => $city){
                                    $resultCity[$key]['label'] = Utils::firstWordLiterUppercase($city['name']);
                                    if($cityList[$key]['region'] != $cityList[$key]['label']) {
                                        $resultCity[$key]['label'] .= ', '.$cityList[$key]['region'].' обл.';
                                    }
                                    $resultCity[$key]['id'] = $city['_id'];
                                }
                            }

                            echo json_encode($resultCity);

                        }
                        return;
                    case 'getCompanies':
                        $dd_to = $request['ddcalc_to'];
                        $dd_payment = $request['ddcalc_payment'];
                        $dd_weight = $request['ddcalc_weight'];
                        $dd_width = $request['ddcalc_width'];
                        $dd_height = $request['ddcalc_height'];
                        $dd_length = $request['ddcalc_length'];

                        $pickup = $this->sdk->calculatorPickupForCity( $dd_to, $dd_width, $dd_height, $dd_length, $dd_weight, $dd_payment );
                        $courier = $this->sdk->calculatorCourier( $dd_to, $dd_width, $dd_height, $dd_length, $dd_weight, $dd_payment );
                        echo json_encode(
                            array(
                                'pickup' => $pickup->response,
                                'courier' => $courier->response
                            )
                        );
                        return;
                }
            }
        }


        private function renderChange()
        {
            $comment = '';
            $point = $this->order->getPoint();

            $comment = $this->getPointComment($this->order);

            $this->shop->onFinishChange( $this->order );

            $returnArray = array(
                            'html'=>'',
                            'js'=>'change',
                            'comment'=>htmlspecialchars($comment),
                            'orderId' => $this->order->localId,
                            'clientPrice'=>$this->getClientPrice($point, $this->order, $this->order->type),
                            'userInfo' => $this->getDDUserInfo($this->order),
                            'payment'  => $this->getAvailablePaymentVariants($this->order)
                            );
            $returnArray = $this->shop->onFinishResultReturn( $this->order, $returnArray );
            return json_encode( $returnArray );
        }




        /**
         * Страница с картой
         *
         * @param bool $dataOnly ajax
         * @return string
         */

        protected function renderMap($dataOnly = false){
            $cityId = $this->order->city;
            $staticURL = $this->shop->getStaticPath();
            $styleUrl = $this->shop->getStaticPath() . 'tems/' . $this->shop->getTemplate() . '/';

            $selfCompanyList = $this->cachedCalculateSelfPrices( $this->order );
            $pointsJs = array();
            if(count( $selfCompanyList )){
                $pointsJs = $this->getSelfPointsList( $this->order, $selfCompanyList );
            }
            if($dataOnly) {
                ob_start();
                include(__DIR__ . '/../../templates/mapCompanyHelper.php');
                $content = ob_get_contents();
                ob_end_clean();
                $dataFromHeader = $this->getDataFromHeader();

                return json_encode(array('html'=>$content, 'points' => $pointsJs, 'orderId' => $this->order->localId, 'headerData' => $dataFromHeader));
            } else {
                $cityList = $this->cityLocator->getCityByDisplay($this->order->city, $this->order->cityName);
                $headerData = $this->getDataFromHeader();
                ob_start();
                include(__DIR__ . '/../../templates/map.php');
                $content = ob_get_contents();
                ob_end_clean();
                return json_encode(array('html'=>$content, 'js'=>'map', 'points' => $pointsJs, 'orderId' => $this->order->localId, 'type'=>DDeliverySDK::TYPE_SELF));
            }
        }

        protected function getDataFromHeader(){
            $data = array(
                'self' => array(
                    'minPrice' => 0,
                    'minTime' => 0,
                    'timeStr' => '',
                    'disabled' => true,
                ),
                'courier' => array(
                    'minPrice' => 0,
                    'minTime' => 0,
                    'timeStr' => '',
                    'disabled' => true
                ),
            );


            if(in_array(Sdk\DDeliverySDK::TYPE_SELF, $this->supportedTypes)) {
                $selfCompanies = $this->cachedCalculateSelfPrices( $this->order );
                if(count( $selfCompanies )){

                    $minPrice = $this->getClientPrice( reset($selfCompanies), $this->order, DDeliverySDK::TYPE_SELF  );
                    $minTime = PHP_INT_MAX;
                    foreach( $selfCompanies as $selfCompany ) {
                        if($minTime > $selfCompany['delivery_time_min']){
                            $minTime = $selfCompany['delivery_time_min'];
                        }
                    }
                    $data['self'] = array(
                        'minPrice' => $minPrice,
                        'minTime' => $minTime,
                        'timeStr' => Utils::plural($minTime, 'дня', 'дней', 'дней', 'дней', false),
                        'disabled' => false
                    );
                }

            }
            if(in_array(Sdk\DDeliverySDK::TYPE_COURIER, $this->supportedTypes)) {
                $courierCompanies = $this->cachedCalculateCourierPrices( $this->order );

                if(count( $courierCompanies )){
                    $minPrice = $this->getClientPrice( reset($courierCompanies), $this->order, DDeliverySDK::TYPE_COURIER  );
                    $minTime = PHP_INT_MAX;
                    foreach( $courierCompanies as $courierCompany ) {
                        if($minTime > $courierCompany['delivery_time_min']){
                            $minTime = $courierCompany['delivery_time_min'];
                        }
                    }
                    $data['courier'] = array(
                        'minPrice' => $minPrice,
                        'minTime' => $minTime,
                        'timeStr' => Utils::plural($minTime, 'дня', 'дней', 'дней', 'дней', false),
                        'disabled' => false
                    );
                }

            }
            return $data;
        }

        /**
         * Возвращает страницу с формой выбора способа доставки
         * @param bool $dataOnly если передать true, то отдаст данные для обновления верстки через js
         * @return string
         */
        protected function renderDeliveryTypeForm( $dataOnly = false ){
            $staticURL = $this->shop->getStaticPath();
            $styleUrl = $this->shop->getStaticPath() . 'tems/' . $this->shop->getTemplate() . '/';
            $cityId = $this->order->city;

            $order = $this->order;
            $order->declaredPrice = $this->shop->getDeclaredPrice($order);
            $order->city = $cityId;

            $data = $this->getDataFromHeader();

            if(!$dataOnly) {
                // Рендер html
                $cityList = $this->cityLocator->getCityByDisplay($this->order->city, $this->order->cityName);

                ob_start();
                include(__DIR__.'/../../templates/typeForm.php');
                $content = ob_get_contents();
                ob_end_clean();

                return json_encode(array('html'=>$content, 'js'=>'typeForm', 'orderId' => $this->order->localId, 'typeData' => $data));
            }else{
                return json_encode(array('typeData' => $data));
            }
        }

        /**
         * @return string
         */
        protected function renderCourier(){
            $cityId = $this->order->city;
            $cityList = $this->cityLocator->getCityByDisplay($this->order->city, $this->order->cityName);
            $companies = $this->getCompanySubInfo();
            $staticURL = $this->shop->getStaticPath();
            $styleUrl = $this->shop->getStaticPath() . 'tems/' . $this->shop->getTemplate() . '/';
            $courierCompanyList = $this->cachedCalculateCourierPrices( $this->order );
            // Ресетаем ключи.
            $headerData = $this->getDataFromHeader();

            ob_start();
            include(__DIR__.'/../../templates/couriers.php');
            $content = ob_get_contents();
            ob_end_clean();

            return json_encode(array('html'=>$content, 'js'=>'courier', 'orderId' => $this->order->localId,
                'type'=>DDeliverySDK::TYPE_COURIER, 'typeData' => $headerData));
        }

        /**
         * @return string
         */
        private function renderContactForm(){
            $point = $this->getOrder()->getPoint();
            if(!$point){
                return '';
            }

            $displayCityName = $this->order->cityName;
            $type = $this->getOrder()->type;
            if($this->getOrder()->type == DDeliverySDK::TYPE_COURIER) {
                $displayCityName.=', '.$point['delivery_company_name'];
                $requiredFieldMask = $this->shop->getCourierRequiredFields();
            }elseif($this->getOrder()->type == DDeliverySDK::TYPE_SELF) {
                $displayCityName.=' '. $point['address'];
                $requiredFieldMask = $this->shop->getSelfRequiredFields();
            }else{
                return '';
            }
            $deliveryType = $this->getOrder()->type;

            $order = $this->order;
            $order->declaredPrice = $this->shop->getDeclaredPrice($order);

            $fieldValue = $order->firstName;
            if(!$fieldValue)
                $order->firstName = $this->shop->getClientFirstName();


            $fieldValue = $order->secondName;
            if(!$fieldValue)
                $order->secondName = $this->shop->getClientLastName();

            $fieldValue = $order->getToPhone();
            if(!$fieldValue)
                $order->setToPhone($this->shop->getClientPhone());

            if(!$order->getToIndex())
                $order->toIndex = $this->shop->getClientZipCode();


            $fieldValue = $order->getToStreet();
            if(!$fieldValue){
                $address = $this->shop->getClientAddress();
                if(!is_array($address))
                    $address = array($address);
                if(isset($address[0]))
                    $order->setToStreet($address[0]);
                if(isset($address[1]))
                    $order->setToHouse($address[1]);
                if(isset($address[2]))
                    $order->setToHousing($address[2]);
                if(isset($address[3]))
                    $order->setToFlat($address[3]);
            }

            $fieldValue = $order->toEmail;
            if(!$fieldValue){
                $order->toEmail = $this->shop->getClientEmail();
            }

            if($requiredFieldMask == 0){
                return $this->renderChange();
            }

            ob_start();
            include(__DIR__.'/../../templates/contactForm.php');
            $content = ob_get_contents();
            ob_end_clean();
            $content = str_replace('<input', '<inp!KasperskyHack!ut', $content);
            $html = json_encode(array('html'=>$content, 'js'=>'contactForm', 'orderId' => $this->order->localId, 'type'=>DDeliverySDK::TYPE_COURIER));
            return $html;
        }

        /**
         * Возвращает дополнительную информацию по компаниям доставки
         * @return array
         */
        static public function getCompanySubInfo(){
            // pack забита для тех у кого нет иконки
            return Utils::getCompanySubInfo();
        }




        /**
         *
         * Инициализирует свойства объекта DDeliveryOrder из stdClass полученный из
         * запроса БД SQLite
         *
         * @param DDeliveryOrder $currentOrder
         * @param \stdClass $item
         */
        public function _initOrderInfo($currentOrder, $item){
            $currentOrder->type = $item->type;
            $currentOrder->paymentVariant = $item->payment_variant;
            $currentOrder->localId = $item->id;
            $currentOrder->city = $item->to_city;
            $currentOrder->localStatus = $item->local_status;
            $currentOrder->ddStatus = $item->dd_status;
            $currentOrder->shopRefnum = $item->shop_refnum;
            $currentOrder->ddeliveryID = $item->ddeliveryorder_id;
            $currentOrder->pointID = $item->point_id;
            $currentOrder->companyId = $item->delivery_company;

            $currentOrder->amount = $currentOrder->getAmount();

            $currentOrder->orderCache = unserialize( $item->cache );
            $currentOrder->setPoint( json_decode( $item->point, true ) );

            $currentOrder->addField1 = $item->add_field1;
            $currentOrder->addField2 = $item->add_field2;
            $currentOrder->addField3 = $item->add_field3;

            $orderInfo = json_decode( $item->order_info, true );

            $currentOrder->confirmed = $orderInfo['confirmed'];
            $currentOrder->firstName = $orderInfo['firstName'];
            $currentOrder->secondName = $orderInfo['secondName'];
            $currentOrder->toPhone = $orderInfo['to_phone'];
            $currentOrder->declaredPrice = $orderInfo['declaredPrice'];
            $currentOrder->paymentPrice = $orderInfo['paymentPrice'];
            $currentOrder->toStreet = $orderInfo['toStreet'];
            $currentOrder->toHouse = $orderInfo['toHouse'];
            $currentOrder->toFlat = $orderInfo['toFlat'];
            $currentOrder->comment = $orderInfo['comment'];
            $currentOrder->cityName = $orderInfo['city_name'];
            $currentOrder->toHousing = $orderInfo['toHousing'];
            $currentOrder->toEmail = $orderInfo['toEmail'];
            $currentOrder->toIndex = $orderInfo['toIndex'];
        }

        /**
         * Удалить все заказы
         * @return bool
         */
        public function deleteAllOrders(){
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            return $orderDB->cleanOrders();
        }

        /**
         * Получить описание статуса на DDelivery
         *
         * @param $ddStatus код статуса на DDeivery
         *
         * @return string
         */
        public function getDDStatusDescription( $ddStatus ){
           $statusProvider = new DDStatusProvider();
           return $statusProvider->getOrderDescription( $ddStatus );
        }

        /**
         * @param DShopAdapter $dShopAdapter
         * @throws DDeliveryException
         */
        public function _initDb(DShopAdapter $dShopAdapter)
        {
            $dbConfig = $dShopAdapter->getDbConfig();
            if (isset($dbConfig['pdo']) && ($dbConfig['pdo'] instanceof \PDO || $dbConfig['pdo'] instanceof ConnectInterface)) {
                $this->pdo = $dbConfig['pdo'];
            } elseif ($dbConfig['type'] == DShopAdapter::DB_SQLITE) {
                if (!$dbConfig['dbPath'])
                    throw new DDeliveryException('SQLite db is empty');

                $dbDir = dirname($dbConfig['dbPath']);
                if ((!is_writable($dbDir)) || (!is_writable($dbConfig['dbPath'])) || (!is_dir($dbDir))) {
                    throw new DDeliveryException('SQLite database does not exist or is not writable');
                }

                $this->pdo = new \PDO('sqlite:' . $dbConfig['dbPath']);
                $this->pdo->exec('PRAGMA journal_mode=WAL;');
            } elseif ($dbConfig['type'] == DShopAdapter::DB_MYSQL) {
                $this->pdo = new \PDO($dbConfig['dsn'], $dbConfig['user'], $dbConfig['pass']);
                $this->pdo->exec('SET NAMES utf8');
            } else {
                throw new DDeliveryException('Not support database type');
            }
            $this->pdoTablePrefix = isset($dbConfig['prefix']) ? $dbConfig['prefix'] : '';
        }

        /**
         * Получить описание точки в заказе
         * @param $order
         * @return string
         */
        public function getPointComment( $order ){
            $comment = '';
            $point = $order->getPoint();

            if( $order->type == DDeliverySDK::TYPE_SELF ){
                $comment = 'Самовывоз, ' . $order->cityName . ' ' . $point['address'] .
                    (', ' . $point['delivery_company_name']) .
                    (', ' . $point['name'] . ', ID точки - ' . $point['_id'] ) .
                    (', ' . (($point['type'] == 1)?'Постамат':'ПВЗ'));
            }else if( $order->type == DDeliverySDK::TYPE_COURIER ){
                $comment = 'Доставка курьером по адресу ' . $order->getFullAddress() .
                    (', ' . $point['delivery_company_name']) ;
            }
            return $comment;
        }


        /**
         * Получить список заказов по массиву из ID
         *
         * @param $ids
         * @return DDeliveryOrder[]
         */
        public function getOrderList($ids){
            $orderDB = new DataBase\Order($this->pdo, $this->pdoTablePrefix);
            $orders = $orderDB->getOrderList($ids);
            $orderList = array();
            if( count($orders) ) {
                foreach( $orders as &$item ){
                    $productList = unserialize($item->cart);
                    $currentOrder = new DDeliveryOrder($productList);
                    $this->_initOrderInfo($currentOrder, $item);
                    $orderList[] = $currentOrder;
                }
            }
            return $orderList;
        }
    }
