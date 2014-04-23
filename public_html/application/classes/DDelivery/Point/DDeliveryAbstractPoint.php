<?php
/**
 * User: mrozk
 * Date: 29.03.14
 * Time: 00:00
 */

namespace DDelivery\Point;

/**
 * Class DDeliveryAbstractPoint
 * @package DDelivery\Point
 */
abstract class DDeliveryAbstractPoint {
	
	/**
	 * Инфа по ID точки
	 * 
	 * Для компаний типа самовывоз - это id точки
	 * Для компаний типа доставки курьером это id компании
	 * 
	 * @var int pointID
	 */ 
	public $pointID;
	
	
	/**
	 * Информация по доставке для данной компании
	 * @var DDeliveryInfo
	 */
	protected $deliveryInfo = null;

    /**
     * Точка создана интегратором
     * @var bool $isCustom
     */
    public $isCustom;

    /**
     * Точка создана интегратором
     * @param bool $isCustom
     */
    public function __construct( $isCustom = true )
    {
        $this->isCustom = $isCustom;
    }

    /**
     * @param $deliveryInfo
     */
    public function setDeliveryInfo( $deliveryInfo )
	{
		$this->deliveryInfo = $deliveryInfo;
	}

    /**
     * @return DDeliveryInfo
     */
    public function getDeliveryInfo()
	{
		return $this->deliveryInfo;
	}

} 
