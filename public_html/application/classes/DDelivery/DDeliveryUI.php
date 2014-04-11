<?php
/**
*
* @package    DDelivery
*
* @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
*
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*
* @author  mrozk <mrozk2012@gmail.com>
*/
namespace DDelivery;
use DDelivery\Adapter\DShopAdapter;
use DDelivery\DataBase\City;
use DDelivery\DataBase\Order;
use DDelivery\DataBase\SQLite;
use DDelivery\Point\DDeliveryPointSelf;
use DDelivery\Sdk\DDeliverySDK;
use DDelivery\Order\DDeliveryOrder;
use DDelivery\Adapter\DShopAdapterImpl;
use DDelivery\Point\DDeliveryInfo;
use DDelivery\Point\DDeliveryAbstractPoint;
use DDelivery\Point\DDeliveryPointCourier;

/**
 * DDeliveryUI - Обертка рабочих классов, для взаимодействия 
 * с системой DDelivery
 *
 * @package  DDelivery
 */
class DDeliveryUI
{
    /**
     * Поддерживаемые способы доставки
     * @var int[]
     */
    public $supportedTypes;
    /**
     * @var int
     */
    public $deliveryType = 0;

    /**
	 * Api обращения к серверу ddelivery
	 * 
	 * @var DDeliverySDK
	 */
    public  $sdk;
	
    /**
     * Адаптер магазина CMS
     * @var DShopAdapter
     */
    private $shop;
    
    /**
     * Заказ DDelivery
     * @var DDeliveryOrder
     */
    private $order;
	
    
    /**
     * @param DShopAdapter $dShopAdapter
     */
    public function __construct(DShopAdapter $dShopAdapter)
    {	
    	
        $this->sdk = new Sdk\DDeliverySDK($dShopAdapter->getApiKey(), true);
        
        $this->shop = $dShopAdapter;
        SQLite::$dbUri = $dShopAdapter->getPathByDB();
		
        // Формируем объект заказа
        $productList = $this->shop->getProductsFromCart();
        $this->order = new DDeliveryOrder( $productList );
        $this->order->amount = $this->shop->getAmount();
        $this->order->declaredPrice = $this->shop->getDeclaredPrice();
        $this->order->paymentVariant = $this->shop->getPaymentVariant();
        
    }
    
    /**
     * Жду подтверждение  для необходимости  реализации 
     * 
     * при изменении состояния заказа пересчитать 
     * все возможные парамтры для заказа, цена для доставк на точку 
     *
     * @return void;
     */
    public function update()
    {
    	
    }
    /**
     * получить текущее состояние заказа в БД
     * 
     * Вызывается всегда при инициализации объекта заказа
     *
     * @return void;
     */
    public function initIntermediateOrder( $id )
    {	
    	$orderDB = new DataBase\Order();
    	
    	if( !empty( $id ) )	
    	{
    		if($orderDB->isRecordExist($id))
    	    {
    	        $order = $orderDB->selectSerializeByID( $id );
    	        if( count( $order ) )
    	        {	
    	        	// Распаковываем те параметры которые менятся не могут
    	        	$jsonOrder = json_decode($order[0]);
    	        	$this->order->type = $jsonOrder->type;
    	        	$this->order->city = $jsonOrder->city;
    	        	$this->order->toName = $jsonOrder->to_name;
    	        	$this->order->toPhone = $jsonOrder->to_phone;
    	        	$this->order->toStreet = $jsonOrder->to_street;
    	        	$this->order->toHouse = $jsonOrder->to_house;
    	        	$this->order->toFlat = $jsonOrder->to_flat;
    	        	$this->order->toHouse = $jsonOrder->to_email;
    	        	
    	        	// Распаковываем точку если она была выставлена
    	        	if(!empty($jsonOrder->type) && !empty($jsonOrder->point_id))
    	        	{	
    	        		// Если содержимое корзины не изменялось
    	        		if( $jsonOrder->checksum != md5($this->order->goodsDescription) )
    	        		{ 
	    	        	    $point = unserialize($jsonOrder->point);
	    	        	    $this->order->setPoint($point);
    	        		}
    	        	    else 
    	        	    {	
    	        	    	// Если содержимое корзины изменялось то автоматически перересчитываем параметры заказа для точки
    	        	    	// Либо для самовывоза, либо для курьерки
    	        	    	if( $jsonOrder->type == 1 )
    	        	    	{
    	        	    		$points = $this->getSelfPoints( $this->order->city );	
    	        	    		foreach ( $points as $p )
    	        	    		{
    	        	    			if( $p->pointID == $jsonOrder->point_id )
    	        	    			{
    	        	    				$this->order->setPoint($p);
    	        	    				break;
    	        	    			}
    	        	    		}
    	        	    	}
    	        	    	else if( $jsonOrder->type == 2 )
    	        	    	{
    	        	    		$points = $this->getCourierPointsForCity( $this->order->city );
    	        	    		foreach ( $points as $p )
    	        	    		{
    	        	    		    if( $p->pointID == $jsonOrder->point_id )
    	        	    		    {
    	        	    		        $this->order->setPoint($p);
    	        	    		        break;
    	        	    		    }
    	        	    		}
    	        	    	}
    	        	    }
    	        		
    	        	}
    	        }	
    	    }
    	}
    }
    
