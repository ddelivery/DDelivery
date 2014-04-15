<?php
/**
 *
 * @package    DDelivery.Point
 *
 * @author  mrozk 
 */
namespace DDelivery\Point;

/**
 * DDeliveryPointSelf - точка самовывоза
 *
 * @property int _id
 * @property int name
 * @property int city_id
 * @property int city
 * @property int region
 * @property int region_id
 * @property int city_type
 * @property int postal_code
 * @property int area
 * @property int kladr
 * @property int company
 * @property int company_id
 * @property int company_code
 * @property int metro
 * @property int description_in
 * @property int description_out
 * @property int indoor_place
 * @property int address
 * @property int schedule
 * @property int longitude
 * @property int latitude
 * @property int type
 * @property int status
 * @property int has_fitting_room
 * @property int is_cash
 * @property int is_card
 *
 * @package  DDelivery.Point
 */
class DDeliveryPointSelf extends DDeliveryAbstractPoint{
	/**
	 * масcив с располажением точек по городу для компании
	 * @deprecated
	 */
    private $pointLocation = array();

    protected $params = array();
    
    protected $allowParams = array('_id', 'name','city_id',
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

    function __get($name)
    {
        return $this->get($name);
    }

    function __set($name, $value)
    {
        $this->set($name, $value);
    }


    /**
     * Возвращает только информацию которая может понадобиться на карте
     * @return array
     */
    public function toJson()
    {
        $params = array('_id', 'name', 'longitude', 'latitude', 'schedule', 'is_cash', 'is_card', 'has_fitting_room', 'company', 'company_id', 'address');
        $result = array();
        foreach($params as $param){
            $result[$param] = $this->get($param);
        }
        return $result;
    }
    
}