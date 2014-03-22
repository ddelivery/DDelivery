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

/**
 * DDelivery Sdk - API для работы с сервером DDelivery
 *
 * @package     DDelivery
 */
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
	
    
    function sendSelfOrder( $order )
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
        
        //return $this->requestProvider->request('order_create', $params, 'post');
    }
    
    /**
     * Получить список точек для самовывоза
     * @param mixed $cities список id городов через запятую
     * @param mixed $cities список id компаний через запятую
     */
    function getSelfDeliveryPoints( $cities, $companies = '' )
    {
    	$params = array(
    			'_action' => 'delivery_points',
    			'cities' => $cities,
    			'companies' => $companies 
    	);
    	return $this->requestProvider->request('geoip', $params, 'get', 'node');
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
    	return $this->requestProvider->request('geoip', $params, 'get', 'node');
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

        return $this->requestProvider->request( 'calculator', $params );
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
    public function getAutoCompleteCity( $q ) {
    	
    	$params = array(
    			'_action' => 'autocomplete',
    			'q' => $q
    	);
    	
    	return $this->requestProvider->request('autocomplete', $params,
    											'get', 'node') ;
    }
    

    /**
     * Получить список пунктов самовывоза
     * @return DDeliverySDKResponse
     */
    public function deliveryPoints() {
        return $this->requestProvider->request('delivery_points') ;
    }



}