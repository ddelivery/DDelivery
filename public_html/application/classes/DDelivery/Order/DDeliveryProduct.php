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
/**
 * Class DDeliveryProduct
 * @package DDelivery\Order
 */
class DDeliveryProduct
{	
	/**
	 * id товара
	 * @var int
	 */
	private $id;
	
	/**
	 * название товара
	 * @var string
	 */
	private $name;
	
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
     * @param int $id id заказа в системе и-нет магазина
     * @param float $width длинна
     * @param float $height высота
     * @param float $length ширина
     * @param float $weight вес
     * @param float $price стоимостьв рублях
     * @param int $quantity количество товара
     * @param string $name Название вещи
     */
    public function __construct( $id, $width, $height, $length,
                                 $weight, $price, $quantity, $name )
    {
        $this->id = $id;
        $this->width = $width;
        $this->heigth = $height;
        $this->length = $length;
        $this->weigth = $weight;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getID()
    {
    	return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
    	return $this->name;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
    	return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
    	return $this->heigth;
    }

    /**
     * @return float
     */
    public function getLength()
    {
    	return $this->length;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
    	return $this->weigth;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
    	return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
    	return $this->quantity;
    }
    
}    	
