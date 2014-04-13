<?php
/**
 *
 * @package    DDelivery.Point
 *
 * @author  mrozk
 */

namespace DDelivery\Point;

/**
 * DDeliveryPointCourier - компания 
 * для курьерской доставки
 * 
 * @package  DDelivery.Point
 *
 * @property int $delivery_company
 * @property string $delivery_company_name
 * @property int $pickup_price
 * @property int $delivery_price_fee
 * @property int $declared_price_fee
 * @property int $delivery_time_min
 * @property int $delivery_time_max
 * @property int $delivery_time_avg
 * @property int $return_price
 * @property int $return_client_price
 * @property int $return_partial_price
 * @property int $total_price
 * @property int $delivery_price

 */
class DDeliveryPointCourier extends DDeliveryAbstractPoint {

    /**
     * @param $name
     * @return string
     */
    function __get($name)
    {
        return $this->deliveryInfo->get($name);
    }

}