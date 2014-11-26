<?php
/**
*
* @package    DDelivery.Sdk
*
* @author  mrozk 
*/
		 
namespace DDelivery\Sdk;

use DDelivery\DDeliveryException;
/**
 * DDelivery Sdk - API для работы с сервером DDelivery
 *
 * @package     DDelivery\Sdk
 */
class DDeliverySDK {

    /**
     * Тип самовывоз
     */
    const TYPE_SELF = 1;
    /**
     * Тип курьер
     */
    const TYPE_COURIER = 2;

	/**
	 * создает запросы
	 * @var RequestProvider
	 */
	private $requestProvider;
	/**
	 * сервер по умолчанию
	 * @var string
	 */
	private $server;
	
	/**
	 * @param string $apiKey ключ полученный для магазина
	 * @param bool $testMode тестовый шлюз
	 */
    public function __construct($apiKey, $testMode = true)
    {
        if($testMode){
            $this->server = RequestProvider::SERVER_STAGE;
        }else{
            $this->server = RequestProvider::SERVER_CABINET;
        }

        $this->requestProvider = new RequestProvider( $apiKey, $this->server );
        $this->requestProvider->setKeepActive( true );
    }
	
    /**
     * Добавить заказ на курьерку на обработку DDelivery
     *
     * @param int $to_city
     * @param int $delivery_company
     * @param int $dimensionSide1 -
     * @param int $dimensionSide2
     * @param int $dimensionSide3
     * @param String $shop_refnum
     * @param boolean $confirmed
     * @param float $weight
     * @param String $to_name
     * @param String $to_phone
     * @param String $goods_description
     * @param float  $declaredPrice
     * @param float  $paymentPrice
     * @param String  $to_street
     * @param String  $to_house
     * @param String  $to_flat
     * @param String  $to_email
     * @param String $metadata
     * @param string $to_index
     *
     * @throws DDeliveryException
     *
     * @return DDeliverySDKResponse
     */
    public function addCourierOrder( $to_city, $delivery_company, $dimensionSide1, 
    		                         $dimensionSide2, $dimensionSide3, $shop_refnum,
                                     $confirmed, $weight, $to_name, $to_phone, $goods_description,
    		                         $declaredPrice, $paymentPrice, $to_street, $to_house, $to_flat,
                                     $to_email = '', $metadata = '', $to_index )
    {
        $params = array(
            'type' => self::TYPE_COURIER,
            'to_city' => $to_city,
            'delivery_company' => $delivery_company,
            'dimension_side1' => $dimensionSide1,
            'dimension_side2' => $dimensionSide2,
            'dimension_side3' => $dimensionSide3,
            'shop_refnum' =>  $shop_refnum,
            'weight' => $weight,
            'confirmed' => $confirmed,
            'to_name' => $to_name,
            'to_phone' => $to_phone,
            'goods_description' => $goods_description,
            'declared_price' => $declaredPrice,
            'payment_price' => $paymentPrice,
            'to_street' => $to_street,
            'to_house' => $to_house,
            'to_flat' => $to_flat,
            'to_email' => $to_email,
            'metadata' => $metadata,
            'to_index' => $to_index
        );
    	$response = $this->requestProvider->request( 'order_create', $params, 'post' );

    	if( !count ( $response->response ))
    	{
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
        }
    	return $response;
    }
    
    /**
     * Получить статус заказа 
     *
     * @param int $orderID  id заказа на DDelivery
     *
     * @throws \DDelivery\DDeliveryException
     *
     * @return DDeliverySDKResponse
     */
    public function getOrderStatus( $orderID )
    {
    	$params = array( 'order' => $orderID );
    	$response = $this->requestProvider->request( 'order_status', $params, 'get' );
    	
    	if( !count ( $response->response ))
    	{
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            return array();
            throw new DDeliveryException( $errorMsg );
    	}
    	return $response;
    }

