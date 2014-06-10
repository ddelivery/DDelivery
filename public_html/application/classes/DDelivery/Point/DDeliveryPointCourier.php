<?php
/**
 *
 * @package    DDelivery.Point
 *
 * @author  mrozk
 */

namespace DDelivery\Point;

/**
 * DDeliveryPointCourier - компания для курьерской доставки
 * 
 * @package  DDelivery.Point
 *
 * @property int $delivery_company ID компании доставки
 * @property string $delivery_company_name Буквенное название компании доставки
 * @property int $pickup_price сколько стоит забрать товар
 * @property int $delivery_price_fee наценка DD
 * @property int $declared_price_fee размер страховки
 * @property int $delivery_time_min минимальное время доставки
 * @property int $delivery_time_max максимальное время доставки
 * @property int $delivery_time_avg
 * @property int $return_price
 * @property int $return_client_price
 * @property int $return_partial_price
 * @property int $total_price
 * @property int $delivery_price цена доставки

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

    function __set($name, $value)
    {
        $this->deliveryInfo->set($name, $value);
    }

    /**
     * Возвращает только информацию которая может понадобиться на карте
     * @return array
     */
    public function toJson()
    {
        $params = array('delivery_company', 'delivery_company_name', 'delivery_time_min', 'delivery_time_max', 'total_price');
        $result = array();
        foreach($params as $param){
            $result[$param] = $this->__get($param);
        }
        return $result;
    }
}