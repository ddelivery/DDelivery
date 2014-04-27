<?php
/**
*
* @package    DDelivery.Point
*
*/
namespace DDelivery\Point;


/**
 * DDeliveryInfo - Информация по доставке на точку
 * 
 * "delivery_company": 1, - ID компании доставки
 * "delivery_company_name": "PickPoint", - Буквенное название компании доставки
 * "pickup_price": 0, - цена забора
 * "delivery_price": 254, цена доставки
 * "delivery_price_fee": 22.86, - наценка DD
 * "declared_price_fee": 0, - размер страховки
 * "delivery_time_min": 2, - минимальное время доставки
 * "delivery_time_max": 3, - максимальное время доставки
 * "delivery_time_avg": 3, - средняя между максимальный и минимальным временем
 * "return_price": 118, - цена возврата
 * "return_client_price": 0, - цена клиентского возврата
 * "return_partial_price": 236, - цена частичного возврата
 * "total_price": 276.86 – Суммарная цена доставки
 *
 * @property float delivery_price цена доставки
 * @property int delivery_time_min минимальное время доставки
 * @property float pickup_price цена забора
 * @property float total_price Суммарная цена доставки
 * @package  DDelivery.Point
 */
class DDeliveryInfo
{

    /**
     * Реально отображаемая цена.
     * @var float
     */
    public $clientPrice;

	/**
     * Массив с параметрами объекта
     * @var array
     */
	private $params = array();
	
	/**
	 * Разрешенные праметры
	 * @var array
	 */
	private  $allowParams = array('delivery_company', 'delivery_company_name', 
			'pickup_price', 'delivery_price', 'delivery_price_fee',
			'declared_price_fee', 'delivery_time_min', 'delivery_time_max', 
			'delivery_time_avg', 'return_price', 'return_client_price', 'return_partial_price', 
			'total_price');

    /**
     *
     * @param array $initParams - массив со значениями для инициализации
     * @throws DDeliveryPointException
     */
	public function __construct( $initParams = array() )
	{
		if(is_array($initParams))
		{
			foreach ( $initParams as $key => $value)
			{
                $this->set($key, $value);
			}
            $this->clientPrice = $this->total_price;
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
}