    /**
     * Добавить заказ на самовывоз на обработку DDelivery
     *
     * @param int $delivery_point Идентификатор пункта выдачи
     * @param int $dimensionSide1 Сторона 1 (см)
     * @param int $dimensionSide2 Сторона 2 (см)
     * @param int $dimensionSide3 Сторона 3 (см)
     * @param boolean $confirmed Подвтержденная ли заявка
     * @param float $weight Вес (кг)
     * @param String $to_name ФИО получателя
     * @param String $to_phone Телефон получателя
     * @param String $goods_description Описание посылки
     * @param float $declaredPrice Оценочная стоимость (руб)
     * @param float $paymentPrice Наложенный платеж (руб)
     * @param $shop_refnum id заказа cms
     * @param $to_email email  заказа
     * @param $metadata метадвнные заказа
     *
     * @throws \DDelivery\DDeliveryException
     *
     * @return DDeliverySDKResponse
     */
    public function addSelfOrder( $delivery_point, $dimensionSide1, $dimensionSide2, $dimensionSide3,
                                  $confirmed = true, $weight, $to_name, $to_phone, $goods_description,
    		                      $declaredPrice, $paymentPrice, $shop_refnum, $to_email = '', $metadata )
    {   
    	$params = array(
    			'type' => self::TYPE_SELF,
    			'delivery_point' => $delivery_point,
    			'dimension_side1' => $dimensionSide1,
    			'dimension_side2' => $dimensionSide2,
    			'dimension_side3' => $dimensionSide3,
    			'weight' => $weight,
    			'confirmed' => $confirmed,
    			'to_name' => $to_name,
    			'to_phone' => $to_phone,
    			'goods_description' => $goods_description,
    			'declared_price' => $declaredPrice,
    			'payment_price' => $paymentPrice,
    			'shop_refnum' => $shop_refnum,
                'to_email' => $to_email,
                 'metadata' => $metadata
    	);
        
        $response = $this->requestProvider->request( 'order_create', $params,'post' );
        if( !count ( $response->response ))
        {
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
        }
        return $response;
    }

    /**
     *
     * Возможность НПП в городе или регионе
     *
     * @param $city
     * @param $company
     *
     * @return DDeliverySDKResponse $response
     */
    public function paymentPriceEnable( $city, $company ){
        $params = array(
            'city' => $city,
            'company' => $company
        );
        $response = $this->requestProvider->request( 'paymentprice', $params );
        return $response;
    }

    /**
     * Получить список точек для самовывоза
     * @param string $companies список id компаний через запятую
     * @param mixed $cities
     *
     * @throws \DDelivery\DDeliveryException
     * @return DDeliverySDKResponse
     */
    public function getSelfDeliveryPoints( $companies, $cities  ){
    	$params = array(
    			'_action' => 'delivery_points',
    			'cities' => $cities,
    			'companies' => $companies
    	);
    	$response = $this->requestProvider->request('geoip', $params, 'get', $this->server . 'node');
    	if( !$response->success ){
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
    	}
    	return $response;
    }

    /**
     * Получить id города по ip
     * @param string $ip ip адрес клиента
     *
     * @throws \DDelivery\DDeliveryException
     * @return DDeliverySDKResponse
     */
    public function getCityByIp( $ip ){
    	$params = array(
            '_action' => 'geoip',
            'ip' => $ip
        );
    	$response = $this->requestProvider->request('geoip', $params, 'get', $this->server . 'node');
    	if( !$response->success )
    	{
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;

            //throw new DDeliveryException( $errorMsg );
    	}
    	return $response;
    }

    /**
     * Расчитать цену самовывоза для компаний города
     * @param int $deliveryCity Идентификатор города
     * @param int $dimensionSide1 Сторона 1 (см)
     * @param int $dimensionSide2 Сторона 2 (см)
     * @param int $dimensionSide3 Сторона 3 (см)
     * @param float $weight Вес (кг)
     * @param float|null $declaredPrice Оценочная стоимость (руб)
     * @param float|null $paymentPrice Наложенный платеж (руб)
     *
     * @throws \DDelivery\DDeliveryException
     * @return DDeliverySDKResponse
     */
    public function calculatorPickupForCity( $deliveryCity, $dimensionSide1,
                                            $dimensionSide2, $dimensionSide3, $weight,
                                            $declaredPrice, $paymentPrice = null )
    {
    	$params = array(
    			'type' => self::TYPE_SELF,
    			'city_to' => $deliveryCity,
    			'dimension_side1' => $dimensionSide1,
    			'dimension_side2' => $dimensionSide2,
    			'dimension_side3' => $dimensionSide3,
    			'weight' => $weight,
    			'declared_price' => $declaredPrice
    	);
    	
    	if($paymentPrice !== null)
    		$params['payment_price']  = $paymentPrice;
    	
    	$response = $this->requestProvider->request( 'calculator', $params );
    	if( !$response->success )
    	{
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
    	}
    	
    	return $response;
    }

