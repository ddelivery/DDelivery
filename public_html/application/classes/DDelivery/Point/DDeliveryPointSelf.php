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
 * @property string metro
 * @property string description_in
 * @property string description_out
 * @property string indoor_place
 * @property string address
 * @property string schedule
 * @property float longitude
 * @property float latitude
 * @property int type 1 - ячейка, 2 - живой пункт
 * @property int status
 * @property int has_fitting_room
 * @property int is_cash
 * @property int is_card 
 * @property bool is_custom Точка создана интегратором
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
    		'has_fitting_room', 'is_cash', 'is_card', 'is_custom' );

    /**
     * @param bool $isCustom Точка создана интегратором
     */
    public function __construct( $isCustom = true )
    {
        $this->is_custom = (bool)$isCustom;
    }

    public function init( $initParams = array() )
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
        $params = array('_id', 'name', 'longitude', 'latitude', 'schedule', 'is_cash', 'is_card',
            'has_fitting_room', 'company', 'company_id', 'address', 'type', 'is_custom');
        $result = array();
        foreach($params as $param){
            $result[$param] = $this->get($param);
        }
        return $result;
    }
    
}