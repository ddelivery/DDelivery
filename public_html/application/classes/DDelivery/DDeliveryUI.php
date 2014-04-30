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
use DDelivery\Sdk\DCache;
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
     *  Кэш
     *  @var DCache
     */
    private $cache;

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

        $this->messager = new Sdk\DDeliveryMessager($this->shop->isTestMode());

        $this->cache = new DCache( $this, $this->shop->getCacheExpired(), $this->shop->isCacheEnabled() );
    }
   




    /**
     *
     * Получить статус заказа cms по статусу DD
     *
     * @param $ddStatus
     *
     * @return mixed
     */
    public function getLocalStatusByDD( $ddStatus )
    {
        return $this->shop->getLocalStatusByDD( $ddStatus );
    }


    /**
     *
     * Обработчик изменения статуса заказа
     *
     * @param int $cmsOrderID id заказа в cms
     *
     * @return bool
     *
     */
    public function changeOrderStatus( $cmsOrderID )
    {
        $orderDB = new DataBase\Order();
        $data = $orderDB->getOrderByCmsOrderID( $cmsOrderID );
        $ids = array( $data[0]->id );
        $orderArr = $this->initIntermediateOrder($ids);
        $order = $orderArr[0];
        if( $order->ddeliveryID == 0 )
        {
            return false;
        }
        $ddStatus = $this->getDDOrderStatus($order->ddeliveryID);
        if( $ddStatus == 0 )
        {
            return false;
        }
        $order->ddStatus = $ddStatus;
        $order->localStatus = $this->shop->getLocalStatusByDD( $order->ddStatus );
        $this->saveFullOrder($order);
        $this->shop->setCmsOrderStatus($order->shopRefnum, $order->localStatus);
        return true;
    }

    /**
     *
     * Получает статус заказа на сервере DD
     *
     * @param $ddeliveryOrderID
     *
     * @return int
     */
    public function getDDOrderStatus( $ddeliveryOrderID )
    {   
    	try
    	{
            $response = $this->sdk->getOrderStatus($ddeliveryOrderID);
    	}
    	catch (DDeliveryException $e)
    	{   
    		$this->messager->pushMessage( $e->getMessage() );
    		return 0;
    	}
    	return $response->response['status'];
    }

    /**
     * После окончания оформления заказа вызывается в cms и передает заказ на обработку в DDelivery
     *
     * @param int $id id заказа в локальной БД SQLLite
     * @param string $shopOrderID id заказа в CMS
     * @param int $status выбираются интегратором произвольные
     * @param int $payment выбираются интегратором произвольные
     * @throws DDeliveryException
     *
     * @return bool
     */
    public function onCmsOrderFinish( $id, $shopOrderID, $status, $payment)
    {   
        if(!$this->initIntermediateOrder( $id )) {
            return false;
        }
        $order = $this->getOrder();
        $order->paymentVariant = (int)$payment;
        $order->shopRefnum = $shopOrderID;
        $order->localStatus = (int)$status;

        if( $this->shop->isStatusToSendOrder( $status, $order) )
        {   

            if( $order->type == DDeliverySDK::TYPE_SELF ) {
                $order->ddeliveryID = $this->createSelfOrder($order);
            } else if( $order->type == DDeliverySDK::TYPE_COURIER ) {
                $order->ddeliveryID = $this->createCourierOrder($order);
            }else{
                throw new DDeliveryException('Not support order type');
            }
            $this->saveFullOrder($order);
            return (bool)$order->ddeliveryID;
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
     * Инициализирует заказ по id из заказов локальной БД, в контексте текущего UI
     *
     * @param int $id id заказа
     *
     * @throws DDeliveryException
     *
     * @return DDeliveryOrder[]
     */
    public function initIntermediateOrder( $id )
    {
        $orderDB = new DataBase\Order();
        if(!$id)
            return false;
        $orders = $orderDB->getOrderList(array( $id ));
        if( count($orders) )
        {
            $item = $orders[0];
            $this->_initOrderInfo( $this->order,  $item);
        }
        return true;
    }

    /**
     * Инициализирует массив заказов из массива id заказов локальной БД
     *
     * @param int[] $ids массив с id заказов
     *
     * @throws DDeliveryException
     *
     * @return DDeliveryOrder[]
     */
    public function initOrder( $ids )
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
                $this->_initOrderInfo( $currentOrder, $item );
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
     * @deprecated
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
    	        	    		$points = $this->getSelfPoints( $this->order );
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
    	        	    		$points = $this->getCourierPointsForCity( $this->order );
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
            foreach ($points as $point)
            {
            	$deliveryInf = $point->getDeliveryInfo();

                if( $minPeriod == -1 )
                {

                	$minPeriod = $deliveryInf->get('delivery_time_avg');
                	$minPrice = $deliveryInf->clientPrice;

                	$maxPeriod = $deliveryInf->get('delivery_time_avg');
                	$maxPrice = $deliveryInf->clientPrice;
                }
                else
                {
                    if( $deliveryInf->get('delivery_time_avg') < $minPeriod )
                    {
                    	$minPeriod = $deliveryInf->get('delivery_time_avg');
                    }
                    if( $deliveryInf->clientPrice < $minPrice )
                    {
                    	$minPrice  = $deliveryInf->clientPrice;
                    }
                    if( $deliveryInf->get('delivery_time_avg') > $maxPeriod )
                    {
                    	$maxPeriod = $deliveryInf->get('delivery_time_avg');
                    }
                    if( $$deliveryInf->clientPrice > $minPrice )
                    {
                    	$maxPrice  = $deliveryInf->clientPrice;
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
    protected function _getMinMaxPriceAndPeriod( $deliveryInfo )
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
    				$minPrice = $p->clientPrice;

    				$maxPeriod = $p['delivery_time_avg'];
    				$maxPrice = $p->clientPrice;
    			}
    			else
    			{
    				if( $p['delivery_time_avg'] < $minPeriod )
    				{
    					$minPeriod = $p['delivery_time_avg'];
    				}
    				if( $p['total_price'] < $minPrice )
    				{
    					$minPrice  = $p->clientPrice;
    				}

    				if( $p['delivery_time_avg'] > $maxPeriod )
    				{
    					$maxPeriod = $p['delivery_time_avg'];
    				}
    				if( $p['total_price'] > $maxPrice )
    				{
    					$maxPrice  = $p->clientPrice;
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
     *
     * @param DDeliveryOrder $order
     *
     * @return array;
     */
    public function getMinPriceAndPeriodCourier( $order )
    {
    	$deliveryInfo = $this->getCourierDeliveryInfoForCity($order);
    	return $this->_getMinMaxPriceAndPeriod( $deliveryInfo );
    }

    /**
     * Получить минимальный период и цену поставки самовывоза для города
     *
     * @param DDeliveryOrder $order
     *
     * @return array;
     */
    public function getMinPriceAndPeriodSelf( $order )
    {
        $deliveryInfo = $this->getSelfDeliveryInfoForCity($order);

        return $this->_getMinMaxPriceAndPeriod( $deliveryInfo );
    }


    /**
     * Сохранить промежуточное состояние заказа в БД
     *
     * Вызывать вручную при завершении обработки запроса
     *
     * @deprecated
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
            $this->order->localId = $orderDB->insertOrder($packOrder);
        }
        return $this->order->localId;
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
     * Проверяем на валидность $order для получение точек доставки
     *
     * @param DDeliveryOrder $order
     *
     * @return bool
     */
    public function _validateOrderToGetPoints( DDeliveryOrder $order )
    {
        if( count($order->getProducts()) > 0 && $order->city )
        {
            return true;
        }
        return false;
    }

    /**
     * Получить курьерские точки для города
     *
     * @param DDeliveryOrder $order
     * @throws DDeliveryException
     * @return array DDeliveryPointCourier[]
     */
    public function getCourierPointsForCity( DDeliveryOrder $order )
    {
        if(!$this->_validateOrderToGetPoints($order))
            throw new DDeliveryException('Для получения списка необходимо корректный order');
        $points = array();
    	// Есть ли необходимость искать точки на сервере ddelivery
    	if( $this->shop->preGoToFindPoints( $this->order ))
    	{
            $declared_price = $this->shop->getDeclaredPrice($order);
    	    $response = $this->sdk->calculatorCourier( $order->city, $order->getDimensionSide1(),
                                                       $order->getDimensionSide2(),
                                                       $order->getDimensionSide3(),
                                                       $order->getWeight(), $declared_price );
    	    if( $response->success )
            {

    		    if( count( $response->response ) )
    		    {
    			    foreach ($response->response as $p)
    			    {
    				    $point = new \DDelivery\Point\DDeliveryPointCourier( false );
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

        $points = $this->shop->filterPointsCourier( $points, $order);
        return $points;
    }

    /**
     * Получить компании самовывоза для города
     * @param DDeliveryOrder $order
     * @throws DDeliveryException
     * @return array;
     */
    public function getCourierDeliveryInfoForCity( DDeliveryOrder $order )
    {
        $declared_price = $this->shop->getDeclaredPrice($order);
        if(!$this->_validateOrderToGetPoints($order))
            throw new DDeliveryException('Для получения списка необходимо корректный order');
    	$response = $this->sdk->calculatorCourier( $order->city, $order->getDimensionSide1(),
    			                                   $order->getDimensionSide2(),
    			                                   $order->getDimensionSide3(),
    			                                   $order->getWeight(), $declared_price );
    	if( $response->success )
    	{
    		return $response->response;
    	}
    	else
    	{
    		return array();
    	}
    }

    /**
     *
     * Получить пользовательскую точку по ID
     *
     * @param $pointID
     * @param DDeliveryOrder $order
     *
     * @throws DDeliveryException
     *
     * @return DDeliveryAbstractPoint
     *
     */
    public function getUserPointByID( $pointID, $order )
    {
        $userPoint = null;
        if( $order->type = DDeliverySDK::TYPE_COURIER )
        {
            $points = $this->shop->getUserCourierPoints( $order );
        }
        else if( $order->type = DDeliverySDK::TYPE_SELF )
        {
            $points = $this->shop->getUserSelfPoints( $order );
        }
        if( count($points) )
        {
            foreach( $points as $p )
            {
                if($p->pointID = $pointID)
                {
                    $userPoint = $p;
                    break;
                }
            }
        }

        if( $userPoint == null )
        {
            throw new DDeliveryException('Точка не найдена');
        }
        return $userPoint;
    }

    /**
     *  Получить курьерскую точку по id компании
     *
     * @param $companyID
     * @param $order
     *
     * @return DDeliveryPointCourier|null
     * @throws DDeliveryException
     */
    public function getCourierPointByCompanyID( $companyID, $order )
    {
        $deliveryInfo = $this->getCourierDeliveryInfoForCity($order);
        $courierPoint = null;
        if(count( $deliveryInfo ))
        {
            foreach( $deliveryInfo as $di )
            {
                if ( $di['delivery_company'] == $companyID )
                {
                    $courierPoint = new DDeliveryPointCourier(false);
                    $courierPoint->setDeliveryInfo( new DDeliveryInfo($di) );
                    break;
                }
            }
        }
        if( $courierPoint == null )
        {
            throw new DDeliveryException('Точка не найдена');
        }
        return $courierPoint;
    }

    /**
     *
     * Получить всю информацию по точке по ее ID
     *
     * @param int $pointId id точки
     * @param DDeliveryOrder $order
     *
     * @return DDeliveryPointSelf
     * @throws DDeliveryException
     */
    public function getSelfPointByID( $pointId, $order )
    {
        if(!$this->_validateOrderToGetPoints( $order))
            throw new DDeliveryException('Для получения списка необходимо корректный order');
        $points = $this->cache->render( 'getSelfPointsDetail', array( $order->city ) );
        $selfPoint = null;
        if(count($points))
        {
            foreach( $points AS $p )
            {
                if( $p->_id == $pointId )
                {
                    $selfPoint = $p;
                    break;
                }
            }
        }
        if( $selfPoint == null )
        {
            throw new DDeliveryException('Точка не найдена');
        }
        /**
         * @var DDeliveryPointSelf $selfPoint
         */
        $deliveryInfo = $this->getDeliveryInfoForPointID( $pointId, $order );
        $selfPoint->setDeliveryInfo($deliveryInfo);
        return $selfPoint;
    }

    /**
     * Получить компании самовывоза  для города с их полным описанием, и координатами их филиалов
     * @param DDeliveryOrder $order
     * @throws DDeliveryException
     * @return DDeliveryPointSelf[]
     */
    public function getSelfPoints( DDeliveryOrder $order )
    {
        if(!$this->_validateOrderToGetPoints( $order))
            throw new DDeliveryException('Для получения списка необходимо корректный order');
        // Есть ли необходимость искать точки на сервере ddelivery
        $result_points = array();
        if( $this->shop->preGoToFindPoints( $order ))
        {
            $points = $this->cache->render( 'getSelfPointsDetail', array( $order->city ) ); /** cache **/
            //$points = $this->getSelfPointsDetail( $order->city ); /** cache **/

            $companyInfo = $this->getSelfDeliveryInfoForCity( $order );

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
                        $result_points[] = $item;
                    }
                }
            }
        }
        $points = $this->shop->filterPointsSelf( $result_points , $order, $order->city );

        return $points;

    }


    /**
     * Получить компании самовывоза для города
     *
     * @param DDeliveryOrder $order
     * @throws DDeliveryException
     *
     * @return array
     */
    public function getSelfDeliveryInfoForCity( DDeliveryOrder $order )
    {
        $declared_price = $this->shop->getDeclaredPrice($order);
    	$response = $this->sdk->calculatorPickupForCity( $order->city, $order->getDimensionSide1(),
                                                         $order->getDimensionSide2(),
                                                         $order->getDimensionSide3(),
                                                         $order->getWeight(), $declared_price );
        
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
     *
     * @param int $pointID
     * @param DDeliveryOrder $order
     *
     * @return DDeliveryInfo
     */
    public function getDeliveryInfoForPointID( $pointID, DDeliveryOrder $order )
    {

        $declared_price = $this->shop->getDeclaredPrice($order);
    	$response = $this->sdk->calculatorPickupForPoint( $pointID, $order->getDimensionSide1(),
                                                          $order->getDimensionSide2(),
                                                          $order->getDimensionSide3(),
                                                          $order->getWeight(), $declared_price );
    	if( $response->success )
    	{
    		return new Point\DDeliveryInfo( reset($response->response) );
    	}
    	else
    	{
    		return null;
    	}
    }

    /**
     * Для удобства перебора сортируем массив объектов deliveryInfo
     *
     * @param array $companyInfo
     * @return Point\DDeliveryInfo[]
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
     * Здесь проверяется заполнение всех данных для заказа
     *
     * @param DDeliveryOrder $order заказ ddelivery
     * @throws DDeliveryException
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
        if( $order->type != DDeliverySDK::TYPE_COURIER )
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

        if( empty( $order->localStatus ) )
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
     * @throws DDeliveryException
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
        if( $order->type != DDeliverySDK::TYPE_SELF )
        {
        	$errors[] = "Не верный тип доставки";
        }

        if( empty( $order->paymentVariant ) )
        {
        	$errors[] = "Не указан способ оплаты в CMS";
        }

        if( empty( $order->localStatus ) )
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
        return true;
    }

    /**
     *
     * Сохранить в локальную БД заказ
     *
     * @param DDeliveryOrder $order заказ ddelivery
     *
     * @return int
     */
    public function saveFullOrder( DDeliveryOrder $order )
    {
    	$orderDB = new DataBase\Order();
    	$id = $orderDB->saveFullOrder( $order );
    	return $id;
    }

    /**
     *
     * отправить заказ на курьерку
     *
     * @param DDeliveryOrder $order
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
    	    $confirmed = $this->shop->isConfirmedStatus($order);

    	    $to_name = $order->getToName();
    	    $to_phone = $order->getToPhone();

    	    $orderPrice = $point->getDeliveryInfo()->clientPrice;

    	    $declaredPrice = $this->shop->getDeclaredPrice( $order );
    	    $paymentPrice = $this->shop->getPaymentPriceCourier( $order, $orderPrice );

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

    	$this->saveFullOrder( $order );

    	return $ddeliveryOrderID;
    }


    /**
     *
     * отправить заказ на самовывоз
     *
     * @param DDeliveryOrder $order
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
    	    $confirmed = $this->shop->isConfirmedStatus($order);
    	    $to_name = $order->getToName();
    	    $to_phone = $order->getToPhone();

    	    $orderPrice = $point->getDeliveryInfo()->clientPrice;
    	    $declaredPrice = $this->shop->getDeclaredPrice( $order );
    	    $paymentPrice = $this->shop->getPaymentPriceSelf( $order, $orderPrice );

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

        $this->saveFullOrder( $order );

    	return $ddeliveryOrderID;
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
     * Получить информацию о точке самовывоза по ее ID  и по ID города
     *
     * @param mixed $cityID
     * @param mixed $companyIDs
     *
     * @return DDeliveryPointSelf[]
     */
    public function getSelfPointsDetail( $cityID, $companyIDs = null )
    {

    	$points = array();

    	$response = $this->sdk->getSelfDeliveryPoints( $companyIDs, $cityID );

    	if( $response->success )
    	{
    		foreach ( $response->response as $p )
    		{
    			$point = new DDeliveryPointSelf( false );
                $point->init( $p );
                $points[] = $point;
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
     */
    public function render($request)
    {
        if(!empty($request['order_id'])) {
            $this->initIntermediateOrder($request['order_id']);
        }

        if(isset($request['action'])) {
            switch($request['action']) {
                case 'searchCity':
                    if(isset($request['name']) && mb_strlen($request['name']) >= 3){
                        $cityList = $this->sdk->getAutoCompleteCity($request['name']);

                        $cityList = $cityList->response;
                        foreach($cityList as $key => $city){
                            $cityList[$key]['name'] = Utils::firstWordLiterUppercase($city['name']);
                        }

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
                        $pointSelf = $this->getSelfPointByID((int)$request['id'], $this->order);
                        if(empty($pointSelf)) {
                            echo json_encode(array('point'=>array()));
                        }
                        $selfCompanyList = $this->shop->filterSelfInfo(array($pointSelf->getDeliveryInfo()));
                        if(empty($selfCompanyList)){
                            echo json_encode(array('point'=>array()));
                            return;
                        }

                        echo json_encode(array(
                            'point'=>array(
                                'description_out' => $pointSelf->description_out,
                                'total_price' => $pointSelf->getDeliveryInfo()->clientPrice,
                                'delivery_time_min' => $pointSelf->getDeliveryInfo()->delivery_time_min,
                                'delivery_time_min_str' => Utils::plural($pointSelf->getDeliveryInfo()->delivery_time_min, 'дня', 'дней', 'дней', 'дней', false),
                            ),
                        ));
                    }
                    return;
            }
        }

        if(!empty($request['city_id'])) {
            $this->order->city = $request['city_id'];
        }
        if(!$this->order->city ) {
            $this->order->city = $this->getCityId();
        }
        if(!empty($request['point']) && isset($request['type'])) {
            if ( $request['type'] == DDeliverySDK::TYPE_SELF ) {
                $this->order->setPoint($this->getSelfPointByID($request['point'], $this->order));
            }elseif($request['type'] == DDeliverySDK::TYPE_COURIER){
                $this->order->setPoint($this->getCourierPointByCompanyID($request['point'], $this->order));
            }
        }
        if(!empty($request['contact_form']) && is_array($request['contact_form'])){
            if(!empty($request['contact_form'])) {
                foreach($request['contact_form'] as $row) {
                    switch($row['name']){
                        case 'second_name':
                            $this->order->secondName = $row['value'];
                            break;
                        case 'first_name':
                            $this->order->firstName = $row['value'];
                            break;
                        case 'phone':
                            $this->order->toPhone = $row['value'];
                            break;
                        case 'address':
                            $this->order->toStreet = $row['value'];
                            break;
                        case 'address_house':
                            $this->order->toHouse = $row['value'];
                            break;
                        case 'address_housing':
                            $this->order->toHousing = $row['value'];
                            break;
                        case 'address_flat':
                            $this->order->toFlat = $row['value'];
                            break;
                        case 'comment':
                            //@todo Комента нет
                            //$this->order->toHousing = $row['value'];
                            break;
                    }
                }
            }
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

        $this->order->localId = $this->saveFullOrder($this->order);

        switch($request['action']) {
            case 'map':
                echo $this->renderMap();
                break;
            case 'mapDataOnly':
                echo $this->renderMap(true);
                break;
            case 'courier':
                echo $this->renderCourier();
                break;
            case 'typeForm':
                echo $this->renderDeliveryTypeForm();
                break;
            case 'typeFormDataOnly':
                echo $this->renderDeliveryTypeForm(true);
                break;
            case 'contactForm':
                echo $this->renderContactForm();
                break;
            case 'change':
                $comment = '';
                $point = $this->order->getPoint();
                if ($point instanceof DDeliveryPointSelf) {
                    $comment = 'Самовывоз, '.$point->address;
                } elseif($point instanceof DDeliveryPointCourier) {
                    $comment = 'Доставка курьером по адресу '.$this->order->getFullAddress();
                }
                echo json_encode(array('html'=>'', 'js'=>'change', 'comment'=>htmlspecialchars($comment), 'orderId' => $this->order->localId));
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
        $cityList = $cityDB->getTopCityList();
        // Складываем массивы получаем текущий город наверху, потом его и выберем
        if(isset($cityList[$cityId])){
            $cityData = $cityList[$cityId];
            unset($cityList[$cityId]);
            array_unshift($cityList, $cityData);
        }else{
            array_unshift($cityList, $cityDB->getCityById($cityId));
        }
        foreach($cityList as &$cityData){
            // Костыль, на сервере города начинаются с маленькой буквы
            $cityData['name'] = Utils::firstWordLiterUppercase($cityData['name']);

            //Собирает строчку с названием города для отображения
            $displayCityName = $cityData['type'].'. '.$cityData['name'];
            if($cityData['region'] != $cityData['name']) {
                $displayCityName .= ', '.$cityData['region'].' обл.';
            }

            $cityData['display_name'] = $displayCityName;
        }
        return $cityList;
    }

    /**
     * Страница с картой
     *
     * @param bool $dataOnly ajax
     * @return string
     */
    protected function renderMap($dataOnly = false)
    {
        $cityId = $this->order->city;

        $points = $this->getSelfPoints($this->order);
        $pointsJs = array();

        foreach($points as $point) {
            $pointsJs[] = $point->toJson();
        }
        $staticURL = $this->shop->getStaticPath();
        $selfCompanyList = $this->getSelfDeliveryInfoForCity( $this->order );
        $selfCompanyList = $this->_getOrderedDeliveryInfo( $selfCompanyList );
        $selfCompanyList = $this->shop->filterSelfInfo($selfCompanyList);

        if($dataOnly) {
            ob_start();
            include(__DIR__ . '/../../templates/mapCompanyHelper.php');
            $content = ob_get_contents();
            ob_end_clean();
            return json_encode(array('html'=>$content, 'points' => $pointsJs, 'orderId' => $this->order->localId));
        } else {
            $cityList = $this->getCityByDisplay($cityId);
            $headerData = $this->getDataFromHeader();
            ob_start();
            include(__DIR__ . '/../../templates/map.php');
            $content = ob_get_contents();
            ob_end_clean();
            return json_encode(array('html'=>$content, 'js'=>'map', 'points' => $pointsJs, 'orderId' => $this->order->localId, 'type'=>DDeliverySDK::TYPE_SELF));
        }
    }

    protected function getDataFromHeader()
    {
        $cityId = $this->order->city;

        $order = $this->order;
        $order->city = $cityId;
        $data = array(
            'self' => array(
                'minPrice' => 0,
                'minTime' => 0,
                'timeStr' => '',
                'disabled' => true,
            ),
            'courier' => array(
                'minPrice' => 0,
                'minTime' => 0,
                'timeStr' => '',
                'disabled' => true
            ),
        );

        if(in_array(Sdk\DDeliverySDK::TYPE_SELF, $this->supportedTypes)) {
            $selfCompanyList = $this->getSelfDeliveryInfoForCity( $this->order );
            if(!empty($selfCompanyList)){
                $selfCompanyList = $this->_getOrderedDeliveryInfo( $selfCompanyList );
                $selfCompanyList = $this->shop->filterSelfInfo($selfCompanyList);
                if(!empty($selfCompanyList)) {
                    $minPrice = PHP_INT_MAX;
                    $minTime = PHP_INT_MAX;
                    foreach($selfCompanyList as $selfCompany) {
                        if($minPrice > $selfCompany->clientPrice){
                            $minPrice = $selfCompany->clientPrice;
                        }
                        if($minTime > $selfCompany->delivery_time_min){
                            $minTime = $selfCompany->delivery_time_min;
                        }
                    }
                    $data['self'] = array(
                        'minPrice' => $minPrice,
                        'minTime' => $minTime,
                        'timeStr' => Utils::plural($minTime, 'дня', 'дней', 'дней', 'дней', false),
                        'disabled' => false
                    );
                }
            }
        }
        if(in_array(Sdk\DDeliverySDK::TYPE_COURIER, $this->supportedTypes)) {
            $courierCompanyList = $this->getCourierPointsForCity($this->order);
            if(!empty($courierCompanyList)){
                $courierCompanyList = $this->shop->filterPointsCourier($courierCompanyList, $this->order);
                if($courierCompanyList){
                    $minPrice = PHP_INT_MAX;
                    $minTime = PHP_INT_MAX;

                    foreach($courierCompanyList as $courierCompany){
                        $deliveryInfo = $courierCompany->getDeliveryInfo();
                        if($minPrice > $deliveryInfo->clientPrice) {
                            $minPrice = $deliveryInfo->clientPrice;
                        }
                        if($minTime > $deliveryInfo->delivery_time_min) {
                            $minTime = $deliveryInfo->delivery_time_min;
                        }
                    }
                    $data['courier'] = array(
                        'minPrice' => $minPrice,
                        'minTime' => $minTime,
                        'timeStr' => Utils::plural($minTime, 'дня', 'дней', 'дней', 'дней', false),
                        'disabled' => false
                    );
                }
            }
        }
        return $data;
    }

    /**
     * Возвращает страницу с формой выбора способа доставки
     * @param bool $dataOnly если передать true, то отдаст данные для обновления верстки через js
     * @return string
     */
    protected function renderDeliveryTypeForm( $dataOnly = false )
    {
        $staticURL = $this->shop->getStaticPath();
        $cityId = $this->order->city;

        $order = $this->order;
        $order->declaredPrice = $this->shop->getDeclaredPrice($order);
        $order->city = $cityId;

        $data = $this->getDataFromHeader();

        if(!$dataOnly) {
            // Рендер html
            $cityList = $this->getCityByDisplay($cityId);

            ob_start();
            include(__DIR__.'/../../templates/typeForm.php');
            $content = ob_get_contents();
            ob_end_clean();

            return json_encode(array('html'=>$content, 'js'=>'typeForm', 'orderId' => $this->order->localId, 'typeData' => $data));
        }else{
            return json_encode(array('typeData' => $data));
        }
    }

    /**
     * @return string
     */
    protected function renderCourier()
    {
        $cityId = $this->order->city;
        $cityList = $this->getCityByDisplay($cityId);
        $companies = $this->getCompanySubInfo();
        $courierCompanyList = $this->getCourierPointsForCity($this->order);

        $staticURL = $this->shop->getStaticPath();
        // Ресетаем ключи.
        $courierCompanyList = array_values($courierCompanyList);
        $headerData = $this->getDataFromHeader();
        ob_start();
        include(__DIR__.'/../../templates/couriers.php');
        $content = ob_get_contents();
        ob_end_clean();

        return json_encode(array('html'=>$content, 'js'=>'courier', 'orderId' => $this->order->localId, 'type'=>DDeliverySDK::TYPE_COURIER));
    }

    /**
     * @return string
     */
    private function renderContactForm()
    {
        $point = $this->getOrder()->getPoint();
        if(!$point){
            return '';
        }
        if ($point instanceof DDeliveryPointSelf) {
            $deliveryType = DDeliverySDK::TYPE_SELF;
        } elseif($point instanceof DDeliveryPointCourier) {
            $deliveryType = DDeliverySDK::TYPE_COURIER;
        }else{
            return '';
        }

        if($deliveryType == DDeliverySDK::TYPE_COURIER) {
            $requiredFieldMask = $this->shop->getCourierRequiredFields();
        }else{
            $requiredFieldMask = $this->shop->getSelfRequiredFields();
        }

        $order = $this->order;

        $fieldValue = $order->getToName();
        if(!$fieldValue)
            $order->setToName($this->shop->getClientFirstName());


        $fieldValue = $order->secondName;
        if(!$fieldValue)
            $order->secondName = $this->shop->getClientLastName();

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

        return json_encode(array('html'=>$content, 'js'=>'contactForm', 'orderId' => $this->order->localId, 'type'=>DDeliverySDK::TYPE_COURIER));
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

    /**
     *
     * Инициализирует свойства объекта DDeliveryOrder из stdClass полученный из
     * запроса БД SQLite
     *
     * @param DDeliveryOrder $currentOrder
     * @param \stdClass $item
     */
    public function _initOrderInfo($currentOrder, $item)
    {
        $currentOrder->type = $item->type;
        $currentOrder->localId = $item->id;
        $currentOrder->confirmed = $item->confirmed;
        $currentOrder->amount = $item->amount;
        $currentOrder->city = $item->to_city;
        $currentOrder->localStatus = $item->local_status;
        $currentOrder->ddStatus = $item->dd_status;
        $currentOrder->shopRefnum = $item->shop_refnum;
        $currentOrder->ddeliveryID = $item->ddeliveryorder_id;
        if ($item->point != null) {
            $currentOrder->setPoint(unserialize($item->point));
        }
        $currentOrder->firstName = $item->first_name;
        $currentOrder->secondName = $item->second_name;
        $currentOrder->shopRefnum = $item->shop_refnum;
        $currentOrder->declared_price = $item->declared_price;
        $currentOrder->paymentPrice = $item->payment_price;
        $currentOrder->toName = $item->to_name;
        $currentOrder->toPhone = $item->to_phone;
        $currentOrder->goodsDescription = $item->goods_description;
        $currentOrder->toStreet = $item->to_street;
        $currentOrder->toHouse = $item->to_house;
        $currentOrder->toFlat = $item->to_flat;
        $currentOrder->toEmail = $item->to_email;
    }


}