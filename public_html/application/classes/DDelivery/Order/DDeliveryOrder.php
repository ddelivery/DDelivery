<?php
/**
 * 
 * @package    DDelivery.Order
 *
 * @author  mrozk 
 */
namespace DDelivery\Order;

use DDelivery\Adapter\DShopAdapter;
use DDelivery\Point\DDeliveryAbstractPoint;
use DDelivery\DataBase\SQLite;

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
     * Локальный id для хранилища
     * @var int
     */
    public $localId;
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
    public $amount;
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
    
    
    public $secondName;
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
	
    
    public $paymentVariant = null;

    /**
     * @param DDeliveryProduct[] $productList
     * @throws DDeliveryOrderException
     */
    public function __construct( $productList )
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
     * Сторона 1: сумма минимальных сторон товаров в Заказе
     * Сторона 2: максимальная сторона товара в Заказе
     * Сторона 3: следующая за максимальной стороной товара в Заказе 
     * 
     * @return void
     */
    public function getProductParams()
    {
        $items = array();
        $description = array();
        $weight = 0;
        $dimensionSide1 = 0;
        $dimensionSide2 = 0;
        $dimensionSide3 = 0;
        
        foreach ($this->productList as $product) {
        	$description[] = $product->getName() . ' ' . $product->getQuantity() . 'шт.';
        	$weight += ($product->getQuantity() * $product->getWeight());
        	$currentSizes = array( $product->getWidth(), $product->getHeight(), $product->getLength()  );
        	sort($currentSizes);
        	$minValue = array_shift($currentSizes);
        	$dimensionSide1 += ( $minValue * $product->getQuantity() );
        	$items = array_merge($items, $currentSizes);
        }
        sort($items);
        $dimensionSide2 = array_pop( $items );
        $dimensionSide3 = array_pop( $items );
		
        $this->weight = $weight;
        $this->goodsDescription = implode( ', ', $description );
        $this->dimensionSide1 = $dimensionSide1;
        $this->dimensionSide2 = $dimensionSide2;
        $this->dimensionSide3 = $dimensionSide3;
    }
	
    /**
     *
     * Упаковать данные заказа для сохранения в БД
     * 
     * @return array
     */
    public function packOrder()
    {	
    	$point = $this->getPoint();
    	$checkSum = md5( $this->goodsDescription );
    	$pointID = 0;
    	$pointPacked = '';
    	
    	if( !empty( $point ) )
    	{
    		$pointPacked = serialize($point);
            $pointID = $this->point->pointID;
    	}
    	
    	$packedOrder = array('type'=>$this->type, 'city' => $this->city, 
    	                     'point_id' => $pointID, 'to_name' => $this->toName,
    	                     'to_phone' => $this->toPhone, 'to_street' => $this->toStreet,
                             'to_house' => $this->toHouse, 'to_flat' => $this->toFlat, 'to_email' => $this->toEmail,
    						  'point' => $pointPacked, 'checksum' => $checkSum );
    	
    	return $packedOrder;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    
    /**
     * @param DDeliveryAbstractPoint $point
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
	
    
    public function setProducts( $productList )
    {
    	$this->productList = $productList;
    	$this->getProductParams();
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