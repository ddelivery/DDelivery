<?php
/**
 * User: DnAp
 * Date: 30.03.14
 * Time: 18:13
 */

namespace DDelivery\Adapter;


use DDelivery\Adapter\DShopAdapter;
use DDelivery\DDeliveryException;
use DDelivery\Order\DDeliveryOrder;



/**
 * Класс реализует базовую логику фильтров для плагина интернет магазинов
 *
 * Class PluginFilters
 * @package DDelivery\Base
 */
abstract class PluginFilters extends DShopAdapter
{
    /**
     * Клиент оплачивает все
     */
    const INTERVAL_RULES_CLIENT_ALL = 1;
    /**
     * Магазин оплачивает все
     */
    const INTERVAL_RULES_MARKET_ALL = 2;
    /**
     *  Магазин оплачивает процент от стоимости доставки
     */
    const INTERVAL_RULES_MARKET_PERCENT = 3;
    /**
     * Магазин оплачивает конкретную сумму от доставки. Если сумма больше, то всю доставку
     */
    const INTERVAL_RULES_MARKET_AMOUNT = 4;

    /**
     * Оплата не важно где
     */
    const PAYMENT_NOT_CARE = 0;
    /**
     * Способ оплаты, только предоплата
     */
    const PAYMENT_PREPAYMENT = 1;
    /**
     * Оплата на месте курьеру или в точке самовывоза
     */
    const PAYMENT_POST_PAYMENT = 2;

    /**
     * Округлять цену в математически(просто round)
     */
    const AROUND_ROUND = 1;

    /**
     * Округлять в меньшую сторону
     */
    const AROUND_FLOOR = 2;

    /**
     * Округлять в большую сторону
     */
    const AROUND_CEIL = 3;


    public  function  getErrorMsg( \Exception $e, $extraParams = array() ){
        return $e->getMessage();
    }
    /**
     *
     * Залоггировать ошибку
     *
     * @param \Exception $e
     * @param array $extraParams
     *
     * @return mixed
     */
    public function logMessage( \Exception $e, $extraParams = array() ){
        $logginUrl = $this->getLogginServer();
        if( !is_null( $logginUrl ) ){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_URL, $logginUrl);
            curl_setopt($curl, CURLOPT_POST, true);

            $message = $this->getErrorMsg($e, $extraParams);

            $params = array('message' => $message . ', версия SDK -' . DShopAdapter::SDK_VERSION . ', '
                . $e->getFile() . ', '
                . $e->getLine() . ', ' . date("Y-m-d H:i:s"), 'url' => $_SERVER['SERVER_NAME'],
                'apikey' => $this->getApiKey(),
                'testmode' => (int)$this->isTestMode());
            $urlSuffix = '';
            foreach($params as $key => $value) {
                $urlSuffix .= urlencode($key).'='.urlencode($value) . '&';
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $urlSuffix);
            $answer = curl_exec($curl);
            curl_close($curl);
            return $answer;
        }
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
    public function getPaymentPriceCourier($order, $orderPrice)
    {
        $filterByPayment = $this->filterPointByPaymentTypeCourier( $order );
        if($filterByPayment == PluginFilters::PAYMENT_POST_PAYMENT) {
            if( $orderPrice && $order->amount ) {
                return $order->amount + $orderPrice;
            }
            return $order->amount;
        }
        return 0;
    }



    /**
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
    public function getPaymentPriceSelf( $order, $orderPrice )
    {
        $filterByPayment = $this->filterPointByPaymentTypeSelf( $order );
        if($filterByPayment == PluginFilters::PAYMENT_POST_PAYMENT){
            if( $orderPrice && $order->amount ){
                return $order->amount + $orderPrice;
            }
            return $order->amount;
        }

        return 0;
    }

    /**
     * Возвращает стоимоть заказа
     * @return float
     */
    public function getAmount()
    {
        $amount = 0.;
        foreach($this->getProductsFromCart() as $product) {
            $amount += $product->getPrice() * $product->getQuantity();
        }
        return $amount;
    }

    /**
     * Если true, то не учитывает цену забора
     * @return bool
     */
    abstract public function isPayPickup();


    /**
     * Возвращает оценочную цену для товаров в послыке
     *
     * @param \DDelivery\Order\DDeliveryOrder $order
     *
     * @return float
     */
    public function getDeclaredPrice($order)
    {
        return ($order->amount / 100) * $this->getDeclaredPercent();
    }

    /**
     * Какой процент от стоимости страхуется
     * @return float
     */
    abstract public function getDeclaredPercent();


    /**
     * Округляет стоимость согласно настройкам
     * @param float $price
     * @return float
     */
    public function aroundPrice($price)
    {
        $step = $this->aroundPriceStep();
        $type = $this->aroundPriceType();

        $priceCount = $price / $step;
        if($priceCount == (int)$priceCount) {
            return $price;
        }
        switch ($type) {
            case self::AROUND_ROUND:
                return $step*round($priceCount);
            case self::AROUND_FLOOR:
                return $step*floor($priceCount);
            case self::AROUND_CEIL:
                return $step*ceil($priceCount);
        }
        return $price;
    }

