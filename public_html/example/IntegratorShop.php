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

class IntegratorShop extends \DDelivery\Adapter\PluginFilters
{
    /**
     * Синхронизация локальных статусов
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
    public function isTestMode()
    {
        return true;
        // TODO: Change the autogenerated stub
    }


    /**
     * Возвращает товары находящиеся в корзине пользователя, будет вызван один раз, затем закеширован
     * @return DDeliveryProduct[]
     */
    protected function _getProductsFromCart()
    {
        $products = array();

        $products[] = new DDeliveryProduct(
            1,	//	int $id id товара в системе и-нет магазина
            20,	//	float $width длинна
            13,	//	float $height высота
            25,	//	float $length ширина
            0.5,	//	float $weight вес кг
            1000,	//	float $price стоимостьв рублях
            1,	//	int $quantity количество товара
            'Веселый клоун'	//	string $name Название вещи
        );
        $products[] = new DDeliveryProduct(2, 10, 13, 15, 0.3, 1500, 2, 'Грустный клоун');
        return $products;
    }

    /**
     * Меняет статус внутреннего заказа cms
     *
     * @param $cmsOrderID - id заказа
     * @param $status - статус заказа для обновления
     *
     * @return bool
     */
    public function setCmsOrderStatus($cmsOrderID, $status)
    {
        // TODO: Implement setCmsOrderStatus() method.
    }

    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
    public function getApiKey()
    {
            return '852af44bafef22e96d8277f3227f0998';
    }

    /**
     * Должен вернуть url до каталога с статикой
     * @return string
     */
    public function getStaticPath()
    {
        return '../html/';
    }

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public function getPhpScriptURL()
    {
        // Тоесть до этого файла
        return 'ajax.php';
    }

    /**
     * Возвращает путь до файла базы данных, положите его в место не доступное по прямой ссылке
     * @return string
     */
    public function getPathByDB()
    {
        return __DIR__.'/../db/db.sqlite';
    }

    /**
     * Метод будет вызван когда пользователь закончит выбор способа доставки
     *
     * @param int $orderId
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @param bool $customPoint Если true, то заказ обрабатывается магазином
     * @return void
     */
    public function onFinishChange($orderId, \DDelivery\Order\DDeliveryOrder $order, $customPoint)
    {
        if($customPoint){
            // Это условие говорит о том что нужно обрабатывать заказ средствами CMS
        }else{
            // Запомни id заказа
        }

    }

    /**
     * Какой процент от стоимости страхуется
     * @return float
     */
    public function getDeclaredPercent()
    {
        return 100; // Ну это же пример, пускай будет случайный процент
    }

    /**
     * Должен вернуть те компании которые НЕ показываются в курьерке
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    public function filterCompanyPointCourier()
    {
        return array();
        return array(4,32);
        // TODO: Implement filterCompanyPointCourier() method.
    }

    /**
     * Должен вернуть те компании которые НЕ показываются в самовывозе
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    public function filterCompanyPointSelf()
    {
        return array();
        return array(4,32);
        // TODO: Implement filterCompanyPointSelf() method.
    }

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Курьер
     * @return int
     */
    public function filterPointByPaymentTypeCourier()
    {
        return self::PAYMENT_POST_PAYMENT;
        // выбираем один из 3 вариантов(см документацию или комменты к констатам)
        return self::PAYMENT_POST_PAYMENT;
        return self::PAYMENT_PREPAYMENT;
        return self::PAYMENT_NOT_CARE;
        // TODO: Implement filterPointByPaymentTypeCourier() method.
    }

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Самовывоз
     * @return int
     */
    public function filterPointByPaymentTypeSelf()
    {
        return self::PAYMENT_POST_PAYMENT;
        // выбираем один из 3 вариантов(см документацию или комменты к констатам)
        return self::PAYMENT_POST_PAYMENT;
        return self::PAYMENT_PREPAYMENT;
        return self::PAYMENT_NOT_CARE;
        // TODO: Implement filterPointByPaymentTypeSelf() method.
    }

    /**
     * Если true, то не учитывает цену забора
     * @return bool
     */
    public function isPayPickup()
    {
        return true;
        // TODO: Implement isPayPickup() method.
    }

    /**
     * Метод возвращает настройки оплаты фильтра которые должны быть собраны из админки
     *
     * @return array
     */
    public function getIntervalsByPoint()
    {
        return array();
        return array(
            array('min' => 0, 'max'=>100, 'type'=>self::INTERVAL_RULES_MARKET_AMOUNT, 'amount'=>30),
            array('min' => 100, 'max'=>200, 'type'=>self::INTERVAL_RULES_CLIENT_ALL, 'amount'=>60),
            array('min' => 300, 'max'=>5000, 'type'=>self::INTERVAL_RULES_MARKET_PERCENT, 'amount'=>0),
            array('min' => 1000, 'max'=>null, 'type'=>self::INTERVAL_RULES_MARKET_ALL),
        );
    }

    /**
     * Тип округления
     * @return int
     */
    public function aroundPriceType()
    {
        return self::AROUND_ROUND; // self::AROUND_FLOOR, self::AROUND_CEIL
    }

    /**
     * Шаг округления
     * @return float
     */
    public function aroundPriceStep()
    {
        return 0.5; // До 50 копеек
        // TODO: Implement aroundPriceStep() method.
    }

    /**
     * описание собственных служб доставки
     * @return string
     */
    public function getCustomPointsString()
    {
        return '';
    }

    /**
     * Если вы знаете имя покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientFirstName() {
        return null;
    }

    /**
     * Если вы знаете фамилию покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientLastName() {
        return null;
    }

    /**
     * Если вы знаете телефон покупателя, сделайте чтобы оно вернулось в этом методе. 11 символов, например 79211234567
     * @return string|null
     */
    public function getClientPhone() {
        return null;
    }

    /**
     * Верни массив Адрес, Дом, Корпус, Квартира. Если не можешь можно вернуть все в одном поле и настроить через get*RequiredFields
     * @return string[]
     */
    public function getClientAddress() {
        return array('1','2','3','4','5');
    }

    /**
     * Верните id города в системе DDelivery
     * @return int
     */
    public function getClientCityId()
    {
        // Если нет информации о городе, оставьте вызов родительского метода.
        return parent::getClientCityId();
    }

    /**
     * Возвращает поддерживаемые магазином способы доставки
     * @return array
     */
    public function getSupportedType()
    {
        return array(
            \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER,
            \DDelivery\Sdk\DDeliverySDK::TYPE_SELF
        );
    }

    /**
     * Если необходимо фильтрует пункты самовывоза и добавляет новые
     *
     * @param \DDelivery\Point\DDeliveryPointSelf[] $selfPoints
     * @param DDeliveryOrder $order
     * @return \DDelivery\Point\DDeliveryPointSelf[]
     */
    public function filterPointsSelf($selfPoints, DDeliveryOrder $order)
    {

        $selfPoints = parent::filterPointsSelf($selfPoints, $order);
        /* пример установки собственного магазина на карту
        $myCourier = new \DDelivery\Point\PointSelfCustom(
            1, 'Моя компания', 'Петропавловская крепость, 8',
            59.950236, 30.31537, 100, 0, 'Ежедневно с 10 до 22',
            '', ''
        );
        $selfPoints[] = $myCourier;
        /*/
        return $selfPoints;
    }


}