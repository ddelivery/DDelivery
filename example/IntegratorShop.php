<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 15.05.14
 * Time: 23:14
 */

use DDelivery\Order\DDeliveryOrder;
use DDelivery\Order\DDeliveryProduct;
use DDelivery\Order\DDStatusProvider;

class IntegratorShop extends \DDelivery\Adapter\PluginFilters{
    /**
     * Синхронизация локальных статусов и статусов дделивери
     * @var array
     */
    protected  $cmsOrderStatus = array( DDStatusProvider::ORDER_IN_PROGRESS => 0,
                                        DDStatusProvider::ORDER_CONFIRMED => 23,
                                        DDStatusProvider::ORDER_IN_STOCK => 14,
                                        DDStatusProvider::ORDER_IN_WAY => 15,
                                        DDStatusProvider::ORDER_DELIVERED => 16,
                                        DDStatusProvider::ORDER_RECEIVED => 17,
                                        DDStatusProvider::ORDER_RETURN => 20,
                                        DDStatusProvider::ORDER_CUSTOMER_RETURNED => 21,
                                        DDStatusProvider::ORDER_PARTIAL_REFUND => 22,
                                        DDStatusProvider::ORDER_RETURNED_MI => 2,
                                        DDStatusProvider::ORDER_WAITING => 25,
                                        DDStatusProvider::ORDER_CANCEL => 26 );



    /**
     * Верните true если нужно использовать тестовый(stage) сервер
     * @return bool
     */
    public function isTestMode(){
        return true;
        // TODO: Change the autogenerated stub
    }


    /**
     * Возвращает товары находящиеся в корзине пользователя, будет вызван один раз, затем закеширован
     * @return DDeliveryProduct[]
     */
    protected function _getProductsFromCart(){
        $products = array();

        $products[] = new DDeliveryProduct(
            1,	//	int $id id товара в системе и-нет магазина
            20,	//	float $width длинна
            13,	//	float $height высота
            25,	//	float $length ширина
            0.5,	//	float $weight вес кг
            1000,	//	float $price стоимостьв рублях
            1,
            'Веселый клоун',	//	string $name Название вещи
            'artikul222'
        );
        $products[] = new DDeliveryProduct(2, 10, 13, 15, 0.3, 1500, 2, 'Грустный клоун', 'another artikul222');
        return $products;
    }

