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
        
        // Получаем параметры для товаров в заказе
        $this->order->getProductParams();

    }
    
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
	
    public function getOrder( )
    {
        return $this->order;
    }
    
    public function getDeliveryInfoForPoint( $id )
    {
    	
    	$declaredPrice = 0;
    	$response = $this->sdk->calculatorPickup( $id, $this->order->dimensionSide1, 
    			                                  $this->order->dimensionSide2, $this->order->dimensionSide3, 
                                                  $this->order->weight, $declaredPrice );

    	if(count( $response->success) )
    	{
    		
    		return new Point\DDeliveryInfo( $response->response );
    		
    	}
    	return null; 
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
        echo json_encode(['html'=>file_get_contents(__DIR__.'/../../templates/popup-form.php')]);
    }


}