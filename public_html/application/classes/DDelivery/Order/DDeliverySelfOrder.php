<?php
/**
 * 
 * @author mrozk
 *
 */
namespace DDelivery\Order;


class DDeliverySelfOrder
{	
	
	private $params = array();
	
	private $allowParams = array('id', 'type', 'delivery_point',
	                             'dimension_side1', 'dimension_side2',
	                             'dimension_side3', 'weight', 'declared_price',
	                             'payment_price', 'to_name', 'to_phone', 
	                             'goods_description');
	
    public function __construct( $initParams = array() )
    {
        if(is_array($initParams))
        {
            foreach ( $initParams as $key=>$value)
            {
                $this->set($key, $value);	
            }    	
        }	
    }	 
	
    public function pack()
    {
        foreach ($this->allowParams as $p)
        {
            if ( !array_key_exists( $p, $this->params ) ) 
            {
            	throw new DDeliveryOrderException("Order params not full");
            }
        }
        return $this->params;
    }
    
    public function set( $paramName, $paramValue)
    {
        if( array_key_exists($paramName, $this->allowParams) )
        {
        	$this->params[$paramName] = $paramValue;
        }
        else
        {
        	throw new DDeliveryOrderException("Order param not found");
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
	
}