<?php
/**
 *
 * @package    DDelivery.Adapter
 *
 * @author  mrozk 
 */

namespace DDelivery\Adapter;

use DDelivery\Order\DDeliveryOrder;
use DDelivery\Order\DDeliveryProduct;
use DDelivery\Point\DDeliveryAbstractPoint;
use DDelivery\Point\DDeliveryPointCourier;
use DDelivery\Point\DDeliveryPointSelf;
use DDelivery\Sdk\DDeliverySDK;

/**
 * Class DShopAdapter
 * @package DDelivery\Adapter
 */
abstract class DShopAdapter
{  
    /**
     * Имя редактируется
     */
    const FIELD_EDIT_FIRST_NAME = 1;
    /**
     * Имя обязательное
     */
    const FIELD_REQUIRED_FIRST_NAME = 2;
    /**
     * Фамилия редактируется
     */
    const FIELD_EDIT_LAST_NAME = 4;
    /**
     * Фамилия обязательное
     */
    const FIELD_REQUIRED_LAST_NAME = 8;
    /**
     * Телефон редактируется
     */
    const FIELD_EDIT_PHONE = 16;
    /**
     * Телефон обязательное
     */
    const FIELD_REQUIRED_PHONE = 32;
    /**
     * Адресс редактируется
     */
    const FIELD_EDIT_ADDRESS = 64;
    /**
     * Адресс обязательное
     */
    const FIELD_REQUIRED_ADDRESS = 128;
    /**
     * Адресс, дом редактируется
     */
    const FIELD_EDIT_ADDRESS_HOUSE = 256;
    /**
     * Адресс, дом обязательное
     */
    const FIELD_REQUIRED_ADDRESS_HOUSE = 512;
    /**
     * Адресс, корпус редактируется
     */
    const FIELD_EDIT_ADDRESS_HOUSING = 1024;
    /**
     * Адресс, корпус обязательное
     */
    const FIELD_REQUIRED_ADDRESS_HOUSING = 2048;
    /**
     * Адресс, квартира редактируется
     */
    const FIELD_EDIT_ADDRESS_FLAT = 4096;
    /**
     * Адресс, квартира обязательное
     */
    const FIELD_REQUIRED_ADDRESS_FLAT = 8192;

    /**
     * Кеш объекта
     * @var DDeliveryProduct[]
     */
    private $productsFromCart = null;
    
    
    private $ddeliveryOrderStatus = array( '10' => 'В обработке', '20' => 'Подтверждена', '30' => 'На складе ИМ',
                                           '40' => 'Заказ в пути', '50' => 'Заказ доставлен', '60' => 'Заказ получен',
                                           '70' => 'Возврат заказа', '80' => 'Клиент вернул заказ', '90' => 'Частичный возврат заказа',
                                           '100' => 'Возвращен в ИМ', '110' => 'Ожидание', '120' => 'Отмена');
    
    /**
     * Возвращает товары находящиеся в корзине пользователя, будет вызван один раз, затем закеширован
     * @return DDeliveryProduct[]
     */
    protected abstract function _getProductsFromCart();
    
    /**
     * Проверяет статус заказа, при определенном статусе отправляем заказ на сервер dd
     * 
     * @param string $status
     * @param DDeliveryOrder $order
     * 
     * @return DDeliveryProduct[]
     */
    public function isValidStatusToOrder( $status, $order )
    {
       return true;
    }
    
    /**
     * Возвращает товары находящиеся в корзине пользователя, реализует кеширование getProductsFromCart
     * @return DDeliveryProduct[]
     */
    public final function getProductsFromCart()
    {
        if(!$this->productsFromCart) {
            $this->productsFromCart = $this->_getProductsFromCart();
        }
        return $this->productsFromCart;
    }
    
    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
    public abstract function getApiKey();

    /**
     * Должен вернуть url до каталога с статикой
     * @return string
     */
    public abstract function getStaticPath();

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public abstract function getPhpScriptURL();

    /**
     * Верните true если нужно использовать тестовый(stage) сервер
     * @return bool
     */
    public function isTestMode()
    {
        return true;
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
     * Если вы знаете телефон покупателя, сделайте чтобы оно вернулось в этом методе
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
        return array();
    }

    /**
     * Возвращает путь до файла базы данных, положите его в место не доступное по прямой ссылке
     * @return string
     */
    public abstract function getPathByDB();
    
    
    
    
    /**
     * Вызывается перед отображением цены точки самовывоза, можно что-то изменить
     *
     * @param DDeliveryPointSelf $ddeliveryPointSelf
     * @param DDeliveryOrder $order
     *
     * @return \DDelivery\Point\DDeliveryPointSelf
     */
    public function preDisplaySelfPoint( DDeliveryPointSelf $ddeliveryPointSelf, DDeliveryOrder $order) {

    }