    public function getAllOrders()
    {
    	$orderDB = new DataBase\Order();
    	return $orderDB->selectAll();
    }
    
    /**
     * Получить  минимальный и максимальные период и цену поставки для массива точек
     * @var array DDeliveryAbstractPoint[]
     *
     * @return array;
     */
    public function getMinMaxPriceAndPeriodDelivery( $points )
    {
        if( count( $points ) )
        {
        	$minPeriod = -1; 
        	$minPrice  = -1;
        	$maxPeriod = 0;
        	$maxPrice = 0; 
            foreach ($points as $p)
            {	
            	$deliveryInf = $p->getDeliveryInfo();
            	
                if( $minPeriod == -1 )
                {
                	
                	$minPeriod = $deliveryInf->get('delivery_time_avg');
                	$minPrice = $deliveryInf->get('total_price');
                	
                	$maxPeriod = $deliveryInf->get('delivery_time_avg');
                	$maxPrice = $deliveryInf->get('total_price');
                }
                else
                {
                    if( $deliveryInf->get('delivery_time_avg') < $minPeriod )
                    {
                    	$minPeriod = $deliveryInf->get('delivery_time_avg');
                    }
                    if( $deliveryIf->get('total_price') < $minPrice )
                    {
                    	$minPrice  = $deliveryInf->get('total_price');
                    }
                    if( $deliveryInf->get('delivery_time_avg') > $maxPeriod )
                    {
                    	$maxPeriod = $deliveryInf->get('delivery_time_avg');
                    }
                    if( $deliveryIf->get('total_price') > $minPrice )
                    {
                    	$maxPrice  = $deliveryInf->get('total_price');
                    }
                }
            	    
            }
            return array('min_price' => $minPrice, 'min_period' => $minPeriod,
                         'max_price' => $maxPrice, 'max_period' => $maxPeriod);
        }
        return null;
    }
    
    /**
     * Получить минимальный и максимальные период и цену поставки для массива
     * @var array $deliveryInfo
     *
     * @return array;
     */
    public function _getMinMaxPriceAndPeriod( $deliveryInfo )
    {
    	if( count( $deliveryInfo ) )
    	{
    		$minPeriod = -1;
    		$minPrice  = -1;
    		
    		$maxPeriod = 0;
    		$maxPrice = 0;
    		
    		foreach ($deliveryInfo as $p)
    		{
    			if( $minPeriod == -1 )
    			{
    				$minPeriod = $p['delivery_time_avg'];
    				$minPrice = $p['total_price'];
    				
    				$maxPeriod = $p['delivery_time_avg'];
    				$maxPrice = $p['total_price'];
    			}
    			else 
    			{
    				if( $p['delivery_time_avg'] < $minPeriod )
    				{
    					$minPeriod = $p['delivery_time_avg'];
    				}
    				if( $p['total_price'] < $minPrice )
    				{
    					$minPrice  = $p['total_price'];
    				}
    				
    				if( $p['delivery_time_avg'] > $maxPeriod )
    				{
    					$maxPeriod = $p['delivery_time_avg'];
    				}
    				if( $p['total_price'] > $maxPrice )
    				{
    					$maxPrice  = $p['total_price'];
    				}
    			}
    		}
    		
    		return array('min_price' => $minPrice, 'min_period' => $minPeriod,
                         'max_price' => $maxPrice, 'max_period' => $maxPeriod);
    	}
    	return null;
    }
    
