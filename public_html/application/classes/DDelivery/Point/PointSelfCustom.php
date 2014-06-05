<?php
/**
 * User: DnAp
 * Date: 03.06.14
 * Time: 17:42
 */

namespace DDelivery\Point;
use DDelivery\Point\DDeliveryInfo;

class PointSelfCustom extends DDeliveryPointSelf {
    public function __construct($id, $company, $address, $latitude, $longitude, $totalPrice, $deliveryTimeMin,
                                $schedule, $descriptionIn = '', $descriptionOut = '' )
    {
        $this->is_custom = true;
        $this->_id = $id;
        $this->company = $company;
        $this->address = $address;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->schedule = $schedule;
        $this->description_in = $descriptionIn;
        $this->description_out = $descriptionOut;
        $this->is_card = 1;
        $this->is_cash = 1;
        $this->setDeliveryInfo(new DDeliveryInfo(array('total_price' => $totalPrice, 'pickup_price'=>0, 'delivery_time_min'=>$deliveryTimeMin)));
    }
}