    /**
     * Вызывается перед отображением цены курьера, можно что-то изменить
     *
     * @param \DDelivery\Point\DDeliveryPointCourier $DDeliveryPointCourier
     * @param DDeliveryOrder $order
     *
     */
    public function preDisplayCourierPoint( DDeliveryPointCourier $DDeliveryPointCourier, DDeliveryOrder $order) {

    }

    /**
     * Срабатывает когда выбрана точка доставки
     *
     * @param DDeliveryAbstractPoint $point

    public function onChangePoint( DDeliveryAbstractPoint $point) {}
     */

    /**
     * Если необходимо фильтрует курьеров и добавляет новых
     * Кстати здесь можно отсортировать еще точки
     *
     * @param DDeliveryPointCourier[] $courierPoints
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @return \DDelivery\Point\DDeliveryPointCourier[]
     */
    public function filterPointsCourier($courierPoints, DDeliveryOrder $order) {
        return $courierPoints;
    }

    /**
     * Если необходимо фильтрует пункты самовывоза и добавляет новых
     *
     * @param DDeliveryPointSelf[] $courierPoints
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @return \DDelivery\Point\DDeliveryPointSelf[]
     */
    public function filterPointsSelf($courierPoints, DDeliveryOrder $order) {
        return $courierPoints;
    }
    
    /**
     *
     * Получить свойство refnum для курьерки
     *
     * @param \DDelivery\Order\DDeliveryOrder $order
     *
     * @return float
     */
    public function getShopRefNum( $order )
    {
    	return 'shopRefNum';
    }
    
    /**
     * Если есть необходимость искать точки на сервере 
     * ddelivery 
     * 
     * @param \DDelivery\Order\DDeliveryOrder $order
     * 
     * @return boolean
     */
    public function preGoToFindPoints( $order )
    {
        return true;        	
    }
    
    /**
     * 
     * Есть ли необходимость отправлять заказ на сервер ddelivery
     * 
     * @param \DDelivery\Order\DDeliveryOrder $order
     * 
     * @return float
     */
    public function sendOrderToDDeliveryServer( $order ) 
    {
        return true;    	
    }
    
    /**
     * Возвращает выбраный вариант оплаты
     * @return float
     */
    public function getPaymentVariant( ) {
    	 return null;
    }
    /**
     * 
     * Сумма к оплате на точке или курьеру
     * 
     * Возвращает параметр payment_price для создания заказа
     * Параметр payment_price необходим для добавления заявки на заказ
     * По этому параметру в доках интегратору будет написан раздел
     * 
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @param float $orderPrice
     * 
     * @return float
     */
    public function getPaymentPrice( $order, $orderPrice ) {
    	return 0;
    }
    /**
     *
     * Получить список продуктов по id
     * @param int[]
     * 
     * @return array DDeliveryProduct[]
     */
    public function getProductsByID( $productIDs )
    {
        return array();
    }
    /**
     * Верните id города в системе DDelivery
     * @return int
     */
    public function getClientCityId() {
        if(isset($_COOKIE['ddCityId'])){
            return $_COOKIE['ddCityId'];
        }
        return 0;
    }
    
    
    /**
     * Возвращает стоимоть заказа
     * @return float
     */
    public abstract function getAmount();
    
    
    /**
     * Возвращает оценочную цену для товаров в послыке
     * 
     * @param \DDelivery\Order\DDeliveryOrder $order
     * 
     * @return float
     */
    public function getDeclaredPrice( $order ) {
    	$declaredPrice = $order->amount;
        return $declaredPrice;
    }

    /**
     * Возвращает поддерживаемые магазином способы доставки
     * @return array
     */
    public function getSupportedType()
    {
        return array(DDeliverySDK::TYPE_COURIER, DDeliverySDK::TYPE_SELF);
    }

    /**
     * Возвращает бинарную маску обязательных полей для курьера
     * Если редактирование не включено, но есть обязательность то поле появится
     * Если редактируемых полей не будет то пропустим шаг
     * @return int
     */
    public function getCourierRequiredFields()
    {
        // ВВести все обязательно, кроме корпуса
        return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME | self::FIELD_EDIT_LAST_NAME | self::FIELD_REQUIRED_LAST_NAME
            | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE
            | self::FIELD_EDIT_ADDRESS | self::FIELD_REQUIRED_ADDRESS
            | self::FIELD_EDIT_ADDRESS_HOUSE | self::FIELD_REQUIRED_ADDRESS_HOUSE
            | self::FIELD_EDIT_ADDRESS_HOUSING
            | self::FIELD_EDIT_ADDRESS_FLAT;
    }

    /**
     * Возвращает бинарную маску обязательных полей для пунктов самовывоза
     * Если редактирование не включено, но есть обязательность то поле появится
     * Если редактируемых полей не будет то пропустим шаг
     * @return int
     */
    public function getSelfRequiredFields()
    {
        // Имя, фамилия, мобилка
        return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
            | self::FIELD_EDIT_LAST_NAME | self::FIELD_REQUIRED_LAST_NAME
            | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE;
    }

}