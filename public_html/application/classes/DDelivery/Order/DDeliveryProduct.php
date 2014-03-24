<?php
/**
 *
* Товар
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
 * DDeliveryProduct - объектное представление товара
 *
 * @package     DDelivery.Order
 */
class DDeliveryProduct
{	
	/**
	 * id товара
	 * @var int
	 */
	private $id;
	
	/**
	 * ширина товара в м
	 * @var float
	 */
	private $width;
	
	/**
	 * высота товара в м
	 * @var float
	 */
	private $height;
	
	/**
	 * длина товара в м
	 * @var float
	 */
	private $length;
	
	/**
	 * вес товара в м
	 * @var float
	 */
	private $weight;
	
	/**
	 * цена товара
	 * @var float
	 */
	private $price;
	
	
	/**
	 * @param int $id
	 * @param float $width
	 * @param float $height
	 * @param float $length
	 * @param float $weight
	 * @param float $price
	 */
    public function __construct( $id, $width, $height, $length, 
                                 $weight, $price )
    {
    	$this->id = $id;
    	$this->width = $width;
    	$this->height = $height;
    	$this->length = $length;
    	$this->weight = $weight;
    	$this->price = $price;
    }
    
    public function getID()
    {
    	return $this->id;
    }
    
    public function getWidth()
    {
    	return $this->width;
    }
    
    public function getHeight()
    {
    	return $this->height;
    }
    
    public function getLenght()
    {
    	return $this->length;
    }
    
    public function getWeight()
    {
    	return $this->weight;
    }
    
    public function getPrice()
    {
    	return $this->price;
    }
}    	
