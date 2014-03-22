<?php
/**
 *
 * @package    DDelivery.Order
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */
namespace DDelivery\Order;


abstract  class DDeliveryOrder
{
	private $params = array( 'id' => 0 );
	
	protected  $allowParams = array();
	
	private $productList;
	
	
	
	public function __construct( $initParams = array() )
	{
		if(is_array($initParams))
		{
			foreach ( $initParams as $key=>$value)
			{
				$this->set($key, $value);
			}
		}
	}
	
	public function pack()
	{
		foreach ($this->allowParams as $p)
		{	
			if ( !array_key_exists( $p, $this->params ) )
			{
				throw new DDeliveryOrderException("Order params not full");
			}
		}
		return $this->params;
	}
	
	public function set( $paramName, $paramValue)
	{
		if( array_key_exists($paramName, $this->allowParams) )
		{
			$this->params[$paramName] = $paramValue;
		}
		else
		{
			throw new DDeliveryOrderException("Order param not found");
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
}     