    /**
     * Получить минимальный период и цену поставки курьером для города
     * @var int $cityID
     *
     * @return array;
     */
    public function getMinPriceAndPeriodCourier( $cityID )
    {
    	$deliveryInfo = $this->getCourierDeliveryInfoForCity($cityID);
    	return $this->_getMinMaxPriceAndPeriod( $deliveryInfo );
    }
    
    /**
     * Получить минимальный период и цену поставки самовывоза для города 
     * @var int $cityID
     *
     * @return array;
     */
    public function getMinPriceAndPeriodSelf( $cityID )
    {
        $deliveryInfo = $this->getSelfDeliveryInfoForCity($cityID);
        
        return $this->_getMinMaxPriceAndPeriod( $deliveryInfo );
    }
    
    
    /**
     * Сохранить промежуточное состояние заказа в БД
     * 
     * Вызывать вручную при завершении обработки запроса
     *
     * @return int;
     */
    public function saveIntermediateOrder( $id )
    {	
    	
    	$orderDB = new \DDelivery\DataBase\Order();
    	$orderDB->createTable();
    	
    	$packOrder = $this->order->packOrder();
    	
    	if( !empty( $id ) )
    	{	
    	    $id = $orderDB->insertOrder($packOrder);
    	}
    	else 
    	{	
    		if($orderDB->isRecordExist($id) )
    		{
    			$id = $orderDB->updateOrder( $id, $packOrder );
    		}
    		else 
    		{
    			$id = $orderDB->insertOrder($packOrder);
    		}
    	}
    	
	    return  $id;
    }
    
    
    /**
     * Получить объект заказа
     *
     * @return DDeliveryOrder;
     */
    public function getOrder( )
    {
        return $this->order;
    }
    
    	
    
    /**
     * Получить курьерские точки для города
     * @var int $cityID
     *
     * @return DDeliveryPointCourier[]
     */
    public function getCourierPointsForCity( $cityID )
    {
    	
    	$pointsUser = $this->shop->preGoToFindPoints( $this->order );
    	if( $pointsUser['go_to_server'] )
    	{
    	
    	    $response = $this->sdk->calculatorCourier( $cityID, $this->order->getDimensionSide1(),
                                                   $this->order->getDimensionSide2(), $this->order->getDimensionSide3(),
                                                   $this->order->getWeight(), 0 );
    	    $this->order->city = $cityID;
    	
    	
    	    if( $response->success )
            {
    		    $points = array();
    		    if( count( $response->response ) )
    		    {
    			    foreach ($response->response as $p)
    			    {
    				    $point = new \DDelivery\Point\DDeliveryPointCourier();
    				    $deliveryInfo = new \DDelivery\Point\DDeliveryInfo( $p );
    				    $point->setDeliveryInfo($deliveryInfo);
    				    $point->pointID = $deliveryInfo->get('delivery_company');
    				    $points[] = $point;
    			    }
    		   }
    		
    		   $points = $this->shop->filterPointsCourier( $points, $this->order, $cityID );
            }
    	}  
    	$allPoints = array_merge( $pointsUser['points'], $points );
    	return $allPoints;
    }
    
    /**
     * Получить компании самовывоза для города
     * @var int $cityID
     *
     * @return array;
     */
    public function getCourierDeliveryInfoForCity( $cityID )
    {
    	$response = $this->sdk->calculatorCourier( $cityID, $this->order->getDimensionSide1(),
    			                                   $this->order->getDimensionSide2(),
    			                                   $this->order->getDimensionSide3(),
    			                                   $this->order->getWeight(), 0 );
    	if( $response->success )
    	{
    		return $response->response;
    	}
    	else
    	{
    		return 0;
    	}	
    }
    /**
     * Получить компании самовывоза для города
     * @var int $cityID
     *
     * @return array;
     */
    public function getSelfDeliveryInfoForCity( $cityID )
    {
    	
    	$response = $this->sdk->calculatorPickupForCity( $cityID, $this->order->getDimensionSide1(),
                                                         $this->order->getDimensionSide2(), 
    			                                         $this->order->getDimensionSide3(),
                                                         $this->order->getWeight(), 0 );

    	if( $response->success )
    	{
    		return $response->response;
    	}
    	else
    	{
    		return 0;
    	}
    }
    
    /**
     * Получить информацию о самовывозе для точки
     * @var int $cityID
     *
     * @return DDeliveryAbstractPoint[];
     */
    public function getDeliveryInfoForPointID( $pointID )
    {
    	 
    	$response = $this->sdk->calculatorPickupForPoint( $pointID, $this->order->getDimensionSide1(),
    			$this->order->getDimensionSide2(),
    			$this->order->getDimensionSide3(),
    			$this->order->getWeight(), 0 );
    	if( $response->success )
    	{
    		return new Point\DDeliveryInfo( $response->response );
    	}
    	else
    	{
    		return 0;
    	}
    }
    