    /**
     * Расчитать цену самовывоза для компании
     * @param int $deliveryPoint Идентификатор пункта выдачи
     * @param int $dimensionSide1 Сторона 1 (см)
     * @param int $dimensionSide2 Сторона 2 (см)
     * @param int $dimensionSide3 Сторона 3 (см)
     * @param float $weight Вес (кг)
     * @param float|null $declaredPrice Оценочная стоимость (руб)
     * @param float|null $paymentPrice Наложенный платеж (руб)
     *
     * @throws \DDelivery\DDeliveryException
     * @return DDeliverySDKResponse
     */
    public function calculatorPickupForPoint(   $deliveryPoint, $dimensionSide1,
                                                $dimensionSide2, $dimensionSide3, $weight, 
                                                $declaredPrice, $paymentPrice = null )
    {
    	
        $params = array(
            'type' => self::TYPE_SELF,
            'delivery_point' => $deliveryPoint,
            'dimension_side1' => $dimensionSide1,
            'dimension_side2' => $dimensionSide2,
            'dimension_side3' => $dimensionSide3,
            'weight' => $weight,
        	'declared_price' => $declaredPrice
        );

        if($paymentPrice !== null)
            $params['payment_price']  = $paymentPrice;
		
        $response = $this->requestProvider->request( 'calculator', $params );
        if( !$response->success )
        {
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
        }
        return $response;
    }

    /**
     * Расчитать цену курьерской доставки
     * @param int $cityTo Идентификатор города получателя
     * @param int $dimensionSide1 Сторона 1 (см)
     * @param int $dimensionSide2 Сторона 2 (см)
     * @param int $dimensionSide3 Сторона 3 (см)
     * @param float $weight Вес (кг)
     * @param float|null $declaredPrice Оценочная стоимость (руб)
     * @param float|null $paymentPrice Наложенный платеж (руб)
     *
     * @throws \DDelivery\DDeliveryException
     * @return DDeliverySDKResponse
     */
    public function calculatorCourier($cityTo, $dimensionSide1,
                                      $dimensionSide2, $dimensionSide3,
    		                          $weight, $declaredPrice, $paymentPrice = null)
    {
        $params = array(
            'type' => self::TYPE_COURIER,
            'city_to' => $cityTo,
            'dimension_side1' => $dimensionSide1,
            'dimension_side2' => $dimensionSide2,
            'dimension_side3' => $dimensionSide3,
            'weight' => $weight,
        	'declared_price' => $declaredPrice
        );
		
        if($paymentPrice !== null)
            $params['payment_price']  = $paymentPrice;
        $response = $this->requestProvider->request('calculator', $params);
        if( !$response->success )
        {
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
        }
        return $response;
    }

    public function getCityById( $id ){
        $params = array(
            '_action' => 'city',
            '_id' => $id
        );
        $response = $this->requestProvider->request('city', $params, 'get', $this->server . 'node') ;
        if( !$response->success ){
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            //throw new DDeliveryException( $errorMsg );
        }
        return $response;
    }

    /**
     * Получить автокомплит для города
     *
     * @param string $q Часть строки для поиска
     *
     * @throws \DDelivery\DDeliveryException
     * @return DDeliverySDKResponse
     */
    public function getAutoCompleteCity( $q ) {
    	
    	$params = array(
    			'_action' => 'autocomplete',
    			'q' => $q
    	);
    	$response = $this->requestProvider->request('autocomplete', $params,
    											    'get', $this->server . 'node') ;
    	if( !$response->success ){
            $errorMsg = (is_array($response->errorMessage))?implode(', ', $response->errorMessage):$response->errorMessage;
            throw new DDeliveryException( $errorMsg );
        }
        return $response;
    }

    /**
     * Возвращает true если ключ валиден
     * @return bool
     */
    function checkApiKey(){
        $result = $this->requestProvider->request('order_status');
        return $result->errorMessage != 'Shop not found!';
    }

    /**
     * Возвращает id городов с болшим кол-вом людей, может когда-нибудь будет на сервере
     * @return array
     */
    public function getTopCityId(){
        return array(
            151184, // 'Москва',
            151185, // 'Санкт-Петербург',
            293, // 'Новосибирск',
            375, // 'Екатеринбург',
            282, // 'Нижний Новгород',
            54, //'Казань',
            345, // 'Самара',
            296, //'Омск',
            434, // 'Челябинск',
            331, // 'Ростов-на-Дону',
        );
    }



}