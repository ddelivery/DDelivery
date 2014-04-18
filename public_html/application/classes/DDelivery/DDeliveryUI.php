<?php
/**
*
* @package    DDelivery
*
* @author  mrozk
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
use DDelivery\Sdk\Messager;


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
     * печаталка сообщений про ошибку
     * @var string
     */
    private $messager;

    /**
     * @param DShopAdapter $dShopAdapter
     */
    public function __construct(DShopAdapter $dShopAdapter)
    {
        $this->shop = $dShopAdapter;

        $this->sdk = new Sdk\DDeliverySDK($dShopAdapter->getApiKey(), $this->shop->isTestMode());

        SQLite::$dbUri = $dShopAdapter->getPathByDB();

        // Формируем объект заказа
        $productList = $this->shop->getProductsFromCart();
        $this->order = new DDeliveryOrder( $productList );
        
        $this->order->amount = $this->shop->getAmount();
        $this->order->paymentVariant = $this->shop->getPaymentVariant();
        
        $this->messager = new Sdk\DDeliveryMessager($this->shop->isTestMode());
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
     * @todo завтра допилится
     */
    public function changeOrderStatus( $orederStatus )
    {
        echo $this->shop->getLocalStatusByDD($orederStatus);
    }
    /**
     * @todo завтра допилится
     */
    public function checkOrderStatus( $orderID )
    {
    	try
    	{
            $response = $this->sdk->getOrderStatus($orderID);
    	}
    	catch (DDeliveryException $e)
    	{
    		$this->messager->pushMessage( $e->getMessage() );
    	}
    	return $response;
    }
    /**
     * После окончания оформления заказа в cms вызывается для 
     * дальнейшей обработки заказа
     *
     * @param int $id id заказа в локальной БД SQLLite
     * @param int $shopOrderID id заказа в CMS
     * 
     * @todo
     * 
     * @return bool
     */
    public function onCmsOrderFinish( $id, $shopOrderID)
    {   
        try
        {
            $order = $this->initIntermediateOrder( array($id) );
        }
        catch (DDeliveryException $e)
        {
            $this->messager->pushMessage( $e->getMessage() );
            return false;
        }
        $shopOrderInfo = $this->shop->getShopOrderInfo( $order[0]->shop_refnum );	
        //$this->setShopOrderID( $id, $shopOrderInfo['payment'], $shopOrderInfo['status'], $shopOrderInfo['id']);
        $order->paymentVariant = $shopOrderInfo['payment'];
        $order->shopRefnum = $shopOrderInfo['id'];
        $order->localStatus = $shopOrderInfo['status'];
        
        if( $this->shop->isStatusToSendOrder( $shopOrderInfo['status'], $order) )
        {
            if( $order->type == 1 )
            {
                $ddOrderID = $this->createSelfOrder($order);
            }
            else if( $order->type == 2 )
            {
                $ddOrderID = $this->createCourierOrder($order);
            }
            $order->ddeliveryID = $ddOrderID;
        }
        $this->saveFullOrder($order);
        return true;
    }
    
    
    
    /**
     * Устанавливаем для заказа в таблице Orders SQLLite id заказа в CMS
     *
     * @param int $id id локальной БД SQLLite
     * @param int $shopOrderID id заказа в CMS
     * @param string $paymentVariant  вариант оплаты в CMS
     * @param string $status статус заказа
     * 
     * @return bool
     */
    public function setShopOrderID( $id, $paymentVariant, $status, $shopOrderID )
    {
    	$orderDB = new DataBase\Order();
    	return $orderDB->setShopOrderID($id, $paymentVariant, $status, $shopOrderID);
    }
    
    /**
     * Инициализирует массив заказов из массива id заказов локальной БД
     *
     * @param int[]  $ids массив с id заказов
     *
     * @return DDeliveryOrder[]
     */
    public function initIntermediateOrder($ids)
    {   
    	$orderDB = new DataBase\Order();
        $orderList = array();
        if(!count($ids))
        	throw new DDeliveryException('Пустой массив для инициализации заказа');
        $orders = $orderDB->getOrderList($ids);
       
        if(count($orders))
        {
            foreach ( $orders as $item)
            {   
            	$productList = unserialize( $item->products );
                $currentOrder = new DDeliveryOrder( $productList );
                $currentOrder->type = $item->type;
                $currentOrder->localId = $item->id;
                $currentOrder->confirmed = $item->confirmed;
                $currentOrder->amount = $item->amount;
                $currentOrder->to_city = $item->to_city;
                $currentOrder->localStatus = $item->local_status;
                $currentOrder->ddStatus = $item->dd_status;
                $currentOrder->shopRefnum = $item->shop_refnum;
                $currentOrder->ddeliveryorder_id = $item->ddeliveryorder_id;
                if( $item->point != null )
                {
                	$currentOrder->setPoint(unserialize( $item->point ));
                } 
                $currentOrder->firstName = $item->firstName;
                $currentOrder->secondName = $item->secondName;
                $currentOrder->shop_refnum = $item->shop_refnum;
                $currentOrder->declared_price = $item->declared_price;
                $currentOrder->paymentPrice = $item->payment_price;
                $currentOrder->to_name = $item->to_name;
                $currentOrder->to_phone = $item->to_phone;
                $currentOrder->goods_description = $item->goods_description;
                $currentOrder->to_street = $item->to_street;
                $currentOrder->to_house = $item->to_house;
                $currentOrder->to_flat = $item->to_flat;
                $currentOrder->to_email = $item->to_email;
            	$orderList[] = $currentOrder;
            }    
        }
        else 
        {
        	throw new DDeliveryException('Заказ DD в локальной БД не найден');
        }
        return $orderList;
    }
    /**
     * Инициализировать заказ в контексте текущего заказа
     *
     * Вызывается всегда при инициализации объекта заказа
     *
     * @param $id
     * @return void
     */
    public function initOrderInUIContext( $id )
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
                    $this->order->localId = (int)$id;
    	        	$this->order->type = $jsonOrder->type;
    	        	$this->order->city = $jsonOrder->city;
    	        	$this->order->toName = $jsonOrder->to_name;
    	        	$this->order->toPhone = $jsonOrder->to_phone;
    	        	$this->order->toStreet = $jsonOrder->to_street;
    	        	$this->order->toHouse = $jsonOrder->to_house;
    	        	$this->order->toFlat = $jsonOrder->to_flat;
    	        	$this->order->toHouse = $jsonOrder->to_email;
    	        	$this->order->firstName = $jsonOrder->firstName;
    	        	$this->order->secondName = $jsonOrder->secondName;
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

    /**
     * Получить  минимальный и максимальные период и цену поставки для массива точек
     * @param DDeliveryAbstractPoint[] $points
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
                    if( $deliveryInf->get('total_price') < $minPrice )
                    {
                    	$minPrice  = $deliveryInf->get('total_price');
                    }
                    if( $deliveryInf->get('delivery_time_avg') > $maxPeriod )
                    {
                    	$maxPeriod = $deliveryInf->get('delivery_time_avg');
                    }
                    if( $deliveryInf->get('total_price') > $minPrice )
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
    public function saveIntermediateOrder()
    {
        $orderDB = new \DDelivery\DataBase\Order();

        $packOrder = $this->order->packOrder();
        $id = $this->order->localId;
        if($this->order->localId) {
            $orderDB->updateOrder( $id, $packOrder );
        } 
        else 
        {
            $id = $orderDB->insertOrder($packOrder);
        }
        return $id;
    }

    /**
     * Получить город по ip адресу
     * @var string $ip
     *
     * @return array;
     */
    public function getCityByIp( $ip )
    {
    	$response = $this->sdk->getCityByIp( $ip );

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
     * Получить объект заказа
     * @var string $ip
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
        $points = array();
    	// Есть ли необходимость искать точки на сервере ddelivery
    	if( $this->shop->preGoToFindPoints( $this->order ))
    	{
    	    $response = $this->sdk->calculatorCourier( $cityID, $this->order->getDimensionSide1(),
                                                       $this->order->getDimensionSide2(), $this->order->getDimensionSide3(),
                                                       $this->order->getWeight(), 0 );
    	    $this->order->city = $cityID;

    	    if( $response->success )
            {

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
            }

            usort($points, function($a, $b){
                /**
                 * @var DDeliveryPointCourier $a
                 * @var DDeliveryPointCourier $b
                 */
                return $a->delivery_price - $b->delivery_price;
            });

    	}

        $points = $this->shop->filterPointsCourier( $points, $this->order);
        return $points;
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
     * @return Point\DDeliveryInfo;
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
    		return false;
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
     * @param DDeliveryOrder $order заказ ddelivery
     * 
     * @return bool
     */
    public function checkOrderCourierValues( $order )
    {
    	
    	$errors = array();
    	$point = $order->getPoint();
        
        if( $point == null )
        {
        	$errors[] = "Укажите пожалуйста точку";
        }
        if(!strlen( $order->getToName() ))
        {
        	$errors[] = "Укажите пожалуйста ФИО";
        }
        if(!$this->isValidPhone( $order->toPhone ))
        {
        	$errors[] = "Укажите пожалуйста телефон в верном формате";
        }
        if( $order->type != 2 )
        {
        	$errors[] = "Не верный тип доставки";
        }
        if( !strlen( $order->toStreet ) )
        {
        	$errors[] = "Укажите пожалуйста улицу";
        }
        if(!strlen( $order->toHouse ))
        {
        	$errors[] = "Укажите пожалуйста дом";
        }
        if(!$order->city)
        {
        	$errors[] = "Город не определен";
        }
        if( !strlen( $order->toFlat ) )
        {
        	$errors[] = "Укажите пожалуйста квартиру";
        }
        if(!empty($order->toEmail))
        {
            if(!$this->isValidEmail($order->toEmail))
            {
            	$errors[] = "Укажите пожалуйста email в верном формате";
            }
        }
        
        if( empty( $order->paymentVariant ) )
        {
        		$errors[] = "Не указан способ оплаты в CMS";
        }
        
        if( empty( $order->status ) )
        {
        	$errors[] = "Не указан статус заказа в CMS";
        }
        
        if( ! $order->shopRefnum )
        {
        	$errors[] = "Не найден id заказа в CMS";
        }
        
        if(count($errors))
        {
            throw new DDeliveryException(implode(', ', $errors));
        }
    }

    /**
     *
     * Перед отправкой заказа самовывоза на сервер DDelivery проверяется
     * заполнение всех данных для заказа
     *
     * @param DDeliveryOrder $order заказ ddelivery
     * 
     * @return bool
     */
    public function checkOrderSelfValues( $order )
    {   
    	$errors = array();
    	$point = $order->getPoint();
    	
        if( $point == null )
        {
        	$errors[] = "Укажите пожалуйста точку";
        }
        if(!strlen( $order->getToName() ))
        {
        	$errors[] = "Укажите пожалуйста ФИО";
        }
        if(!$this->isValidPhone( $order->toPhone ))
        {
        	$errors[] = "Укажите пожалуйста телефон в верном формате";
        }
        if( $order->type != 1 )
        {
        	$errors[] = "Не верный тип доставки";
        }
        
        if( empty( $order->paymentVariant ) )
        {
        	$errors[] = "Не указан способ оплаты в CMS";
        }
        
        if( empty( $order->status ) )
        {
        	$errors[] = "Не указан статус заказа в CMS";
        }
        
        if( ! $order->shopRefnum )
        {
        	$errors[] = "Не найден id заказа в CMS";
        }
        
        if(count($errors))
        {
        	throw new DDeliveryException(implode(', ', $errors));
        }
    }

    /**
     *
     * Сохранить в локальную БД заказ при отправке на сервер DDelivery
     *
     * @param DDeliveryOrder $order заказ ddelivery
     *
     * @return int
     */
    public function saveFullOrder( $order )
    {
    	$orderDB = new DataBase\Order();
    	$point = $order->getPoint();
    	$dimensionSide1 = $order->getDimensionSide1();
    	$dimensionSide2 = $order->getDimensionSide2();
    	$dimensionSide3 = $order->getDimensionSide3();
    	$goods_description = $order->getGoodsDescription();
    	$weight = $order->getWeight();
    	$to_city = $order->city;
    	$delivery_company = $point->getDeliveryInfo()->get('delivery_company');
    	$confirmed = $order->getConfirmed();
    	$to_name = $order->getToName();
    	$to_phone = $order->getToPhone();
    	$declaredPrice = $order->declaredPrice;
    	$orderPrice = $point->getDeliveryInfo()->get('total_price');
    	$paymentPrice = $this->shop->getPaymentPrice($order, $orderPrice);
    	$ddeliveryID = $order->ddeliveryID;
    	$localId = $order->localId;
    	$productString = $order->getSerializedProducts();
    	
    	$localStatus = $order->localStatus;
    	$ddStatus = $order->ddStatus;
    	$shop_refnum = $this->order->shopRefnum;
    	
    	if( $order->type == 1 )
    	{   
    	    
    	    $pointID = $point->get('_id');
    	    $id = $orderDB->saveFullSelfOrder( $localId, $pointID, $dimensionSide1, $dimensionSide2, 
    	    		                           $dimensionSide3, $shop_refnum, $confirmed, $weight, 
    	    		                           $to_name, $to_phone, $goods_description, $declaredPrice,
    	 			                           $paymentPrice, $ddeliveryID, $to_city, $delivery_company,
    	                                       $productString, $localStatus, $ddStatus );
    	 }
    	 else if( $this->order->type == 2 )
    	 {  
    	 	
    	    $to_street = $this->order->toStreet;
    	    $to_house = $this->order->toHouse;
    	    $to_flat = $this->order->toFlat;
    	    
    	    $id = $orderDB->saveFullCourierOrder( $localId, $to_city, $delivery_company, $dimensionSide1, 
    	    		                              $dimensionSide2, $dimensionSide3, $shop_refnum, $confirmed, 
    	    		                              $weight, $to_name, $to_phone, $goods_description, $declaredPrice,
    	 			                              $paymentPrice, $to_street, $to_house, $to_flat, $ddeliveryID, 
    	    		                              $productString, $localStatus, $ddStatus );
    	 }

    	 return $id;

    }

    /**
     *
     * отправить заказ на курьерку
     * 
     * @param DDeliveryOrder
     * 
     * @return int
     */
    public function createCourierOrder( $order )
    {
    	/** @var DDeliveryPointCourier $point */
    	try
    	{
    		$this->checkOrderCourierValues( $order );
    	}
    	catch (DDeliveryException $e)
    	{
    		$this->messager->pushMessage( $e->getMessage() );
    		return 0;
    	}
    	
    	$ddeliveryOrderID = 0;
    	
    	if( $this->shop->sendOrderToDDeliveryServer($order) )
    	{
    	    $point = $order->getPoint();

    	    $to_city = $order->city;
    	    $delivery_company = $point->getDeliveryInfo()->get('delivery_company');

    	    $dimensionSide1 = $order->getDimensionSide1();
    	    $dimensionSide2 = $order->getDimensionSide2();
    	    $dimensionSide3 = $order->getDimensionSide3();

    	    $goods_description = $order->getGoodsDescription();
    	    $weight = $order->getWeight();
    	    $confirmed = $order->getConfirmed();

    	    $to_name = $order->getToName();
    	    $to_phone = $order->getToPhone();
            
    	    $orderPrice = $point->getDeliveryInfo()->get('total_price');
    	    
    	    $declaredPrice = $this->shop->getDeclaredPrice( $order );
    	    $paymentPrice = $this->shop->getPaymentPrice( $order, $orderPrice );

    	    $to_street = $order->toStreet;
    	    $to_house = $order->toHouse;
    	    $to_flat = $order->toFlat;
    	    $shop_refnum = $order->shopRefnum;
            try
            {
    	        $response = $this->sdk->addCourierOrder( $to_city, $delivery_company, $dimensionSide1, $dimensionSide2,
    			                                         $dimensionSide3, $shop_refnum, $confirmed, $weight, 
    	        		                                 $to_name, $to_phone, $goods_description, $declaredPrice, 
    	        		                                 $paymentPrice, $to_street, $to_house, $to_flat );
            }
            catch ( DDeliveryException $e )
            {
                $this->messager->pushMessage( $e->getMessage());
                return 0;
            }
    	    $ddeliveryOrderID = $response->response['order'];
    	}
    	$order->ddeliveryID = $ddeliveryOrderID;
    	$order_id = $this->saveFullOrder( $order );
    	return $order_id;
    }


    /**
     *
     * отправить заказ на самовывоз
     * 
     * @param DDeliveryOrder
     * 
     * @return int
     *
     */
    public function createSelfOrder( $order )
    {
        /** @var DDeliveryPointSelf $point */
    	try
    	{
    		$this->checkOrderSelfValues( $order );
    	}
    	catch (DDeliveryException $e)
    	{
    		$this->messager->pushMessage($e->getMessage());
    	    return 0;
    	}
    	$ddeliveryOrderID = 0;
    	if( $this->shop->sendOrderToDDeliveryServer($order) )
    	{
    	    $point = $order->getPoint();

    	    $pointID = $point->get('_id');
    	    $dimensionSide1 = $order->getDimensionSide1();
    	    $dimensionSide2 = $order->getDimensionSide2();
    	    $dimensionSide3 = $order->getDimensionSide3();
    	    $goods_description = $order->getGoodsDescription();
    	    $weight = $order->getWeight();
    	    $confirmed = $order->getConfirmed();
    	    $to_name = $order->getToName();
    	    $to_phone = $order->getToPhone();

    	    $orderPrice = $point->getDeliveryInfo()->get('total_price');
    	    
    	    $declaredPrice = $this->shop->getDeclaredPrice( $order );
    	    $paymentPrice = $this->shop->getPaymentPrice( $order, $orderPrice );
    	    $shop_refnum = $order->shopRefnum;
    	    try
    	    {
    	        $response = $this->sdk->addSelfOrder( $pointID, $dimensionSide1, $dimensionSide2,
    				                                  $dimensionSide3, $confirmed, $weight, $to_name,
    				                                  $to_phone, $goods_description, $declaredPrice,
    				                                  $paymentPrice, $shop_refnum );
    	    }
    	    catch ( DDeliveryException $e )
    	    {
    	    	$this->messager->pushMessage( $e->getMessage() );
    	    	return 0;
    	    }
    	    $ddeliveryOrderID = $response->response['order'];
    	}
    	$order->ddeliveryID = $ddeliveryOrderID;
    	$order_id = $this->saveFullOrder( $order );
    	return $response->response['order'];
    }
    /**
     * Весь список заказов
     *
     */
    public function getAllOrders()
    {
    	$orderDB = new DataBase\Order();
    	return $orderDB->selectAll();
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
    	// Есть ли необходимость искать точки на сервере ddelivery
    	if( $this->shop->preGoToFindPoints( $this->order ))
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
    	}
    	$points = $this->shop->filterPointsSelf( $points, $this->order, $cityID );

    	return $points;

    }


    /**
     * Получить информацию о точке самовывоза по ее ID  и по ID города
     *
     * @param mixed $cityID
     * @param mixed $companyIDs
     *
     * @return array DDeliveryPointSelf[];
     */
    public function getSelfPointsForCityAndCompany( $companyIDs, $cityID )
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


    	return $points;
    }
    /**
     * Проверяем правильность Email
     *
     * @param string $email
     *
     * @return boolean
     */
    public function isValidEmail( $email )
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
        	return true;
        }
        return false;
    }

    /**
     * Проверяем правильность телефона
     *
     * @param string $phone
     *
     * @return boolean
     */
    public function isValidPhone( $phone )
    {
    	if( preg_match('/^[0-9]{10}$/', $phone) )
    	{
    		return true;
    	}
    	return false;
    }

    /**
     * Назначить точку доставки
     *
     */
    public function setOrderPoint( $point )
    {
    	$this->order->setPoint( $point );
    }

    /**
     * Назначить номер телефона доставки
     *
     */
    public function setOrderToPhone( $phone )
    {
    	$this->order->toPhone = trim( strip_tags( $phone ) );
    }

    /**
     * Назначить ФИО доставки
     *
     */
    public function setOrderToName( $name )
    {
    	$this->order->toName = trim( strip_tags( $name ) );
    }

    /**
     * Назначить квартиру доставки
     *
     */
    public function setOrderToFlat( $flat )
    {
    	$this->order->toFlat = trim( strip_tags( $flat ) );
    }

    /**
     * Назначить дом для доставки
     *
     */
    public function setOrderToHouse( $house )
    {
    	$this->order->toHouse = trim( strip_tags( $house ) );
    }

    /**
     * Назначить email для доставки
     *
     */
    public function setOrderToEmail( $email )
    {
    	$this->order->toEmail = trim( strip_tags( $email ) );
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
        if(!empty($request['orderId'])) {
            $this->initIntermediateOrder($request['orderId']);
        }

        if(isset($request['action'])) {
            switch($request['action']) {
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
                    }
                    return;
                case 'mapGetPoint':
                    if(!empty($request['id'])) {
                        $deliveryInfo = $this->getDeliveryInfoForPointID($request['id']);

                        //$this->order->getPoint();

                        // @todo получить подробные данные по точке и отдать их
                        echo json_encode(array(
                            'point'=>''
                        ));
                    }
                    return;
            }
        }

        $cityId = (int) (isset($request['city_id']) ? $request['city_id'] : 0);
        if($cityId) {
            $this->order->city = $cityId;
        }
        if(!$this->order->city ) {
            $this->order->city = $this->getCityId();
        }
        if(!empty($request['point'])) {
            $this->order->setPoint($this->getDeliveryInfoForPointID($request['point']));
        }

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

        if(empty($request['action'])) {

            $deliveryType = (int) (isset($request['type']) ? $request['type'] : 0);
            // Проверяем поддерживаем ли мы этот тип доставки
            if($deliveryType && !in_array($deliveryType, $supportedTypes)) {
                $deliveryType = 0;
            }

            // Неизвестно какой экшен, выбираем
            if(count($supportedTypes) > 1 && !$deliveryType) {
                $request['action'] = 'typeForm';
            }else{
                if(!$deliveryType)
                    $deliveryType = reset($supportedTypes);
                $this->deliveryType = $deliveryType;

                if($deliveryType == DDeliverySDK::TYPE_SELF){
                    $request['action'] = 'map';
                }elseif($deliveryType == DDeliverySDK::TYPE_COURIER){
                    $request['action'] = 'courier';
                }else{
                    throw new DDeliveryException('Not support delivery type');
                }
            }
        }

        switch($request['action']) {
            case 'map':
                echo $this->renderMap();
                break;
            case 'courier':
                echo $this->renderCourier();
                break;
            case 'typeForm':
                echo $this->renderDeliveryTypeForm();
                break;
            case 'contactForm':
                echo $this->renderContactForm();
                break;
            default:
                throw new DDeliveryException('Not support action');
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
        $staticURL = $this->shop->getStaticPath();
        $selfCompanyList = $this->getSelfDeliveryInfoForCity( $cityId);


        ob_start();
        include(__DIR__ . '/../../templates/map.php');
        $content = ob_get_contents();
        ob_end_clean();
        return json_encode(array('html'=>$content, 'js'=>'map', 'points' => $pointsJs, 'orderId' => $this->order->getId()));
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

        return json_encode(array('html'=>$content, 'js'=>'', 'orderId' => $this->order->getId()));
    }

    //protected function renderDeliveryTypeForm

    protected function renderCourier()
    {
        $cityId = $this->getCityId();
        $cityList = $this->getCityByDisplay($cityId);
        $companies = $this->getCompanySubInfo();
        $courierCompanyList = $this->getCourierPointsForCity($cityId);

        foreach($courierCompanyList as $courierCompany) {
            $this->shop->preDisplayCourierPoint($courierCompany, $this->order);
        }
        $staticPath = $this->shop->getStaticPath();
        // Ресетаем ключи.
        $courierCompanyList = array_values($courierCompanyList);

        ob_start();
        include(__DIR__.'/../../templates/couriers.php');
        $content = ob_get_contents();
        ob_end_clean();

        return json_encode(array('html'=>$content, 'js'=>'courier', 'orderId' => $this->order->getId()));
    }

    private function renderContactForm()
    {
        // @todo хардкодим курьера
        $deliveryType = DDeliverySDK::TYPE_COURIER;
        if($deliveryType == DDeliverySDK::TYPE_COURIER){
            $requiredFieldMask = $this->shop->getCourierRequiredFields();
        }

        $order = $this->order;

        $fieldValue = $order->getToName();
        if(!$fieldValue)
            $order->setToName($this->shop->getClientFirstName());


        /** @todo Фамилия
        $fieldValue = $order->getToLastName();
        if(!$fieldValue)
        $order->setToLastName($this->shop->getClientLastName());
         */

        $fieldValue = $order->getToPhone();
        if(!$fieldValue)
            $order->setToPhone($this->shop->getClientPhone());

        $fieldValue = $order->getToStreet();
        if(!$fieldValue){
            $address = $this->shop->getClientAddress();
            if(!is_array($address))
                $address = array($address);
            if(isset($address[0]))
                $order->setToStreet($address[0]);
            if(isset($address[1]))
                $order->setToFlat($address[1]);
            if(isset($address[2]))
                $order->setToHouse($address[2]);
            if(isset($address[3]))
                $order->setToFlat($address[3]);
        }


        ob_start();
        include(__DIR__.'/../../templates/contactForm.php');
        $content = ob_get_contents();
        ob_end_clean();

        return json_encode(array('html'=>$content, 'js'=>'contactForm', 'orderId' => $this->order->getId()));
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