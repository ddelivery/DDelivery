<?php
/**
 * User: mrozk
 * Date: 29.03.14
 * Time: 00:00
 */

namespace DDelivery\Point;

/**
 * DDeliveryAbstractPoint
 * @package DDelivery.Point
 */
abstract class DDeliveryAbstractPoint {
	
	/**
	 * Информация по доставке для данной компании
	 * @var DDeliveryInfo
	 */
	protected $deliveryInfo = null;
	
	
	public function setDeliveryInfo( $deliveryInfo )
	{
		$this->deliveryInfo = $deliveryInfo;
	}
	
	public function getDeliveryInfo()
	{
		return $this->deliveryInfo;
	}

} 