    /**
     *
     * Для удобства перебора сортируем массив объектов deliveryInfo
     * 
     *
     */
    private function _getOrderedDeliveryInfo( $companyInfo )
    {
    	$deliveryInfo = array();
    	foreach ( $companyInfo as $c )
    	{
    		$id = $c['delivery_company'];
    		$deliveryInfo[$id] = new Point\DDeliveryInfo( $c );
    	}
    	return $deliveryInfo;
    }
    
    /**
     *
     * Перед отправкой заказа курьеркой на сервер DDelivery проверяется 
     * заполнение всех данных для заказа
     *
     */
    public function checkOrderCourierValues()
    {	
    	$point = $this->order->getPoint();
    	
        if( $point == null )
        {
        	throw new DDeliveryException("Bad courier point");
        }
        
        if( !$this->isValidString($this->order->toName) || !$this->isValidPhone($this->order->toPhone) ||  
            !$this->isValidString($this->order->toStreet) || !$this->isValidString($this->order->toFlat) ||
    	    !$this->isValidString($this->order->toHouse))
        {   
        	throw new DDeliveryException("Bad user Info");
        	if( !empty( $this->order->toEmail ) )
        	{
        	    if( !$this->isValidEmail( $this->order->toEmail ) ) 
        	    {
        	    	throw new DDeliveryException("Bad user Info");
        	    }
        	}
        }
        return true;
    }
    
    /**
     *
     * Перед отправкой заказа самовывоза на сервер DDelivery проверяется
     * заполнение всех данных для заказа
     *
     */
    public function checkOrderSelfValues()
    {
    	$point = $this->order->getPoint();
    	 
    	if( $point == null )
    	{
    		throw new DDeliveryException("Bad self delivery point");
    	}
    	
    	if( !$this->isValidString($this->order->toName) || !$this->isValidPhone($this->order->toPhone) )
    	{
    		throw new DDeliveryException("Bad userInfo");
    	}
    	return true;
    }
    
