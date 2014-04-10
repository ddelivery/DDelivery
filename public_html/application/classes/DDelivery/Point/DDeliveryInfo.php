<?php
/**
*
* @package    DDelivery.Point
*
* @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
*
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*
* @author  mrozk <mrozk2012@gmail.com>
*/
namespace DDelivery\Point;


/**
 * DDeliveryInfo - Информация по доставке на точку
 *
 * @package  DDelivery
 */
class DDeliveryInfo
{
	private $params = array();
	
	private  $allowParams = array('delivery_company', 'delivery_company_name', 
			'pickup_price', 'delivery_price', 'delivery_price_fee',
			'declared_price_fee', 'delivery_time_min', 'delivery_time_max', 
			'delivery_time_avg', 'return_price', 'return_client_price', 'return_partial_price', 
			'total_price');
	
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
			throw new DDeliveryPointException("Point info param not found");
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

    function __get($name)
    {
        return $this->get($name);
    }

    function __set($name, $value)
    {
        $this->set($name, $value);
    }
}