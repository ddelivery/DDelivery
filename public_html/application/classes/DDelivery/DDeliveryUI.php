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


class DDeliveryUI
{
    private $sdk;

    private $shop;
    
    private $order;
    
    public function __construct()
    {
        $this->sdk = new Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
        
        $this->shop = new Adapter\DShopAdapterImpl();

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
    public function getSelfPointsForCity( $cities, $companies )
    {
    	$response = $this->sdk->getSelfDeliveryPoints( $cities, $companies );
    	if( $response->success )
    	{
    		$points = array();
    		if( count( $response->response ) )
    		{
    			foreach ($pointsResponse as $p)
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