    /**
     * Настройки базы данных
     * @return array
     */
    public function getDbConfig(){

        return array(
            'type' => self::DB_SQLITE,
            'dbPath' => $this->getPathByDB(),
            'prefix' => '',
        );

        return array(
            'pdo' => new \PDO('mysql:host=localhost;dbname=ddelivery', 'root', 'root', array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
            'prefix' => '',
        );


        $connect = mysql_connect('localhost', 'root', '0');
        mysql_select_db('bitrix', $connect);
        mysql_query('SET NAMES utf8');
        return array(
            'pdo' => new DDelivery\DB\Mysql\Connect($connect),
            'prefix' => 'ddelivery_',
        );


        return array(
            'type' => self::DB_MYSQL,
            'dsn' => 'mysql:host=localhost;dbname=ddelivery',
            'user' => 'root',
            'pass' => '0',
            'prefix' => '',
        );
    }

    /**
     * Меняет статус внутреннего заказа cms
     *
     * @param $cmsOrderID - id заказа
     * @param $status - статус заказа для обновления
     *
     * @return bool
     */
    public function setCmsOrderStatus($cmsOrderID, $status){
        // TODO: Implement setCmsOrderStatus() method.
    }

    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
    public function getApiKey(){
            return '852af44bafef22e96d8277f3227f0998';
    }

    /**
     * Должен вернуть url до каталога с статикой
     * @return string
     */
    public function getStaticPath(){
        return '../html/';
    }

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public function getPhpScriptURL(){
        // Тоесть до этого файла
        return 'ajax.php?' . http_build_query( $_GET ) ;
    }

    /**
     * Возвращает путь до файла базы данных, положите его в место не доступное по прямой ссылке
     * @return string
     */
    public function getPathByDB(){
        return __DIR__.'/../db/db.sqlite';
    }

    /**
     * Метод будет вызван когда пользователь закончит выбор способа доставки
     *
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @return void
     */
    public function onFinishChange($order){

    }

    /**
     * Какой процент от стоимости страхуется
     * @return float
     */
    public function getDeclaredPercent(){
        return 100; // Ну это же пример, пускай будет случайный процент
    }

    /**
     * Должен вернуть те компании которые  показываются в курьерке
     *
     * @return int[]
     */
    public function filterCompanyPointCourier(){
        return  array_keys( \DDelivery\DDeliveryUI::getCompanySubInfo() );
        //return array();
        return array	(4,21,29,23,27,28,20,30,31,11,16,22,17,3,14,1,13,18,6,
                         26,25,24,7,35,36,37,39,40,42,43,44,45,46,47,48,49);
        // TODO: Implement filterCompanyPointCourier() method.
    }

    /**
     * Должен вернуть те компании которые  показываются в самовывозе
     *
     * @return int[]
     */
    public function filterCompanyPointSelf(){
        return  array_keys( \DDelivery\DDeliveryUI::getCompanySubInfo() );
        return array	(4,21,29,23,27,28,20,30,31,11,16,22,17,3,14,1,13,18,6,
                         26,25,24,7,35,36,37,39,40,42,43,44,45,46,47,48,49);
        // TODO: Implement filterCompanyPointSelf() method.
    }

    /**
     * Возвращаем способ оплаты  c наложенным платежем для курьера
     *
     * либо константа \DDelivery\Adapter\PluginFilters::PAYMENT_PREPAYMENT - если способ облаты - предоплата,
     * либо константа \DDelivery\Adapter\PluginFilters::PAYMENT_POST_PAYMENT -  если способ оплаты оплата при получении
     *
     * @param $order DDeliveryOrder
     *
     * @return int
     */
    public function filterPointByPaymentTypeCourier( $order ){
        return \DDelivery\Adapter\PluginFilters::PAYMENT_PREPAYMENT;
    }

    /**
     * Возвращаем способ оплаты  c наложенным платежем для самовывоза
     *
     * либо константа \DDelivery\Adapter\PluginFilters::PAYMENT_PREPAYMENT - если способ облаты - предоплата,
     * либо константа \DDelivery\Adapter\PluginFilters::PAYMENT_POST_PAYMENT -  если способ оплаты оплата при получении
     *
     * @param $order DDeliveryOrder
     *
     * @return int
     */
    public function filterPointByPaymentTypeSelf( $order ){
        return \DDelivery\Adapter\PluginFilters::PAYMENT_PREPAYMENT;
    }

    /**
     * Если true, то не учитывает цену забора
     * @return bool
     */
    public function isPayPickup(){
            return false;
        return true;
        // TODO: Implement isPayPickup() method.
    }

    /**
     * Метод возвращает настройки оплаты фильтра которые должны быть собраны из админки
     *
     * @return array
     */
    public function getIntervalsByPoint(){
        return array();
        return array(
            array('min' => 0, 'max'=>100, 'type'=>self::INTERVAL_RULES_MARKET_AMOUNT, 'amount'=>100),
            array('min' => 100, 'max'=>200, 'type'=>self::INTERVAL_RULES_CLIENT_ALL, 'amount'=>60),
            array('min' => 200, 'max'=>5000, 'type'=>self::INTERVAL_RULES_MARKET_PERCENT, 'amount'=>50),
            array('min' => 5000, 'max'=>null, 'type'=>self::INTERVAL_RULES_MARKET_ALL),
        );
    }

    /**
     * Тип округления
     * @return int
     */
    public function aroundPriceType(){
        return self::AROUND_ROUND; // self::AROUND_FLOOR, self::AROUND_CEIL
    }

    /**
     * Шаг округления
     * @return float
     */
    public function aroundPriceStep(){
        return 0.5; // До 50 копеек
        // TODO: Implement aroundPriceStep() method.
    }

    /**
     * описание собственных служб доставки
     * @return string
     */
    public function getCustomPointsString(){
        return '';
    }

    /**
     * Если вы знаете имя покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientFirstName() {
        return 'xxx xxx xxx';
    }

    /**
     * Если вы знаете фамилию покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientLastName() {
        return 'cccccc ccccccc ccccc';
    }

    /**
     * Если вы знаете телефон покупателя, сделайте чтобы оно вернулось в этом методе. 11 символов, например 79211234567
     * @return string|null
     */
    public function getClientPhone() {
        return '79211234567'; ///null;
    }

    /**
     * Верни массив Адрес, Дом, Корпус, Квартира. Если не можешь можно вернуть все в одном поле и настроить через get*RequiredFields
     * @return string[]
     */
    public function getClientAddress() {
        return array('Улица 1','Дом 2','Корпус 3','Квартира 4','5');
    }

    public function getClientEmail(){
        return 'example@ex.ru';
    }

    /**
     * Верните id города в системе DDelivery
     * @return int
     */
    public function getClientCityId(){
        // Если нет информации о городе, оставьте вызов родительского метода.
        //return 151185;
        return parent::getClientCityId();
    }

    /**
     * Возвращает поддерживаемые магазином способы доставки
     * @return array
     */
    public function getSupportedType(){
        return array(
           \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER,
            \DDelivery\Sdk\DDeliverySDK::TYPE_SELF
        );
    }


    /**
     * При отправке заказа на сервер дделивери идет
     * проверка  статуса  выставленого в настройках
     *
     * @param mixed $cmsStatus
     * @return bool|void
     */
    public function isStatusToSendOrder( $cmsStatus ){
        return;
    }


    /**
     *
     * Перед возвратом точек самовывоза фильтровать их по определенным правилам
     *
     * @param $companyArray
     * @param DDeliveryOrder $order
     * @return mixed
     */
    public function finalFilterSelfCompanies( $companyArray, $order ){
        $companyArray = parent::finalFilterSelfCompanies( $companyArray, $order );
        return $companyArray;
    }

    /**
     *
     *  Перед возвратом компаний курьерок фильтровать их по определенным правилам
     *
     * @param $companyArray
     * @param DDeliveryOrder $order
     * @return mixed
     */
    public function finalFilterCourierCompanies( $companyArray, $order ){
        $companyArray = parent::finalFilterCourierCompanies( $companyArray, $order );
        return $companyArray;
    }


    /**
     *
     * Используется при отправке заявки на сервер DD для указания стартового статуса
     *
     * Если true то заявка в сервисе DDelivery будет выставлена в статус "Подтверждена",
     * если false то то заявка в сервисе DDelivery будет выставлена в статус "В обработке"
     *
     * @param mixed $localStatus
     *
     * @return bool
     */
    public function isConfirmedStatus( $localStatus ){
        return true;
    }

    public function getClientZipCode(){
       return 14000;
    }

    /**
     * Возвращает бинарную маску обязательных полей для курьера
     * Если редактирование не включено, но есть обязательность то поле появится
     * Если редактируемых полей не будет то пропустим шаг
     * @return int
     */
    public function getCourierRequiredFields(){
        // ВВести все обязательно, кроме корпуса
        return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
        | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE
        | self::FIELD_EDIT_ADDRESS | self::FIELD_REQUIRED_ADDRESS
        | self::FIELD_EDIT_ADDRESS_HOUSE | self::FIELD_REQUIRED_ADDRESS_HOUSE
        | self::FIELD_EDIT_ADDRESS_HOUSING
        | self::FIELD_EDIT_ADDRESS_FLAT | self::FIELD_REQUIRED_ADDRESS_FLAT | self::FIELD_EDIT_EMAIL
        | self::FIELD_EDIT_INDEX;
    }

    /**
     * Возвращает бинарную маску обязательных полей для пунктов самовывоза
     * Если редактирование не включено, но есть обязательность то поле появится
     * Если редактируемых полей не будет то пропустим шаг
     * @return int
     */
    public function getSelfRequiredFields(){
        // Имя, фамилия, мобилка
        return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
        | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE | self::FIELD_EDIT_EMAIL;
    }

    /**
     * Получить название шаблона для сдк ( разные цветовые схемы )
     *
     * @return string
     */
    public function getTemplate(){
        return 'blue';
    }

    /**
     *
     * Получить массив с кастомными курьерскими компаниями
     *
     * @return array
     */
    public function getCustomCourierCompanies(){
        //return array();
        return array(
            'custom_company1' => array(
                'city' => 151184,
                'delivery_company' => 'custom_company1',
                'delivery_company_name' => 'XXX company',
                'pickup_price' => 250,
                'delivery_price' => 170,
                'delivery_price_fee' => 0,
                'declared_price_fee' => 30,
                'delivery_time_min' => 2,
                'delivery_time_max' => 3,
                'delivery_time_avg' => 3,
                'return_price' => 0,
                'return_client_price' => 0,
                'return_partial_price' => 0,
                'total_price' => 450
            )
        );
    }

    /**
     *
     * Получить массив с кастомными компаниями самовывоза
     *
     * @return array
     */
    public function getCustomSelfCompanies(){
        //return array();
        return array(
            'custom_self_company1' => array(
                'city' => 151184,
                'delivery_company' => 'custom_self_company1',
                'delivery_company_name' => 'XXX Self company',
                'pickup_price' => 250,
                'delivery_price' => 170,
                'delivery_price_fee' => 0,
                'declared_price_fee' => 30,
                'delivery_time_min' => 2,
                'delivery_time_max' => 3,
                'delivery_time_avg' => 3,
                'return_price' => 0,
                'return_client_price' => 0,
                'return_partial_price' => 0,
                'total_price' => 450
            )
        );
    }

    /**
     *
     * Получить массив с кастомными точками самовывоза
     *
     * @return array
     */
    public function getCustomSelfPoints(){
        //return array();
        return array(
            1000900 => array(
                '_id' => 1000900,
                'name' => 'xxxxxxx',
                'city_id' => 151184,
                'city' => 'Москва',
                'region' => 'Москва',
                'region_id' => '77',
                'city_type' => 'г',
                'postal_code' => '101000',
                'area' =>'',
                'kladr' => '77000000000',
                'company' => 'XXX Self company',
                'company_id' => 'custom_self_company1',
                'company_code' => 'MSK3',
                'metro' => '',
                'description_in' =>'',
                'description_out' =>'',
                'indoor_place' =>'',
                'address' => 'Своя собственная точка доставки`',
                'schedule' => 'пн.-пт. 11-20, сб. 11-17, вс. 11-16',
                'longitude' => '37.582745',
                'latitude' => '55.778628',
                'type' => 2,
                'status' => 2,
                'has_fitting_room' => '',
                'is_cash' => 1,
                'is_card' => ''
            )
        );
    }

    public  function getSelfPaymentVariants($order){
        return array(2);
    }

    public function getCourierPaymentVariants($order){
        return array(3);
    }

}