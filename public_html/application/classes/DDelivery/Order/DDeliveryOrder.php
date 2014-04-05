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

use DDelivery\Adapter\DShopAdapter;
use DDelivery\Point\DDeliveryAbstractPoint;

/**
 * DDeliveryOrder - заказ DDelivery
 *
 * @package     DDelivery.Order
 */
class DDeliveryOrder
{
    /**
     * @var int
     */
    protected $_id;
    /**
     * Тип если самовывоз - 1, если курьерка - 2
     * @var int
     */
    public $type;
    /**
     * @var bool
     */
    public $confirmed = false;

    /**
     * @var int
     */
    public  $dimensionSide1 = 0;
    /**
     * @var int
     */
    public $dimensionSide2 = 0;
    /**
     * @var int
     */
    public $dimensionSide3 = 0;
    /**
     * @var int
     */
    protected $weight = 0;
    
    /**
     * @var int
     */
    public $city = 0;

    /**
     * @var float
     */
    public $declaredPrice;
    /**
     * @var float
     */
    public $paymentPrice;
    
    /**
     * @var string
     */
    public $shopRefnum;
    /**
     * @var string
     */
    public $toName;
    /**
     * @var string
     */
    public $toPhone;
    
    /**
     * @var string
     */
    public $toEmail;
    
    /**
     * @var string
     */
    public $toStreet;
    
    /**
     * @var string
     */
    public $toHouse;
    
    /**
     * @var string
     */
    public $toFlat;
    
    /**
     * @var string
     */
    public $goodsDescription;


    /**
     * Лист товаров
     * @var DDeliveryProduct[] DDeliveryProduct
     */
    private $productList = array();

    /**
     * точка самовывоза или курьерская служба
     * @var DDeliveryAbstractPoint
     */
    private $point = null;

    /**
     * @var
     */
    private $user;

    /**
     * @param DDeliveryProduct[] $productList
     * @throws DDeliveryOrderException
     */
    public function __construct($productList)
    {
        /**
         * Возвращает сразу массив DDeliveryProduct
         */
        $this->productList = $productList;
        
        if (count( $this->productList ) == 0)
        {
            throw new DDeliveryOrderException("Корзина пуста");
        }
        
        // Получаем параметры для товаров в заказе
        $this->getProductParams();
		
    }
    
    /**
     * 
     * получить параметры товара
     * 
     */
    public function getProductParams()
    {
        $items = array();
        
        $description = array();
        
        $productSizes = array();
        
        $this->weight = 0;

        foreach ($this->productList as $product) {
        	
        	$description[] = $product->getName() . ' ' . $product->getQuantity() . 'шт.';
        	
            $this->weight += ($product->getQuantity() * $product->getWeight());

            $sizes =  array($product->getWidth(), $product->getHeight(), 
            		        $product->getLength());
            sort($sizes);
            $sizes[] = $product->getQuantity();
            
            for($i=0;$i<$product->getQuantity();$i++) {
                $items[] = $sizes;
            }
            
            $productSizes[] = $sizes;
        }
        /*
        $dimensionSide1 = 0;
        $dimensionSide2 = 0;
        $dimensionSide3 = 0;
        print_r($productSizes);
        foreach($items as $item) {
            $dimensionSide1 += $item[0];
            if($dimensionSide2 < $item[2]) {
                $dimensionSide2 = $item[2];
            }
            if($dimensionSide3 < $item[1]) {
                $dimensionSide3 = $item[1];
            }
        }
        */
        $dimensionSide1 = 0;
        $dimensionSide2 = 0;
        $dimensionSide3 = 0;
        
        for ($i = 0; $i < count( $productSizes );$i++ )
        {
        	$dimensionSide1 += ($productSizes[$i][0] * $productSizes[$i][3] );
			
			     	
        	if( $productSizes[$i][1] > $dimensionSide2 )
        	{
        		$dimensionSideIndex = $i;
        		$dimensionSideElement = 1;
        		$dimensionSide2 = $productSizes[$i][1];
        	}
        	if( $productSizes[$i][2] > $dimensionSide2 )
        	{
        		$dimensionSideIndex = $i;
        		$dimensionSideElement = 2;
        		$dimensionSide2 = $productSizes[$i][2];
        	} 
        	
        }
        $dimensionSide2 = $productSizes[$dimensionSideIndex][$dimensionSideElement];
        for ($i = 0; $i < count( $productSizes );$i++ )
        {
        	if( ( $productSizes[$i][1] > $dimensionSide3) && 
        	      !(($i == $dimensionSideIndex) && ($dimensionSideElement == 1))  )
        	{	
        		
        		$dimensionSideIndex3 = $i;
        		$dimensionSideElement3 = 1;
        		$dimensionSide3 = $productSizes[$i][1];
        	}
        	if( ($productSizes[$i][2] > $dimensionSide3)  && 
        	     !(($i == $dimensionSideIndex) && ($dimensionSideElement == 2)) )
        	{
        		$dimensionSideIndex3 = $i;
        		$dimensionSideElement3 = 2;
        		$dimensionSide3 = $productSizes[$i][2];
        	}
        }
        $dimensionSide3 = $productSizes[$dimensionSideIndex3][$dimensionSideElement3];
		
        $this->goodsDescription = implode( ', ', $description );
        
        $this->dimensionSide1 = $dimensionSide1;
        $this->dimensionSide2 = $dimensionSide2;
        $this->dimensionSide3 = $dimensionSide3;
    }
	
    public function packOrder()
    {
    	
    	$s = serialize($this);
    	return $s;
    }
    
    /**
     * @param $point
     */
    public function setPoint( $point )
    {
        $this->point = $point;    	
    }

    /**
     * @return DDeliveryAbstractPoint|null
     */
    public function getPoint()
    {
    	if( $this->point != null)
    	{
    		return $this->point;
    	}
    	return null;
    }

    /**
     * @return DDeliveryProduct[]
     */
    public function getProducts()
    {
        return $this->productList;
    }

    /**
     * @return int
     */
    public function getDimensionSide1()
    {
    	return $this->dimensionSide1;
    }

    /**
     * @return int
     */
    public function getDimensionSide2()
    {
    	return $this->dimensionSide2;
    }

    /**
     * @return int
     */
    public function getDimensionSide3()
    {
    	return $this->dimensionSide3;
    }
	
    /**
     * @return string
     */
    public function getGoodsDescription()
    {
    	return $this->goodsDescription;
    }
    
    /**
     * @return float
     */
    public function getWeight()
    {
    	return $this->weight;
    }
    
    public function setConfirmed( $confirmed )
    {
    	$this->confirmed = $confirmed;
    }
    
    /**
     * @return boolean
     */
    public function getConfirmed()
    {
    	return $this->weight;
    }
    
    public function setToPhone( $phone )
    {
    	$this->toPhone = $phone;
    }
    
    public function getToPhone()
    {
    	return $this->toPhone;
    }
    
    public function setToName( $name )
    {
    	$this->toName = $name;
    }
    
    public function getToName()
    {
    	return $this->toName;
    }
    
    public function getToStreet()
    {
    	return $this->toStreet;
    }
    
    public function setToStreet( $toStreet )
    {
    	$this->toStreet = $toStreet;
    }
    
    public function setToHouse( $toHouse )
    {
    	$this->toHouse = $toHouse;
    }
    
    public function getToHouse()
    {
    	return $this->toHouse;
    }
    
    public function setToFlat( $toFlat )
    {
    	$this->toFlat = $toFlat;
    }
    
    public function getToFlat()
    {
    	return $this->toFlat;
    }

    /**
     * @param string $toEmail
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
    }

    /**
     * @return string
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

}     