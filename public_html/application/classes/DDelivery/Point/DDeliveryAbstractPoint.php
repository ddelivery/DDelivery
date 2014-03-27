<?php
/**
 * User: DnAp
 * Date: 26.03.14
 * Time: 22:26
 */

namespace DDelivery\Point;


abstract class DDeliveryAbstractPoint {
	
    protected  $params = array();
    
    protected  $allowParams;
	
	protected $deliveryInfo = null;
	
	public function __construct( $initParams = array() )
	{
		if(is_array($initParams))
		{
			foreach ( $initParams as $key => $value)
			{
				try
				{
					$this->set($key, $value);
				}
				catch (DDeliveryPointException $e)
				{
					echo $e->getMessage();
				}
			}
		}
	}
	
	public function set( $paramName, $paramValue)
	{
		 
		if( in_array($paramName, $this->allowParams) )
		{
			$this->params[$paramName] = $paramValue;
		}
		else
		{
			throw new DDeliveryPointException("Point param not found");
		}
	}
	
	public function get( $paramName )
	{
		if( array_key_exists($paramName, $this->params) )
		{
			return 	$this->params[$paramName];
		}
		return null;
	}
	
	public function setDeliveryInfo( $deliveryInfo )
	{
		$this->deliveryInfo = $deliveryInfo;
	}
	
	public function getDeliveryInfo()
	{
		return $this->deliveryInfo;
	}

} 
