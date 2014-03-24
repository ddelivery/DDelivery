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

use DDelivery\Adapter\DShopAdapterImpl;
/**
 * DDeliveryOrder - заказ DDelivery
 *
 * @package     DDelivery.Order
 */
class DDeliveryOrder
{
	private $params = array( 'id' => 0 );

	private $type;
	
	protected  $allowParams = array();
	
	/**
	 * сервер по умолчанию
	 * @var array DDeliveryProduct
	 */
	private $productList = array();
	
	/**
	 * Адаптер CMS магазина
	 * @var DShopAdapterImpl
	 */
	private $shop;
	
	/**
	 * точка для
	 * @var DDeliveryPoint
	 */
	private $point;
	
	private $user;
	
	/**
	 * @param DShopAdapterImpl $shop
	 */
	public function __construct( $shop )
	{
		$this->shop =  $shop;
		
		$products = $this->shop->getProductsFromCart();
		
		if( count($products) > 0)
		{
            foreach ( $products as $p )
            {
            	$this->productList = new DDeliveryProduct( $p['id'], $p['width'],
            			                                   $p['height'], $p['length'], 
            			                                   $p['weight'], $p['price'] );
            }
		}
		else 
		{
			throw new DDeliveryOrderException("Корзина пуста");
		}
		
	}
	
	
	/*
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
	*/
}     