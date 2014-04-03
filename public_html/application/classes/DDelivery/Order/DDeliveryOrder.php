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
    protected $dimensionSide1 = 0;
    /**
     * @var int
     */
    protected $dimensionSide2 = 0;
    /**
     * @var int
     */
    protected $dimensionSide3 = 0;
    /**
     * @var int
     */
    protected $weight = 0;

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
    public $toName;
    /**
     * @var string
     */
    public $toPhone;
    
    
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

    public function getProductParams()
    {
        $items = array();
        
        $description = array();
        
        $this->weight = 0;

        foreach ($this->productList as $product) {
        	
        	$description[] = $product->getName() . ' ' . $product->getQuantity() . 'шт.';
        	
            $this->weight += ($product->getQuantity() * $product->getWeight());

            $sizes =  array($product->getWidth(), $product->getHeight(), $product->getLength());
            sort($sizes);
            for($i=0;$i<$product->getQuantity();$i++) {
                $items[] = $sizes;
            }
        }

        $dimensionSide1 = 0;
        $dimensionSide2 = 0;
        $dimensionSide3 = 0;
        foreach($items as $item) {
            $dimensionSide1 += $item[0];
            if($dimensionSide2 < $item[1]) {
                $dimensionSide2 = $item[1];
            }
            if($dimensionSide3 < $item[2]) {
                $dimensionSide3 = $item[2];
            }
        }
		
        $this->goodsDescription = implode( ', ', $description );
        
        $this->dimensionSide1 = $dimensionSide1;
        $this->dimensionSide2 = $dimensionSide2;
        $this->dimensionSide3 = $dimensionSide3;
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

}     