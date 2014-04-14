<?php
/**
 * User: DnAp
 * Date: 30.03.14
 * Time: 18:13
 */

namespace DDelivery\Base;


use DDelivery\Adapter\DShopAdapter;
use DDelivery\DDeliveryException;
use DDelivery\Order\DDeliveryOrder;
use DDelivery\Point\DDeliveryPointCourier;
use DDelivery\Point\DDeliveryPointSelf;


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
     * @todo все исправить
     * @param DDeliveryPointSelf $ddeliveryPointSelf
     * @param DDeliveryOrder $order
     * @return DDeliveryPointSelf
     */
    public function preDisplaySelfPoint(DDeliveryPointSelf $ddeliveryPointSelf, DDeliveryOrder $order)
    {
        if(1)
            throw new \Exception('Ой, а это еще не работает');

        //$ddeliveryPointSelf->delivery_price = $this->preDisplayPointCalc($ddeliveryPointSelf->delivery_price);

        return $ddeliveryPointSelf;
    }

    /**
     * @param DDeliveryPointCourier $ddeliveryPointCourier
     * @param DDeliveryOrder $order
     * @return DDeliveryPointCourier
     */
    public function preDisplayCourierPoint(DDeliveryPointCourier $ddeliveryPointCourier, DDeliveryOrder $order)
    {
        $ddeliveryPointCourier->delivery_price = $this->preDisplayPointCalc($ddeliveryPointCourier->delivery_price);
        return $ddeliveryPointCourier;
    }

    private function preDisplayPointCalc($price)
    {
        $intervals = self::getIntervalsByPoint();

        $priceReturn = $price;

        foreach($intervals as $interval){
            if (!isset($interval['min']) || $price < $interval['min'])
                continue;

            if(!empty($interval['max']) && $price >= $interval['max'])
                continue;


            if (isset($interval)) {
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
                            $priceReturn = $price < $interval['amount'];
                        }
                        break;
                    case self::INTERVAL_RULES_CLIENT_ALL:
                }
            }
        }
        return $priceReturn;
    }

    /**
     * @param \DDelivery\Point\DDeliveryPointSelf[] $courierPoints
     * @param DDeliveryOrder $order
     * @return \DDelivery\Point\DDeliveryPointSelf[]
     */
    public function filterPointsSelf($courierPoints, DDeliveryOrder $order)
    {
        foreach($courierPoints as $courierPoint) {
            //if($courierPoint->)
        }

        return $courierPoints;
    }


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


}