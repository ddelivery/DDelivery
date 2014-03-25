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

/**
 * DDeliveryOrder - заказ DDelivery
 *
 * @package     DDelivery.Order
 */
class DDeliveryOrder
{
	private $params = array( 'id' => 0 );
	
	private $dimensionSide1 = null;
	
	private $dimensionSide2 = null;
	
	private $dimensionSide3 = null;
	
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
            	$this->productList[] = new DDeliveryProduct( $p['id'], $p['width'],
            			                                     $p['height'], $p['length'], 
            			                                     $p['weight'], $p['price'],
                                                             $p['quantity'] );
            }
		}
		else 
		{
			throw new DDeliveryOrderException("Корзина пуста");
		}
		
	}
	function getOrderInfo()
	{
		$width = 'width';
		$height = 'height';
		$length = 'length';
		$weight = 'weight';
		
		$width_access = $width . '_access';
		$height_access = $height . '_access';
		$length_access = $length . '_access';
		$weight = $weight . '_access';
		
		$total_weight = 0;
		$sum_of_min = 0;
		$max = 0;
		$max_key = -1;
		
		foreach ( $this->productList as $product )
		{	
			// находим 1 сторону
			$min = $product->getWidth();
			$alias = 'width';
			
			if( $min > $product->getHeigth() )
			{
				$min = $product->getHeigth();
				$alias = 'height';
			}
			
			if( $min > $product->getLength())
			{
				$min = $product->getLength();
				$alias = 'length';
			}
			
			$alias_access = $alias  . "access";
			$product->$alias_access  =  1;
			$sum_of_min += (($min) * $product->getQuantity());
			// находим 1 сторону
			
			// находим 2 сторону
			
			if( $product->$width_access == 0 )
			{
				if( $max < $product->getWidth() )
				{
                    $max = $product->getWidth();
                    
				}
			}
			// находим 2 сторону
			
			// находим вес
			$total_weight +=  ( $product->getQuantity() * $product->getWeigth() );
			// находим вес
			
		}
		echo $total_weight;
	}
	function getProducts()
    {
    	return $this->productList;		
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