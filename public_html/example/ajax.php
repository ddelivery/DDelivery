<?php


ini_set("display_errors", "1");
error_reporting(E_ALL);

include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));

use DDelivery\Adapter\DShopAdapter;
use DDelivery\DDeliveryUI;
use DDelivery\Order\DDeliveryProduct;

class IntegratorShop extends \DDelivery\Adapter\PluginFilters
{

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
        return '4bf43a2cd2be3538bf4e35ad8191365d';
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
        return array(1,2,3);
        // TODO: Implement filterCompanyPointCourier() method.
    }

    /**
     * Должен вернуть те компании которые НЕ показываются в самовывозе
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    public function filterCompanyPointSelf()
    {
        return array(1,2,3);
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
        if(rand(1,3) == 1){
            return self::PAYMENT_POST_PAYMENT;
        }elseif(rand(0,1)){
            return self::PAYMENT_PREPAYMENT;
        }else{
            return self::PAYMENT_NOT_CARE;
        }
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
        if(rand(1,3) == 1){
            return self::PAYMENT_POST_PAYMENT;
        }elseif(rand(0,1)){
            return self::PAYMENT_PREPAYMENT;
        }else{
            return self::PAYMENT_NOT_CARE;
        }
        // TODO: Implement filterPointByPaymentTypeSelf() method.
    }

    /**
     * Если true, то не учитывает цену забора
     * @return bool
     */
    public function isPayPickup()
    {
        if(empty($this->_isPayPickup)) {
            $this->_isPayPickup=!rand(0,1);
        }
        return $this->_isPayPickup;
        // TODO: Implement isPayPickup() method.
    }

    /**
     * Метод возвращает настройки оплаты фильтра которые должны быть собраны из админки
     *
     * @throws DDeliveryException
     * @return array
     */
    public function getIntervalsByPoint()
    {
        return array(
            array('min' => 0, 'max'=>1000, 'type'=>self::INTERVAL_RULES_MARKET_AMOUNT, 'amount'=>100),
            array('min' => 1000, 'max'=>2000, 'type'=>self::INTERVAL_RULES_MARKET_AMOUNT, 'amount'=>200),
            array('min' => 3000, 'max'=>4000, 'type'=>self::INTERVAL_RULES_MARKET_PERCENT, 'amount'=>200),
            array('min' => 4000, 'max'=>null, 'type'=>self::INTERVAL_RULES_MARKET_ALL),
        );
    }

}




$IntegratorShop = new IntegratorShop();


$ddeliveryUI = new DDeliveryUI($IntegratorShop);
// В зависимости от параметров может выводить полноценный html или json
$ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());


