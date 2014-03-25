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
     * @var DShopAdapterImpl
     */
    private $shop;
    
    /**
     * Заказ DDelivery
     * @var DDeliveryOrder
     */
    private $order;
    
    public function __construct()
    {
        $this->sdk = new Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
        
        $this->shop = new Adapter\DShopAdapterImpl();
        
        $products = $this->shop->getProductsFromCart();
        
        $this->order = new Order\DDeliveryOrder( $products );

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

    
    public function getDeliveryInfoForPoint( $id )
    {
    	
    	$dimensionSide1 = 1;
    	$dimensionSide2 = 1;
    	$dimensionSide3 = 1;
    	$weight = 1;
    	$declaredPrice = 0;
    	
    	$response = $this->sdk->calculatorPickup( $id, $dimensionSide1, 
    			                                 $dimensionSide2, $dimensionSide3, $weight, 
    			                                 $declaredPrice );
    	
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
    
    
}