    /**
     *
     * отправить заказ на курьерку
     *
     */
    public function createCourierOrder( $intermediateID )
    {
    	/** @var DDeliveryPointCourier $point */
    	try
    	{
    		$this->checkOrderCourierValues();
    	}
    	catch (DDeliveryException $e)
    	{
    		return 0;
    	}
    	$point = $this->order->getPoint();
    	$to_city = $this->order->city;
    	
    	$delivery_company = $point->getDeliveryInfo()->get('delivery_company');
    	
    	$dimensionSide1 = $this->order->getDimensionSide1();
    	$dimensionSide2 = $this->order->getDimensionSide2();
    	$dimensionSide3 = $this->order->getDimensionSide3();
    	
    	$goods_description = $this->order->getGoodsDescription();
    	$weight = $this->order->getWeight();
    	$confirmed = $this->order->getConfirmed();
    	
    	$to_name = $this->order->getToName();
    	$to_phone = $this->order->getToPhone();
    	$declaredPrice = $this->order->declaredPrice;
    	
    	$orderPrice = $point->getDeliveryInfo()->get('total_price');
    	$paymentPrice = $this->shop->getPaymentPrice($this->order, $orderPrice);
    	
    	$to_street = $this->order->toStreet;
    	$to_house = $this->order->toHouse;
    	$to_flat = $this->order->toFlat;
    	
    	$shop_refnum = $this->shop->getShopRefNum($this->order);
    	
    	if( $this->shop->sendOrderToDDeliveryServer($this->order) )
    	{
            $response = $this->sdk->addCourierOrder( $to_city, $delivery_company, 
                                                 $dimensionSide1, $dimensionSide2, 
    			                                 $dimensionSide3, $shop_refnum, $confirmed, 
    			                                 $weight, $to_name, $to_phone, $goods_description, 
    			                                 $declaredPrice, $paymentPrice, $to_street, 
                                                 $to_house, $to_flat );
    	
    	
    	    if( !count ( $response->response ))
    	    {
    		    throw new DDeliveryException( implode(',', $response->errorMessage ));
    		    return 0;
    	    }
    	    else 
    	    {
    	    	$ddeliveryOrderID = $response->response['order'];
    	    }
    	}
    	 
    	$this->saveFullCourierOrder( $intermediateID, $to_city, $delivery_company, 
                                     $dimensionSide1, $dimensionSide2, 
    			                     $dimensionSide3, $shop_refnum, $confirmed, 
    			                     $weight, $to_name, $to_phone, $goods_description, 
    			                     $declaredPrice, $paymentPrice, $to_street, 
                                     $to_house, $to_flat, $ddeliveryOrderID);
    	
    	return $response->response['order'];
    }
    
    
    /**
     *
     * отправить заказ на самовывоз
     *
     */
    public function createSelfOrder( $intermediateID )
    {
        /** @var DDeliveryPointSelf $point */
    	try 
    	{
    		$this->checkOrderSelfValues();
    	}
    	catch (DDeliveryException $e)
    	{
    		return 0;
    	}    	
    	$point = $this->order->getPoint();
    	
    	$pointID = $point->get('_id');
    	$dimensionSide1 = $this->order->getDimensionSide1();
    	$dimensionSide2 = $this->order->getDimensionSide2();
    	$dimensionSide3 = $this->order->getDimensionSide3();
    	$goods_description = $this->order->getGoodsDescription();
    	$weight = $this->order->getWeight();
    	$confirmed = $this->order->getConfirmed();
    	$to_name = $this->order->getToName();
    	$to_phone = $this->order->getToPhone();
    	$declaredPrice = $this->order->declaredPrice;
    	
    	$orderPrice = $point->getDeliveryInfo()->get('total_price');
    	$paymentPrice = $this->shop->getPaymentPrice($this->order, $orderPrice);
    	
    	
    	$ddeliveryOrderID = 0;
    	if( $this->shop->sendOrderToDDeliveryServer($this->order) )
    	{
    	    $response = $this->sdk->addSelfOrder( $pointID, $dimensionSide1, $dimensionSide2,
                                                  $dimensionSide3, $confirmed, $weight, $to_name,
                                                  $to_phone, $goods_description, $declaredPrice, 
    			                                  $paymentPrice );
    	
    	    if( !count ( $response->response ))
    	    {
    	         throw new DDeliveryException( implode(',', $response->errorMessage ));
    	    }
    	    else 
    	    {
    	    	$ddeliveryOrderID = $response->response['order'];
    	    }
    	}
    	
    	$this->saveFullSelfOrder( $intermediateID, $pointID, $dimensionSide1, $dimensionSide2,
                                  $dimensionSide3, $confirmed, $weight, $to_name,
                                  $to_phone, $goods_description, $declaredPrice, 
    			                  $paymentPrice, $ddeliveryOrderID );
    	
    	return $response->response['order'];
    }
    
    /**
     * сохранить в БД данные о заказе курьерки
     *
     * @param 
     *
     *
     * @return DDeliveryPointSelf[]
     */
    public function saveFullCourierOrder( $intermediateID, $to_city, $delivery_company, 
                                          $dimensionSide1, $dimensionSide2, 
    			                          $dimensionSide3, $shop_refnum, $confirmed, 
    			                          $weight, $to_name, $to_phone, $goods_description, 
    			                          $declaredPrice, $paymentPrice, $to_street, 
                                          $to_house, $to_flat, $ddeliveryOrderID )
    {
        $orderDB = new \DDelivery\DataBase\Order();
        
        $id = $orderDB->saveFullCourierOrder( $intermediateID, $to_city, $delivery_company, 
                                              $dimensionSide1, $dimensionSide2, 
    			                              $dimensionSide3, $shop_refnum, $confirmed, 
    			                              $weight, $to_name, $to_phone, $goods_description, 
    			                              $declaredPrice, $paymentPrice, $to_street, 
                                              $to_house, $to_flat, $ddeliveryOrderID );
        return $id;  
        
    }
    /**
     * сохранить в БД данные о заказе на самовывоз
     *
     * @param
     *
     *
     * @return DDeliveryPointSelf[]
     */
    public function saveFullSelfOrder( $intermediateID, $pointID, $dimensionSide1, $dimensionSide2,
                                       $dimensionSide3, $confirmed, $weight, $to_name,
                                       $to_phone, $goods_description, $declaredPrice, 
    			                       $paymentPrice, $ddeliveryOrderID )
    {
    	$orderDB = new \DDelivery\DataBase\Order();
    	
    	$id = $orderDB->saveFullSelfOrder( $intermediateID, $pointID, $dimensionSide1, $dimensionSide2,
                                           $dimensionSide3, $confirmed, $weight, $to_name,
                                           $to_phone, $goods_description, $declaredPrice, 
    			                           $paymentPrice, $ddeliveryOrderID 	);
    }
    
