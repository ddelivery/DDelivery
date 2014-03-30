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
use DDelivery\Point\DDeliveryPointSelf;


/**
 * Класс реализует базовую логику фильтров для плагина интернет магазинов
 *
 * Class ThreeFilters
 * @package DDelivery\Base
 */
abstract class ThreeFilters extends DShopAdapter
{
    /**
     *
     */
    const INTERVAL_RULES_CLIENT_ALL = 1;
    /**
     *
     */
    const INTERVAL_RULES_MARKET_ALL = 2;
    /**
     *  Магазин оплачивает процент от стоимости доставки
     */
    const INTERVAL_RULES_MARKET_PERCENT = 3;
    /**
     *
     */
    const INTERVAL_RULES_MARKET_AMOUNT = 4;


    /**
     * @todo все раскоментировать и исправить
     * @param DDeliveryOrder $order
     * @param DDeliveryPointSelf $ddeliveryPointSelf
     * @return DDeliveryPointSelf
     */
    public function preDisplayPoint(DDeliveryOrder $order, $ddeliveryPointSelf)
    {
        $intervals = self::getIntervalsByPointSelf();

        $price = 100;//$ddeliveryPointSelf->price;

        foreach($intervals as $interval){
            if (!isset($interval['min']) || $price < $interval['min'])
                continue;

            if(!empty($interval['max']) && $price >= $interval['max'])
                continue;


            if (isset($interval)) {
                switch($interval['type']){
                    case self::INTERVAL_RULES_MARKET_ALL:
                        //$ddeliveryPointSelf->price = 0;
                        break;
                    case self::INTERVAL_RULES_MARKET_PERCENT:
                        //$ddeliveryPointSelf->price = $price - ($price / 100 * $interval['amount']);
                        break;
                    case self::INTERVAL_RULES_MARKET_AMOUNT:
                        if($price < $interval['amount']) {
                            //$ddeliveryPointSelf->price = 0;
                        }else{
                            //$ddeliveryPointSelf->price = $price < $interval['amount'];
                        }
                        break;
                    case self::INTERVAL_RULES_CLIENT_ALL:
                }
            }
        }
        return $ddeliveryPointSelf;
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
    public function getIntervalsByPointSelf()
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