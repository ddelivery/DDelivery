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
     * @param int $id id заказа в системе и-нет магазина
     * @param float $width длинна
     * @param float $height высота
     * @param float $length ширина
     * @param float $weight вес
     * @param float $price стоимостьв рублях
     * @param int $quantity количество товара
     */
    public function __construct( $params = array() )
    {
    	$this->id =       $params['id'];
    	$this->width =    $params['width'];
    	$this->heigth =   $params['height'];
    	$this->length =   $params['length'];
    	$this->weigth =   $params['weight'];
    	$this->price =    $params['price'];
    	$this->quantity = $params['quantity'];
    	
    }


    /**
     * Получаем наибольшую сторону товара
     * @todo Чет у мя сомнения по поводу этой штуки, а функция getCurrentMinParameterValue работает подруому
     * @return array
     */
    public function getCurrentMaxParameterValue()
    {
    	$max = 0;
    	if( !$this->widthaccess && ( $max < $this->getWidth() ) )
    	{
    		$max = $this->getWidth();
    		$access =  'widthaccess';
    	}
    	
    	if( !$this->heigthaccess && ( $max < $this->getHeight() ) )
    	{
    		$max = $this->getHeight();
    		$access =  'heigthaccess';
    	}
    	
    	if( !$this->lengthaccess && ( $max < $this->getLength() ) )
    	{
    		$max = $this->getLength();
    		$access =  'lengthaccess';
    	}
    	
    	return array('access' => $access, 'max' => $max ); 
    }

    /**
     * Получаем наименьшую сторону товара
     * @return float
     */
    public function getCurrentMinParameterValue()
    {
    	$min = $this->getWidth();
    	$access =  'widthaccess';
    	if( $min > $this->getHeight() )
    	{
    		$min = $this->getHeight();
    		$access =  'heigthaccess';
    	}
    	if( $min > $this->getLength() )
    	{
    		$min = $this->getLength();
    		$access =  'lengthaccess';
    	}
    	$this->$access = 1;
    	
    	return $min;
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
    	return $this->heigth;
    }
    
    public function getLength()
    {
    	return $this->length;
    }
    
    public function getWeight()
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
    
}    	