    /**
     * Получить компании самовывоза  для города с их полным описанием, и координатами их филиалов
     *
     * @param int $cityID
     * 
     *
     * @throws DDeliveryException
     * @return DDeliveryPointSelf[]
     */
    public function getSelfPoints( $cityID )
    {
    	$pointsUser = $this->shop->preGoToFindPoints( $this->order );
    	if( $pointsUser['go_to_server'] )
    	{
    	    $points = $this->getSelfPointsForCityAndCompany(null, $cityID);
    	    $companyInfo = $this->getSelfDeliveryInfoForCity( $cityID );
    	    $deliveryInfo = $this->_getOrderedDeliveryInfo( $companyInfo );
    	    if( count( $points ) )
    	    {
    		    foreach ( $points as $item )
    		    {
    			    $companyID = $item->get('company_id');
    			
    			    if( array_key_exists( $companyID, $deliveryInfo ) )
    			    {
    				    $item->setDeliveryInfo( $deliveryInfo[$companyID] );
    				    $item->pointID = $item->get('_id');
    			     }
    		     }
    	     }
    	     $points = $this->shop->filterPointsSelf( $points, $this->order );
    	}
    	$allPoints = array_merge( $pointsUser['points'], $points );
    	return $allPoints;
    	
    }
    
    public function isValidPhone( $phone )
    {
    	if ( preg_match('/^[0-9]{10}$/', $phone) )
    	{
    	    return true;
    	}
    	return false;
    }

    public function isValidString( $addressElement )
    {
        if( strlen( $addressElement ) > 0 && strlen( $addressElement ) < 100 )
        {
        	return true;
        }
        return false;
    }
    public function isValidEmail( $addressEmail )
    {
    	if ( preg_match('/^[\._a-za-z0-9-]+@[\.a-za-z0-9-]+\.[a-z]{2,6}$/', $addressEmail) )
    	{
    		return true;
    	}
    	return false;
    }
    /**
     * Получить информацию о точке самовывоза по ее ID  и по ID города
     * 
     * @var mixed $cityID
     * @var mixed $companyIDs
     *
     * @return DDeliveryPointSelf[];
     */
    public function getSelfPointsForCityAndCompany( $companyIDs, $cityID )
    {	
    	
    	$pointsUser = $this->shop->preGoToFindPoints( $this->order );
    	if( $pointsUser['go_to_server'] )
    	{
    	    $points = array();
    	
    	    $response = $this->sdk->getSelfDeliveryPoints( $companyIDs, $cityID );
    	
    	    if( $response->success )
    	    {	
    		    foreach ( $response->response as $p )
    		    {	
    			    $points[] = new DDeliveryPointSelf( $p );
    		    }
    	     }
    	     $points = $this->shop->filterPointsSelf( $points, $this->order );
    	}
    	$allPoints = array_merge( $pointsUser['points'], $points );
    	return $allPoints;
    }
    
    public function setOrderPoint( $point )
    {
    	$this->order->setPoint( $point );
    }
    
    public function setOrderToPhone( $phone )
    {
    	$phone = trim(strip_tags($phone));
    	$this->order->toPhone = $phone;
    }
    
    public function setOrderToName( $name )
    {	
    	$name = trim(strip_tags($name));
    	$this->order->toName = $name;
    }
    
    public function setOrderToFlat( $flat )
    {   
    	$flat = trim(strip_tags($flat));
    	$this->order->toFlat = $flat;
    }
    
    public function setOrderToHouse( $house )
    {   
    	$house = trim(strip_tags( $house ));
    	$this->order->toHouse = $house;
    }
    
    public function setOrderToEmail( $email )
    {	
    	$email = trim(strip_tags( $email ));
    	$this->order->toEmail = $email;
    }

    /**
     * Возвращает id текущего города или пытается определить его
     * @return int
     */
    protected function getCityId()
    {
        if($this->order->city) {
            return $this->order->city;
        }

        $cityId = (int)$this->shop->getClientCityId();

        if(!$cityId){
            $sdkResponse = $this->sdk->getCityByIp($_SERVER['REMOTE_ADDR']);
            if($sdkResponse && $sdkResponse->success && isset($sdkResponse->response['city_id'])) {
                $cityId = (int)$sdkResponse->response['city_id'];
            }
            if(!$cityId) {
                $topCityId = $this->sdk->getTopCityId();
                $cityId = reset($topCityId); // Самый большой город
            }
        }
        return $cityId;
    }

