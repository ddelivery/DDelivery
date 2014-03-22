<?php
namespace DDelivery\Point;

class DDeliveryPointSelf{

    private $params = array();

    private  $allowParams = array('_id', 'name', 'city_id', 'city', 'region',
                                    'region_id', 'city_type', 'postal_code', 'area',
                                    'kladr', 'company', 'company_id', 'company_code',
                                    'metro', 'description_in', 'description_out',
                                    'indoor_place', 'address', 'schedule', 'longitude',
                                    'latitude', 'type', 'status', 'has_fitting_room',
                                    'is_cash', 'is_card');
    
    private $deliveryInfo = null;
    
    public function __construct( $initParams = array() )
    {	
    	if(is_array($initParams))
    	{	
    		foreach ( $initParams as $key => $value)
    		{	
    			try 
    			{
    				$this->set($key, $value);
    			}
    				catch (DDeliveryPointException $e)
    			{
    				echo $e->getMessage();
    			}
    		}
    	}
    }
    
    public function set( $paramName, $paramValue)
    {
    	
    	if( in_array($paramName, $this->allowParams) )
    	{	
    		$this->params[$paramName] = $paramValue;
    	}
    	else
    	{
    		throw new DDeliveryPointException("Point param not found");
    	}
    }
    
    public function get( $paramName )
    {
    	if( array_key_exists($paramName, $this->params) )
    	{
    		return 	$this->params[$paramName];
    	}
    	return null;
    }
    
    public function setDeliveryInfo( $deliveryInfo )
    {
    	 $this->deliveryInfo = $deliveryInfo;
    }
    
    public function getDeliveryInfo()
    {
    	return $this->deliveryInfo;
    }
}