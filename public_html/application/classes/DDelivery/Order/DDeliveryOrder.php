<?php
/**
 * 
 * @package    DDelivery.Order
 *
 * @author  mrozk 
 */
namespace DDelivery\Order;

use DDelivery\Adapter\DShopAdapter;
use DDelivery\DDeliveryException;
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
     * Id компании доставки
     * @var int
     */
    public $companyId;

    /**
     * Тип если самовывоз - 1, если курьерка - 2
     * @var int
     */
    public $type;
    /**
     * @var заказ подтвержден
     */
    public $confirmed = 0;

    /**
     * @var int сторона 3 (см)
     */
    public  $dimensionSide1 = 0;
    /**
     * @var int сторона 2 (см)
     */
    public $dimensionSide2 = 0;
    /**
     * @var int сторона 3 (см)
     */
    public $dimensionSide3 = 0;

    /**
     * @var float вес заказа
     */
    public $weight = 0;
    
    /**
     * @var int город
     */
    public $city = 0;
    /**
     * @var float сумма предоставляемая корзиной
     */
    public $amount;
    /**
     * @var float страховка товара
     */
    public $declaredPrice;
    /**
     * @var float сума к оплате на точке
     */
    public $paymentPrice;
    
    /**
     * @var string id заказа на стороне CMS обязательна для курьерки и для самовывоза
     */
    public $shopRefnum;
    /**
     * @var string ФИО
     */
    public $toName;
    
    /**
     * @var string имя
     */
    public $firstName;
    
    /**
     * @var string фамилия
     */
    public $secondName = '';
    /**
     * @var string телефон
     */
    public $toPhone;
    
    /**
     * @var string email
     */
    public $toEmail;


    /**
     * @var string индекс
     */
    public $toIndex;

    /**
     * @var string улица
     */
    public $toStreet;
    
    /**
     * @var string номер дома
     */
    public $toHouse;
    
    /**
     * @var string номер квартиры
     */
    public $toFlat;
    /**
     * @var string Корпус дома
     */
    public $toHousing;
    
    /**
     * @var string описание заказа 
     */
    public $goodsDescription;
    
    /**
     * @var string id продуктов в заказе
     */
    public $productIDs;

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
     * 
     * @var int id способа оплаты
     */
    public $paymentVariant = null;
    
    /**
     *
     * @var string  статус заказа на стороне CMS
     * 
     */
    public $localStatus;
    /**
     *
     * @var string id статус заказа на стороне DD
     *
     */
    public $ddStatus = 0;
    /**
     *
     * @var int id заказа на стороне сервера ddelivery
     */
    public $ddeliveryID = 0;


    public $comment;

    /**
     * @var String - символическое представление города
     */
    public $cityName = null;

    /**
     * @var DDeliveryOrderCache - кеш в контексте заказа
     */
    public $orderCache;

    /**
     * @var DDeliveryOrderCache - кеш в контексте заказа
     */
    public $pointID;

    /**
     *   дополнительное поле 1
     */
    public $addField1 = null;

    /**
     *   дополнительное поле 2
     */
    public $addField2 = null;

    /**
     *   дополнительное поле 3
     */
    public $addField3 = null;

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
        $this->orderCache = new DDeliveryOrderCache();
    }

    public function getCacheValue( $field, $sig ){
        if( ($this->orderCache->sig == $sig) && ( $this->orderCache->$field != null ) ){
            return $this->orderCache->$field;
        }
        return false;
    }

    public function setCacheValue( $field, $sig, $value ){
        if( $this->orderCache->sig == $sig ){
            $this->orderCache->$field = $value;
        }else{
            $this->orderCache = new DDeliveryOrderCache();
            $this->orderCache->sig = $sig;
            $this->orderCache->$field = $value;
        }
    }

    /**
     *
     * получить параметры товара
     *
     * Сторона 1: сумма минимальных сторон товаров в Заказе
     * Сторона 2: максимальная сторона товара в Заказе
     * Сторона 3: следующая за максимальной стороной товара в Заказе
     *
     * @throws DDeliveryOrderException
     * @return void
     */
    public function getProductParams()
    {
        $items = array();
        $description = array();
        $productIDs = array();
        $weight = 0;
        $dimensionSide1 = 0;
        $dimensionSide2 = 0;
        $dimensionSide3 = 0;
        if(!empty($this->productList)){
            foreach ($this->productList as $product) {
                $description[] = $product->getName() . ' ' . $product->getQuantity() . 'шт.';
                $weight += ($product->getQuantity() * $product->getWeight());
                $currentSizes = array( $product->getWidth(), $product->getHeight(), $product->getLength()  );
                sort($currentSizes);
                $minValue = array_shift($currentSizes);
                $dimensionSide1 += ( $minValue * $product->getQuantity() );
                $items = array_merge($items, $currentSizes);
                array_push($productIDs, $product->getId());
            }
            sort($items);

            $dimensionSide2 = array_pop( $items );
            $dimensionSide3 = array_pop( $items );
        }else{
            throw new DDeliveryOrderException("Корзина пуста");
        }
        $this->weight = $weight;
        $this->goodsDescription = implode( ', ', $description );
        $this->dimensionSide1 = $dimensionSide1;
        $this->dimensionSide2 = $dimensionSide2;
        $this->dimensionSide3 = $dimensionSide3;
        $this->productIDs = implode(',', $productIDs);
    }

    public function getJsonOrder(){
        $json = array();
        if(!empty($this->productList)){
            foreach ($this->productList as $product) {
                $json[] = array('name' => $product->getName(), 'article' =>$product->getSku(),
                                'count' =>  $product->getQuantity());
            }
            return json_encode( $json );
        }else{
            throw new DDeliveryOrderException("Корзина пуста");
        }
    }

    /**
     *
     * Упаковать продукты заказа для сохранения в БД
     *
     * @return string
     */
    public function getSerializedProducts()
    {
        return serialize( $this->productList );
    }

    public function getAmount(){
        $amount = 0.;
        foreach($this->productList as $product) {
            $amount += $product->getPrice() * $product->getQuantity();
        }
        return $amount;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getToIndex(){
        return $this->toIndex;
    }
    
    /**
     * @param  $point
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
    	return $this->confirmed;
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
    	return trim($this->firstName . ' ' . $this->secondName);
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

    public function getFullAddress()
    {
        $return = $this->toStreet;
        if($this->toHouse)
            $return .= ' д.'.$this->toHouse;
        if($this->toHousing)
            $return .= ' корп.'.$this->toHousing;
        if($this->toFlat)
            $return .= ' кв.'.$this->toFlat;

        return $return;
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

    /**
     * @param string $toHosting
     */
    public function setToHousing($toHosting)
    {
        $this->toHousing = $toHosting;
    }

    /**
     * @return string
     */
    public function getToHousing()
    {
        return $this->toHousing;
    }

}     