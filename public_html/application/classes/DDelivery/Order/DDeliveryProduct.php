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
	private $heigth;
	
	/**
	 * длина товара в м
	 * @var float
	 */
	private $length;
	
	/**
	 * вес товара в м
	 * @var float
	 */
	private $weigth;
	
	/**
	 * цена товара
	 * @var float
	 */
	private $price;
	
	
	/**
	 * количество единицы товара
	 * @var int
	 */
	private $quantity;
	
	/**
	 * Доступ к ширине еденицы товара
	 * @var int
	 */
	public $widthaccess = 0;
	
	/**
	 * Доступ к высоте еденицы товара
	 * @var int
	 */
	public $heigthaccess = 0;
	
	/**
	 * Доступ к длине еденицы товара
	 * @var int
	 */
	public $lengthaccess = 0;
	
	
	/**
	 * @param int $id
	 * @param float $width
	 * @param float $height
	 * @param float $length
	 * @param float $weight
	 * @param float $price
	 */
    public function __construct( $id, $width, $heigth, $length, 
                                 $weigth, $price, $quantity )
    {
    	$this->id = $id;
    	$this->width = $width;
    	$this->heigth = $heigth;
    	$this->length = $length;
    	$this->weigth = $weigth;
    	$this->price = $price;
    	$this->quantity = $quantity;
    	
    }
    
    public function getID()
    {
    	return $this->id;
    }
    
    public function getWidth()
    {
    	return $this->width;
    }
    
    public function getHeigth()
    {
    	return $this->heigth;
    }
    
    public function getLength()
    {
    	return $this->length;
    }
    
    public function getWeigth()
    {
    	return $this->weigth;
    }
    
    public function getPrice()
    {
    	return $this->price;
    }
    
    public function getQuantity()
    {
    	return $this->quantity;
    }
    /*
    public function getWidthAccess()
    {
    	return $this->widthaccess;
    }
    
    public function setWidthAccess()
    {
    	return $this->widthaccess;
    }
    
    public function getHeigthAccess()
    {
    	return $this->widthaccess;
    }
    
    public function setHeigthAccess()
    {
    	return $this->heigthaccess;
    }
    */
}    	