    /**
     *
     * Есть ли необходимость отправлять заказ на сервер ddelivery
     *
     * @param \DDelivery\Order\DDeliveryOrder $order
     *
     * @return bool
     *
     */
    public function sendOrderToDDeliveryServer( $order ){
        $point = $order->getPoint();
        if( array_key_exists( $point['delivery_company'], $this->customCourierCompanies ) || array_key_exists( $point['delivery_company'], $this->customSelfCompanies )){

            return false;
        }
        return true;
    }
    /**
     * @param $price
     * @param $orderSum
     * @return bool|int
     */
    public function preDisplayPointCalc($price, $orderSum){
        $intervals = $this->getIntervalsByPoint();

        $priceReturn = $price;

        foreach($intervals as $interval){
            if (!isset($interval['min']) || $orderSum < $interval['min'])
                continue;

            if(!empty($interval['max']) && $orderSum >= $interval['max'])
                continue;


            switch($interval['type']){
                case self::INTERVAL_RULES_MARKET_ALL:
                    $priceReturn = 0;
                    break;
                case self::INTERVAL_RULES_MARKET_PERCENT:
                    $priceReturn = $price - ($price / 100 * $interval['amount']);
                    break;
                case self::INTERVAL_RULES_MARKET_AMOUNT:
                    if($price < $interval['amount']) {
                        $priceReturn = 0;
                    }else{
                        $priceReturn = $price - $interval['amount'];
                    }
                    break;
                case self::INTERVAL_RULES_CLIENT_ALL:
            }

        }
        return $priceReturn;
    }

    /**
     * Тип округления
     * @return int
     */
    abstract public function aroundPriceType();

    /**
     * Шаг округления
     * @return float
     */
    abstract public function aroundPriceStep();

    /**
     * Должен вернуть те компании которые НЕ показываются в курьерке
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    abstract public function filterCompanyPointCourier();

    /**
     * Должен вернуть те компании которые НЕ показываются в самовывозе
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    abstract public function filterCompanyPointSelf();

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Курьер
     * @param $order DDeliveryOrder
     * @return int
     */
    abstract public function filterPointByPaymentTypeCourier( $order );

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Самовывоз
     * @param $order DDeliveryOrder
     * @return int
     */
    abstract public function filterPointByPaymentTypeSelf( $order );


    /**
     * Метод возвращает настройки оплаты фильтра которые должны быть собраны из админки
     *
     * @throws DDeliveryException
     * @return array
     */
    public function getIntervalsByPoint()
    {
        throw new DDeliveryException('Переопредели меня, я просто пример');
        return array(
            array('min' => 0, 'max'=>1000, 'type'=>self::INTERVAL_RULES_MARKET_AMOUNT, 'amount'=>100),
            array('min' => 1000, 'max'=>2000, 'type'=>self::INTERVAL_RULES_MARKET_AMOUNT, 'amount'=>200),
            array('min' => 3000, 'max'=>4000, 'type'=>self::INTERVAL_RULES_MARKET_PERCENT, 'amount'=>200),
            array('min' => 4000, 'max'=>null, 'type'=>self::INTERVAL_RULES_MARKET_ALL),
        );
    }

    /**
     * описание собственных служб доставки
     * @return string
     */
    public abstract function getCustomPointsString();


    /**
     *
     * Перед возвратом точек самовывоза фильтровать их по определенным правилам
     *
     * @param $companyArray
     * @param DDeliveryOrder $order
     * @return mixed
     */
    public function finalFilterSelfCompanies( $companyArray, $order ){
        if( count($this->getCustomSelfCompanies()) ){
            foreach( $this->getCustomSelfCompanies() as $key => $item ){
                if( $item['city'] == $order->city ){

                    $companyArray[] = $item;
                }
            }
        }

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
        if( count($this->getCustomCourierCompanies()) ){
            foreach( $this->getCustomCourierCompanies() as $key => $item ){
                if( $item['city'] == $order->city ){
                    $companyArray[] = $item;
                }
            }
        }
        return $companyArray;
    }

    /**
     *
     * Перед получение списка точек
     *
     * @param $resultPoints array
     * @param $order DDeliveryOrder
     * @param $resultCompanies array
     *
     * @return array
     */
    public function prePointListReturn( $resultPoints, $order, $resultCompanies ){
        if( count( $this->getCustomSelfPoints() ) ){
            foreach( $this->getCustomSelfPoints() as $key => $item ){
                if( ($item['city_id'] == $order->city) && isset($item['company_id']) ){
                        $resultPoints[] = $item;
                }
            }
        }
        return $resultPoints;
    }


    public function preGoToFindPoints( $order, $pointId = 0 ){
        if( array_key_exists( $pointId, $this->getCustomSelfPoints() ) ){
            return false;
        }
        return true;
    }

    public function getClientCityId(){
        if(isset($_COOKIE['ddCityId'])){
            return $_COOKIE['ddCityId'];
        }
        return 0;
    }

    /**
     *
     * Получить массив с кастомными курьерскими компаниями
     *
     * @return array
     */
    public function getCustomCourierCompanies(){
        return array();
    }

    /**
     *
     * Получить массив с кастомными компаниями самовывоза
     *
     * @return array
     */
    public function getCustomSelfCompanies(){
        return array();
    }

    /**
     *
     * Получить массив с кастомными точками самовывоза
     *
     * @return array
     */
    public function getCustomSelfPoints(){
        return array();
    }

}