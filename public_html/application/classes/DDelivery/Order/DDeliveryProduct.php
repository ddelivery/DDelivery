<?php
/**
 *
* Товар
* @package    DDelivery.Order
* @author  mrozk 
*/
namespace DDelivery\Order;

/**
 * Class DDeliveryProduct объектное представление товара
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
     * количество единицы товара
     * @var string
     */
    private $sku;

    /**
     * @param int $id id товара в системе и-нет магазина
     * @param float $width длинна
     * @param float $height высота
     * @param float $length ширина
     * @param float $weight вес
     * @param float $price стоимостьв рублях
     * @param int $quantity количество товара
     * @param string $name Название вещи
     */
    public function __construct( $id, $width, $height, $length,
                                 $weight, $price, $quantity, $name, $sku )
    {
        $this->id = (int)$id;
        $this->width = (float)$width;
        $this->height = (float)$height;
        $this->length = (float)$length;
        $this->weigth = (float)$weight;
        $this->price = (float)$price;
        $this->quantity = (int)$quantity;
        $this->name = $name;
        $this->sku = $sku;
    }
    
   

    /**
     * @return int
     */
    public function getId(){
    	return $this->id;
    }

    /**
     * @return string
     */
    public function getName(){
    	return $this->name;
    }

    /**
     * @return float
     */
    public function getWidth(){
    	return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight(){
    	return $this->height;
    }

    /**
     * @return float
     */
    public function getLength(){
    	return $this->length;
    }

    /**
     * @return float
     */
    public function getWeight(){
    	return $this->weigth;
    }

    /**
     * @return float
     */
    public function getPrice(){
    	return $this->price;
    }


    /**
     * @return float
     */
    public function getSku(){
        return $this->sku;
    }

    /**
     * @return int
     */
    public function getQuantity(){
    	return $this->quantity;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = (float)$height;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param float $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param float $weigth
     */
    public function setWeigth($weigth)
    {
        $this->weigth = $weigth;
    }

    /**
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
}    	
