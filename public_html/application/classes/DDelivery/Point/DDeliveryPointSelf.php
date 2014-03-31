<?php
/**
 *
 * @package    DDelivery.Point
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */
namespace DDelivery\Point;

/**
 * DDeliveryPointSelf - компания для самовывоза 
 * 
 * Этот клас ассоциируется с команией через которую будет осуществлятся самовывоз
 * у этой компании много точек по городу и все они хранятся в переменной $pointLocation
 *
 * @package  DDelivery.Point
 */
class DDeliveryPointSelf extends DDeliveryAbstractPoint{
	
	/**
	 * масcив с располажением точек по городу для компании
	 * @var array 
	 */
    private $pointLocation = array();
    
    private $params = array();
    
    private  $allowParams = array('_id', 'name','city_id', 
    		'city', 'region', 'region_id', 'city_type', 
    		'postal_code', 'area', 'kladr', 'company', 
    		'company_id', 'company_code', 'metro', 'description_in', 
    		'description_out', 'indoor_place','address', 
    		'schedule', 'longitude', 'latitude', 'type', 'status', 
    		'has_fitting_room', 'is_cash', 'is_card' );
    
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
    		throw new DDeliveryPointException("Point info param not found");
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
    
    /**
     * фильтрует расположение точек только для текущей компании
     * 
     * @param array $locationConteiner - массив с расположением точек
     * для разных компаний
     * 
     */
    public function filterLocationInfo( $locationConteiner = array() )
    {	
    	$companyID = $this->getDeliveryInfo()->get('delivery_company');
    	
    	foreach ( $locationConteiner as $item  )
    	{
    	    if( $companyID == $item['company_id'])
    	    {
    	    	$this->pointLocation[] = $item;
    	    }
    	}
    }
    
    /**
     * получить масcив с располажением точек по городу для компании
     */
    public function getPointsLocation()
    {
    	return $this->pointLocation;
    }
    
}