<?php
/**
*
* @package    DDelivery.Sdk
*
* @author  mrozk 
*/
		 
namespace DDelivery\Sdk;

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
     *
     * @return DDeliverySDKResponse
     */
    public function addCourierOrder( $to_city, $delivery_company, $dimensionSide1, 
    		                         $dimensionSide2, $dimensionSide3, $shop_refnum,
                                     $confirmed, $weight, $to_name, $to_phone, $goods_description,
    		                         $declaredPrice, $paymentPrice, $to_street, $to_house, $to_flat,
                                     $to_email = '' )
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
    			'to_email' => $to_email
    	);
    
    	return $this->requestProvider->request( 'order_create', $params,'post' );
    }
    
    
    /**
     * Добавить заказ на самовывоз на обработку DDelivery
     * 
     * @param int $delivery_point  Идентификатор пункта выдачи
     * @param int $dimensionSide1  Сторона 1 (см)
     * @param int $dimensionSide2  Сторона 2 (см)
     * @param int $dimensionSide3  Сторона 3 (см)
     * @param boolean $confirmed   Подвтержденная ли заявка
     * @param float $weight        Вес (кг)
     * @param String $to_name      ФИО получателя
     * @param String $to_phone     Телефон получателя
     * @param String $goods_description  Описание посылки
     * @param float  $declaredPrice Оценочная стоимость (руб)
     * @param float  $paymentPrice  Наложенный платеж (руб)
     *
     * @return DDeliverySDKResponse
     */
    public function addSelfOrder( $delivery_point, $dimensionSide1, $dimensionSide2, $dimensionSide3,
                                  $confirmed = true, $weight, $to_name, $to_phone, $goods_description,
    		                      $declaredPrice, $paymentPrice )
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
    			'payment_price' => $paymentPrice
    	);
        
        return $this->requestProvider->request( 'order_create', $params,'post' );
    }
    
    /**
     * Получить список точек для самовывоза
     * @param mixed $cities список id городов через запятую
     * @param mixed $cities список id компаний через запятую
     * 
     * @return DDeliverySDKResponse
     */
    public function getSelfDeliveryPoints( $companies, $cities  )
    {
    	$params = array(
    			'_action' => 'delivery_points',
    			'cities' => $cities,
    			'companies' => $companies
    	);
    	return $this->requestProvider->request('geoip', $params, 'get', $this->server . 'node');
    }
    
    /**
     * Получить id города по ip
     * @param string $ip ip адрес клиента
     *
     * @return DDeliverySDKResponse
     */
    public function getCityByIp( $ip )
    {	
    	$params = array(
            '_action' => 'geoip',
            'ip' => $ip
        );
    	return $this->requestProvider->request('geoip', $params, 'get', $this->server . 'node');
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
    
    	return $this->requestProvider->request( 'calculator', $params );
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
     * @return DDeliverySDKResponse
     */
    public function calculatorPickupForPoint( $deliveryPoint, $dimensionSide1, 
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
     * 
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

        return $this->requestProvider->request('calculator', $params);
    }
	
    
    /**
     * Получить автокомплит для города
     * 
     * @param string $q Часть строки для поиска
     * 
     * @return DDeliverySDKResponse
     */
    public function getAutoCompleteCity( $q ) {
    	
    	$params = array(
    			'_action' => 'autocomplete',
    			'q' => $q
    	);
    	
    	return $this->requestProvider->request('autocomplete', $params,
    											'get', $this->server . 'node') ;
    }


    /**
     * Возвращает id городов с болшим кол-вом людей, может когда-нибудь будет на сервере
     * @return array
     */
    public function getTopCityId()
    {
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


    /**
     * Получить список пунктов самовывоза
     * @return DDeliverySDKResponse
     */
    public function deliveryPoints() {
        return $this->requestProvider->request('delivery_points') ;
    }



}