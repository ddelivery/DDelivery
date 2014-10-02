<?php
/**
 *
 * @package    DDelivery.Sdk
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */

namespace DDelivery\Sdk;
use DDelivery\Adapter\DShopAdapter;


/**
 * RequestProvider - обрабатывает curl подключения 
 * для обмена данными с сервером DDelivery
 *
 * @package     DDelivery
 */
class RequestProvider
{   
	
	
	/**
	 * @var string
	 */
	private $apiKey;
	/**
	 *
	 * @var bool
	 */
	private $keepActive = true;
	
	/**
	 *
	 * @var string
	 */
	private $defaultServer;
	
	/**
	 * Curl resource
	 * @var resource[]
	 */
	private $curl = array();

    const SERVER_STAGE = 'stage';
    const SERVER_CABINET = 'cabinet';

    const SERVER_STAGENODE = 'stagenode';
    const SERVER_CABINETNODE = 'cabinetnode';

	/**
	 * url до сервера
	 * @var string
	 */
	private $serverUrl = array( self::SERVER_STAGE => 'http://stage.ddelivery.ru/api/v1/',
                                self::SERVER_CABINET => 'http://cabinet.ddelivery.ru/api/v1/',
                                self::SERVER_STAGENODE => 'http://stage.ddelivery.ru/daemon/daemon.js',
                                self::SERVER_CABINETNODE => 'http://cabinet.ddelivery.ru/daemon/daemon.js'
	                           );
	/**
	 * Количество проделанных запросов на сервер ddelivery
	 * @var int
	 */
	public $countRequests = 0;
	
	/**
	 * @param string $apiKey ключ полученный для магазина
	 * @param bool $testMode тестовый шлюз
	 */
	function __construct( $apiKey, $defaultServer )
    {
        $this->apiKey = $apiKey;
        $this->defaultServer = $defaultServer;
    }
    
	public function __destruct()
    {
        foreach ($this->curl as $c)
        {
        	curl_close($c);
        }
    }
    
    /**
     * Работать в с одним подключением: kep-active
     * @param bool $on
     */
    public function setKeepActive($on)
    {
    	$this->keepActive = (bool)$on;
    }
	
    /**
     * Выолняет запрос к серверу ddelivery
     * 
     * @param string $action - действие
     * @param string[] $params - параметры
     * @param string $method - метод запроса
     * @param string $server - сервер для запроса
     *
     * @return DDeliverySDKResponse
     */
	public function request($action, $params = array(), 
	                        $method = 'get', $server = ''){
		
		$this->countRequests++;
		
		if( empty( $server ) || !(array_key_exists($server, $this->serverUrl)) )
			$server = $this->defaultServer;
		
	    $urlSuffix = $this->_setRequest($server, $params);

	    $this->_setSpecificOptionsToRequest( $method, $action, $server, $urlSuffix );
		
	    $result = curl_exec($this->curl[$server]);


	    $response = new DDeliverySDKResponse( $result, $this->curl[$server] );

	    if(!$this->keepActive)
	    {
	    	curl_close($this->curl[$server]);
	    	unset($this->curl[$server]);
	    }
	    
	    return $response;
	}

    /**
     * Выставляет общие  параметры подключения
     * для каждого сервера
     *
     * @param string $server
     * @param string[] $params
     * @return DDeliverySDKResponse
     */
	private function _setRequest( $server, $params )
	{
		if(!$this->keepActive || !array_key_exists($server, $this->curl ) )
		{
			$this->curl[$server] = curl_init();
			curl_setopt($this->curl[$server], CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->curl[$server], CURLOPT_HEADER, 0);
			//curl_setopt($this->curl[$server], CURLOPT_FOLLOWLOCATION, 1); // Не будет работать в защищенных режимах php
            // В реальных интернетах за пинг в секунду убивают
            //curl_setopt($this->curl[$server], CURLOPT_TIMEOUT, 3);
		}
		 
		$urlSuffix = '';
		 
		foreach($params as $key => $value) {
			$urlSuffix .= urlencode($key).'='.urlencode($value) . '&';
		}

        $urlSuffix .= 'sdk_ver=' . DShopAdapter::SDK_VERSION ;

		return $urlSuffix;
	}
	
	/**
	 * Выолняет специфические параметры подключения
	 * для каждого сервера 
	 * 
	 * @param string $action
	 * @param string $method
	 * @param string $server
	 * @param string $urlSuffix
	 */
	private function _setSpecificOptionsToRequest($method, $action, $server, $urlSuffix){
		if( $method == 'get' && ($server == self::SERVER_CABINET || $server ==  self::SERVER_STAGE) ){
			$url = $this->serverUrl[$server] . urlencode($this->apiKey) .'/' . urlencode($action) . '.json?';
			$url .= $urlSuffix;

			curl_setopt($this->curl[$server], CURLOPT_URL, $url);
		}
		else if( $method == 'get' && ( $server == self::SERVER_CABINETNODE || $server == self::SERVER_STAGENODE)  ){
			
			$url = $this->serverUrl[$server] . '?';
			$url .= $urlSuffix;
			curl_setopt($this->curl[$server], CURLOPT_URL, $url);
		}
		else if($method == 'post'){
			$url = $this->serverUrl[$server] . urlencode($this->apiKey) .'/' . urlencode($action) . '.json';

			curl_setopt($this->curl[$server], CURLOPT_URL, $url);
			curl_setopt($this->curl[$server], CURLOPT_POST, true);
			curl_setopt($this->curl[$server], CURLOPT_POSTFIELDS, $urlSuffix);
		}
	}
    
}