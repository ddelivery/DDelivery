<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 22:48
 */
		 
namespace DDelivery;

class DDeliverySDK {
	
	/**
	 * творит запросы
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
    public function __construct($apiKey, $testMode = false)
    {
        if($testMode){
            $this->server = 'stage';
        }else{
            $this->server = 'dev';
        }
        
        $this->requestProvider = new RequestProvider( $apiKey, $this->server );
        $this->requestProvider->setKeepActive( true );
    }
	
    
    function sendCurierOrder( $order )
    {   
    	$params = array();
    	try 
    	{
    		$params = $order->pack();
    	}
    	catch (Order\DDeliveryOrderException $e)
    	{
    		echo $e->getMessage();
    	}
        
        return $this->request('order_create', $params);
    }
    
    function getSelfDeliveryPoints( $cities, $companies = '' )
    {
    	$params = array(
    			'_action' => 'delivery_points',
    			'cities' => $cities,
    			'companies' => $companies 
    	);
    	
    	return $this->request('geoip', $params, true);
    }
    
    /**
     * Получить id города по ip
     * @param string $ip ip адрес клиента
     */
    public function getCityByIp( $ip )
    {	
    	$params = array(
    			'_action' => 'geoip',
    			'ip' => $ip
    			);
    	return $this->request('geoip', $params, true);
    }
    
	
    
    
    /**
     * Выолняет запрос к серверу ddelivery
     * @param string $action
     * @param string[] $params
     * @param string $specificUrl если необходимо обратится к 
     * не к стандартному url
     * 
     * @return DDeliverySDKResponse
     */
    protected function request($action, $params = array(), $useJsDeamon = false)
    {
        if(!$this->keepActive || !$this->curl) {
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($this->curl, CURLOPT_HEADER, 0);
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        }
        if( $useJsDeamon )
        {
            $url = $this->jsDeamon . '?';
        }
        else 
        {
            $url = $this->serverUrl . urlencode($this->apiKey) .'/' . urlencode($action) . '.json?';
        }
        
        foreach($params as $key => $value) {
            $url .= '&'.urlencode($key).'='.urlencode($value);
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);

        $result = curl_exec($this->curl);
		//print_r($result);
        if(!$this->keepActive){
            curl_close($this->curl);
            unset($this->curl);
        }

        return new DDeliverySDKResponse($result);
    }

    /**
     * Расчитать цену самовывоза
     * @param int $deliveryPoint Идентификатор пункта выдачи
     * @param int $dimensionSide1 Сторона 1 (см)
     * @param int $dimensionSide2 Сторона 2 (см)
     * @param int $dimensionSide3 Сторона 3 (см)
     * @param float $weight Вес (кг)
     * @param float|null $declaredPrice Оценочная стоимость (руб)
     * @param float|null $paymentPrice Наложенный платеж (руб)
     * @return DDeliverySDKResponse
     */
    public function calculatorPickup($deliveryPoint, $dimensionSide1, $dimensionSide2, $dimensionSide3, $weight, $declaredPrice = null, $paymentPrice = null)
    {
        $params = array(
            'type' => 1,
            'delivery_point' => $deliveryPoint,
            'dimension_side1' => $dimensionSide1,
            'dimension_side2' => $dimensionSide2,
            'dimension_side3' => $dimensionSide3,
            'weight' => $weight,
        );

        if($declaredPrice !== null)
            $params['declared_price']  = $declaredPrice;

        if($paymentPrice !== null)
            $params['payment_price']  = $paymentPrice;

        return $this->request('calculator', $params);
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
     * @return DDeliverySDKResponse
     */
    public function calculatorCourier($cityTo, $dimensionSide1, $dimensionSide2, $dimensionSide3, $weight, $declaredPrice = null, $paymentPrice = null)
    {
        $params = array(
            'type' => 2,
            'city_to' => $cityTo,
            'dimension_side1' => $dimensionSide1,
            'dimension_side2' => $dimensionSide2,
            'dimension_side3' => $dimensionSide3,
            'weight' => $weight,
        );

        if($declaredPrice !== null)
            $params['declared_price']  = $declaredPrice;

        if($paymentPrice !== null)
            $params['payment_price']  = $paymentPrice;

        return $this->request('calculator', $params);
    }


    /**
     * Получить список пунктов самовывоза
     * @return DDeliverySDKResponse
     */
    public function deliveryPoints() {
    	
        return $this->requestProvider->request('delivery_points') ;
    }



}