    /**
     * Вызывается для рендера текущей странички
     * @param array $request
     * @throws DDeliveryException
     * @todo метод не финальный
     */
    public function render($request)
    {
        if(isset($request['action'])){
            switch($request['action']){
                case 'searchCity':
                    if(isset($request['name']) && mb_strlen($request['name']) >= 3){
                        $cityList = $this->sdk->getAutoCompleteCity($request['name']);

                        $cityList = $cityList->response;
                        $cityId = $this->order->city;
                        ob_start();
                        include(__DIR__ . '/../../templates/cityHelper.php');
                        $content = ob_get_contents();
                        ob_end_clean();

                        echo json_encode(array(
                            'html'=>$content,
                            'request'=>array(
                                'name'=>$request['name'],
                                'action'=>'searchCity'
                            )
                        ));
                        return;
                    }
            }
        }


        $deliveryType = (int) (isset($request['type']) ? $request['type'] : 0);
        $cityId = (int) (isset($request['city_id']) ? $request['city_id'] : 0);
        $this->order->city = $cityId ? $cityId : $this->getCityId();

        if(isset($request['iframe'])) {
            $staticURL = $this->shop->getStaticPath();
            $scriptURL = $this->shop->getPhpScriptURL();
            include(__DIR__ . '/../../templates/iframe.php');
            return;
        }

        $supportedTypes = $this->shop->getSupportedType();

        if(!is_array($supportedTypes))
            $supportedTypes = array($supportedTypes);

        $this->supportedTypes = $supportedTypes;

        // Проверяем поддерживаем ли мы этот тип доставки
        if($deliveryType && !in_array($deliveryType, $supportedTypes))
            $deliveryType = 0;

        if(count($supportedTypes) > 1 && !$deliveryType) {
            echo $this->renderDeliveryTypeForm();
            return;
        }
        if(!$deliveryType)
            $deliveryType = reset($supportedTypes);

        $this->deliveryType = $deliveryType;

        switch($deliveryType) {
            case DDeliverySDK::TYPE_SELF:
                echo $this->renderMap();
                break;
            case DDeliverySDK::TYPE_COURIER:
                echo $this->renderCourier();
                break;
            default:
                throw new DDeliveryException('Not support delivery type');
                break;
        }
    }

    /**
     * Получаем массив городов для отображения на странцие
     * @param $cityId
     * @return array
     */
    protected function getCityByDisplay($cityId)
    {
        $cityDB = new City();
        $topCityId = $this->sdk->getTopCityId();
        $cityList = $cityDB->getCityListById($topCityId, true);

        // Складываем массивы получаем текущий город наверху, потом его и выберем
        if(isset($cityList[$cityId])){
            $cityData = $cityList[$cityId];
            unset($cityList[$cityId]);
            array_unshift($cityList, $cityData);
        }else{
            array_unshift($cityList, $cityDB->getCityById($cityId));
        }
        foreach($cityList as $key => $cityData){
            $cityList[$key]['display_name'] = $cityDB->getDisplayCityName($cityData);
        }
        return $cityList;
    }

    /**
     * Страница с картой
     * @return string
     */
    protected function renderMap()
    {
        $cityId = $this->getCityId();
        $cityList = $this->getCityByDisplay($cityId);

        $points = $this->getSelfPoints($cityId);
        $pointsJs = array();
        foreach($points as $point) {
            $pointsJs[] = $point->toJson();
        }

        ob_start();
        include(__DIR__ . '/../../templates/map.php');
        $content = ob_get_contents();
        ob_end_clean();
        return json_encode(array('html'=>$content, 'js'=>'map', 'points' => $pointsJs));
    }

