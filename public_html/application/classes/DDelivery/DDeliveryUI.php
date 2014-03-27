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

/**
 * DDeliveryUI - Обертка рабочих классов, для взаимодействия 
 * с системой DDelivery
 *
 * @package  DDelivery
 */
class DDeliveryUI
{	
	
	/**
	 * Api обращения к серверу ddelivery
	 * 
	 * @var DDeliverySDK
	 */
    private $sdk;
	
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
    
    public function getCurierPointsForCity( $cityID )
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
    				$point = new \DDelivery\Point\DDeliveryPointCurier();
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
    public function getSelfPointsForCity( $cities, $companies = '' )
    {
    	$response = $this->sdk->getSelfDeliveryPoints( $cities, $companies );
    	
    	if( $response->success )
    	{
    		$points = array();
    		if( count( $response->response ) )
    		{
    			foreach ($response->response as $p)
    			{
    				$point = new \DDelivery\Point\DDeliveryPointSelf( $p );
    				
    				$deliveryInfo = $this->getDeliveryInfoForPoint( $point->get('_id') );
    				 
    				$point->setDeliveryInfo( $deliveryInfo );
    				 
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
     * Вызывается для рендера текущей странички
     * @param array $post
     * @todo метод не финальный
     */
    public function render($post)
    {
        //echo json_encode(['html'=>file_get_contents(__DIR__.'/popup-map.php')]);
        $this->selectDeliveryTypeForm();
    }

    protected function selectDeliveryTypeForm()
    {
        $cityId = (int)$this->shop->getClientCityId();
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

        include(__DIR__.'/../../templates/popup-form.php');
        $content = ob_get_contents();

        ob_end_clean();

        echo json_encode(array('html'=>$content));


    }


}