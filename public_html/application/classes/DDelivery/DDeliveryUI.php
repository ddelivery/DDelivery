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
use DDelivery\Sdk\DDeliverySDK;
use DDelivery\Order\DDeliveryOrder;
use DDelivery\Adapter\DShopAdapterImpl;
use DDelivery\Point\DDeliveryInfo;
use DDelivery\Point\DDeliveryAbstractPoint;

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
        
        $this->order = new Order\DDeliveryOrder( $this->shop );

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
     * Получить информацию о заказе для точки
     * @var int $id
     *
     * @return DDeliveryInfo;
     */
    public function getDeliveryInfoForPoint( $id )
    {
    	
    	$declaredPrice = 0;
    	$response = $this->sdk->calculatorPickup( $id, $this->order->getDimensionSide1(), 
    			                                  $this->order->getDimensionSide2(), $this->order->getDimensionSide3(), 
                                                  $this->order->getWeight(), $declaredPrice );

    	if(count( $response->success) )
    	{
    		
    		return new Point\DDeliveryInfo( $response->response );
    		
    	}
    	return null; 
    }
    
    /**
     * Получить курьерские точки для города
     * @var int $cityID
     *
     * @return array DDeliveryAbstractPoint;
     */
    public function getCourierPointsForCity( $cityID )
    {
    	$response = $this->sdk->calculatorCourier( $cityID, $this->order->getDimensionSide1(),
                                                   $this->order->getDimensionSide2(), $this->order->getDimensionSide3(),
                                                   $this->order->getWeight(), 0 );
    	
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
    				$points[] = $point;
    			}
    		}
    		return $points;
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
     * @return array DDeliveryAbstractPoint;
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
     * Получить компании самовывоза для города
     * @var int $cityID
     *
     * @return array DDeliveryAbstractPoint;
     */
    public function getDeliveryInfoForPointID( $pointID )
    {
    	 
    	$response = $this->sdk->calculatorPickupForCompany( $pointID, $this->order->getDimensionSide1(),
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
     * Получить компании самовывоза  для города с их 
     * полным описанием, и координатами их филиалов
     * 
     * Нужно профиксить баг с выдаванием точек
     * узнать на каком уровне откидывать точки
     * 
     * @var int $cityID
     *
     * @return array DDeliveryAbstractPoint;
     */
    public function getSelfPoints( $cityID )
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
    				
    			}
    			/*
    			else 
    			{	
    				/
    				$pointID = $item->get('_id');
    				
    				$deliveryItem = $this->getDeliveryInfoForPointID( $pointID );
    				
    				$itemId = $deliveryItem->get('delivery_company');
    				
    				$deliveryInfo[ $itemId ] = $deliveryItem;
    				
    				$item->setDeliveryInfo( $deliveryInfo[$companyID] );
    			}
    			*/
    			
    		}
    	}
    	else 
    	{
    		throw new \DDelivery\DDeliveryException("Точек самовывоза не найдено");
    	}
    	
    	return $points;
    	
    }
    /**
     * Получить информацию о точке самовывоза по ее ID  и по ID города
     * 
     * @var mixed $cityID
     * @var int $companyIDs
     *
     * @return array;
     */
    public function getSelfPointsForCityAndCompany( $companyIDs, $cityID )
    {	
    	
    	$points = array();
    	
    	$response = $this->sdk->getSelfDeliveryPoints( $companyIDs, $cityID );
    	
    	if( $response->success )
    	{	
    		foreach ( $response->response as $p )
    		{	
    			if( $p['type'] == 1 )
    			{
    			    $points[] = new \DDelivery\Point\DDeliveryPointSelf( $p );
    			}
    		}
    	}
    	
    	return $points;
    }

    /**
     * Вызывается для рендера текущей странички
     * @param array $post
     * @throws DDeliveryException
     * @todo метод не финальный
     */
    public function render($post)
    {
        $deliveryType = (int) (isset($post['type']) ? $post['type'] : 0);
        $cityId = (int) (isset($post['city_id']) ? $post['city_id'] : 0);

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

                break;
            default:
                throw new DDeliveryException('Not support delivery type');
                break;
        }
    }

    /**
     * Страница с картой
     * @return string
     */
    protected function renderMap()
    {
        ob_start();
        include(__DIR__ . '/../../templates/map.php');
        $content = ob_get_contents();
        ob_end_clean();
        return json_encode(array('html'=>$content, 'js'=>''));
    }

    /**
     * Возвращает страницу с формой выбора способа доставки
     * @return string
     */
    protected function renderDeliveryTypeForm()
    {
        $cityId = (int)$this->shop->getClientCityId();
        $cityData = false;
        if(!$cityId){
            $sdkResponse = $this->sdk->getCityByIp($_SERVER['REMOTE_ADDR']);
            if($sdkResponse->success && isset($sdkResponse->response['city_id'])) {
                $cityId = (int)$sdkResponse->response['city_id'];
                $cityData = $sdkResponse->response;
            }
        }
        // @todo когда будет метод возвращающий инфу о городе по id добавить
        // @todo когда будет метод взять топ, забрать мск
        if(!$cityData) {
            $cityData = array("city_id"=>151184, "country"=>"RU", "city"=>"москва",
                "region"=>"Москва", "region_id"=>77, "type"=>"г", "postal_code"=>"101000",
                "area"=>"","kladr"=>"77000000000");
        }
        $displayCityName = $cityData['type'].'. '.$cityData['region'];

        if(mb_strtolower($cityData['region']) != mb_strtolower($cityData['city'])) {
            $displayCityName .= ', '.$cityData['city'];
        }
        $cityData['display_name'] = $displayCityName;

        ob_start();
        include(__DIR__.'/../../templates/typeForm.php');
        $content = ob_get_contents();
        ob_end_clean();

        return json_encode(array('html'=>$content, 'js'=>''));
    }


}