    /**
     * Возвращает страницу с формой выбора способа доставки
     * @return string
     */
    protected function renderDeliveryTypeForm()
    {
        $cityId = $this->getCityId();
        $cityList = $this->getCityByDisplay($cityId);

        $order = $this->order;

        $order->declaredPrice = $this->shop->getDeclaredPrice($order->getProducts());
        $selfCompanyList = $this->getSelfDeliveryInfoForCity( $cityId);

        $minSelfPrice = PHP_INT_MAX;
        $minSelfTime = PHP_INT_MAX;
        foreach($selfCompanyList as $selfCompany) {
            if($minSelfPrice > $selfCompany['delivery_price']){
                $minSelfPrice = $selfCompany['delivery_price'];
            }
            if($minSelfTime > $selfCompany['delivery_time_min']){
                $minSelfTime = $selfCompany['delivery_time_min'];
            }
        }
        $minCourierPrice = PHP_INT_MAX;
        $minCourierTime = PHP_INT_MAX;

        $courierCompanyList = $this->getCourierPointsForCity($cityId);
        foreach($courierCompanyList as $courierCompany){
            $deliveryInfo = $courierCompany->getDeliveryInfo();
            $deliveryInfo->pickup_price;
            if($minCourierPrice > $deliveryInfo->delivery_price){
                $minCourierPrice = $deliveryInfo->delivery_price;
            }
            if($minCourierTime > $deliveryInfo->delivery_time_min){
                $minCourierTime = $deliveryInfo->delivery_time_min;
            }
        }

        $this->sdk->calculatorPickupForCity($cityId,
            $order->getDimensionSide1(), $order->getDimensionSide2(), $order->getDimensionSide3(), $order->getWeight(),
            $order->declaredPrice
        );

        ob_start();
        include(__DIR__.'/../../templates/typeForm.php');
        $content = ob_get_contents();
        ob_end_clean();

        return json_encode(array('html'=>$content, 'js'=>''));
    }

    //protected function renderDeliveryTypeForm

    protected function renderCourier()
    {
        $cityId = $this->getCityId();
        $cityList = $this->getCityByDisplay($cityId);
        $companies = $this->getCompanySubInfo();
        $courierCompanyList = $this->getCourierPointsForCity($cityId);
        usort($courierCompanyList, function($a, $b){
            /**
             * @var DDeliveryPointCourier $a
             * @var DDeliveryPointCourier $b
             */
            return $a->delivery_price - $b->delivery_price;
        });

        $courierCompanyList = $this->shop->filterPointsCourier($courierCompanyList, $this->order);
        $staticPath = $this->shop->getStaticPath();

        ob_start();
        include(__DIR__.'/../../templates/couriers.php');
        $content = ob_get_contents();
        ob_end_clean();

        return json_encode(array('html'=>$content, 'js'=>'courier'));
    }


    /**
     * Возвращает дополнительную информацию по компаниям доставки
     * @return array
     */
    public function getCompanySubInfo()
    {
        // pack забита для тех у кого нет иконки
        return array(
            1 => array('name' => 'PickPoint', 'ico' => 'pickpoint'),
            3 => array('name' => 'Logibox', 'ico' => 'logibox'),
            4 => array('name' => 'Boxberry', 'ico' => 'boxberry'),
            6 => array('name' => 'СДЭК забор', 'ico' => 'cdek'),
            7 => array('name' => 'QIWI Post', 'ico' => 'qiwi'),
            11 => array('name' => 'Hermes', 'ico' => 'hermes'),
            13 => array('name' => 'КТС', 'ico' => 'pack'),
            14 => array('name' => 'Maxima Express', 'ico' => 'pack'),
            16 => array('name' => 'IMLogistics Пушкинская', 'ico' => 'imlogistics'),
            17 => array('name' => 'IMLogistics', 'ico' => 'imlogistics'),
            18 => array('name' => 'Сам Заберу', 'ico' => 'pack'),
            20 => array('name' => 'DPD Parcel', 'ico' => 'dpd'),
            21 => array('name' => 'Boxberry Express', 'ico' => 'boxberry'),
            22 => array('name' => 'IMLogistics Экспресс', 'ico' => 'imlogistics'),
            23 => array('name' => 'DPD Consumer', 'ico' => 'dpd'),
            24 => array('name' => 'Сити Курьер', 'ico' => 'pack'),
            25 => array('name' => 'СДЭК Посылка Самовывоз', 'ico' => 'cdek'),
            26 => array('name' => 'СДЭК Посылка до двери', 'ico' => 'cdek'),
            27 => array('name' => 'DPD ECONOMY', 'ico' => 'dpd'),
            28 => array('name' => 'DPD Express', 'ico' => 'dpd'),
            29 => array('name' => 'DPD Classic', 'ico' => 'dpd'),
            30 => array('name' => 'EMS', 'ico' => 'ems'),
            31 => array('name' => 'Grastin', 'ico' => 'pack'),
        );
    }


}