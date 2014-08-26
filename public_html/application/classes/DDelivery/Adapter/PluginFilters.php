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
        $filterByPayment = $this->filterPointByPaymentTypeCourier();
        if($filterByPayment == $order->paymentVariant) {
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
        $filterByPayment = $this->filterPointByPaymentTypeSelf();
        if($filterByPayment == $order->paymentVariant){
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
     * @param $price
     * @return bool|int
     */
    public function preDisplayPointCalc($price)
    {
        $intervals = $this->getIntervalsByPoint();

        $priceReturn = $price;

        foreach($intervals as $interval){
            if (!isset($interval['min']) || $price < $interval['min'])
                continue;

            if(!empty($interval['max']) && $price >= $interval['max'])
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
     * @return int
     */
    abstract public function filterPointByPaymentTypeCourier();

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Самовывоз
     * @return int
     */
    abstract public function filterPointByPaymentTypeSelf();


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


}