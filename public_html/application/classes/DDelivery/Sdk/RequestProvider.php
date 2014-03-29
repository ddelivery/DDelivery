<?php
/**
 *
 * @package    DDelivery.Sdk
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */

namespace DDelivery\Sdk;


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
	/**
	 * url до сервера
	 * @var string
	 */
	private $serverUrl = array('stage' => 'http://stage.ddelivery.ru/api/v1/',
	                           'dev' => 'http://cabinet.ddelivery.ru/api/v1/',
	                           'node' => 'http://dev.ddelivery.ru/daemon/daemon.js');

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
	                        $method = 'get', $server = '')
	{	
		
		if( empty( $server ) || !(array_key_exists($server, $this->serverUrl)) )
			$server = $this->defaultServer;
		
	    $urlSuffix = $this->_setRequest($server, $params);
	    
	    $this->_setSpecificOptionsToRequest($method, $action, $server, $urlSuffix);
	    
	    $result = curl_exec($this->curl[$server]);
	    
	    $response = new DDeliverySDKResponse( $result );
	    
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
			curl_setopt($this->curl[$server], CURLOPT_FOLLOWLOCATION, 1);
		}
		 
		$urlSuffix = '';
		 
		foreach($params as $key => $value) {
			$urlSuffix .= urlencode($key).'='.urlencode($value) . '&';
		}
		
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
	private function _setSpecificOptionsToRequest($method, $action, $server, $urlSuffix)
	{
		if( $method == 'get' && ($server == 'dev' || $server == 'stage') )
		{
			$url = $this->serverUrl[$server] . urlencode($this->apiKey) .'/' . urlencode($action) . '.json?';
			$url .= $urlSuffix;
			curl_setopt($this->curl[$server], CURLOPT_URL, $url);
		}
		else if( $method == 'get' && $server == 'node' )
		{
			$url = $this->serverUrl[$server] . '?';
			$url .= $urlSuffix;
			curl_setopt($this->curl[$server], CURLOPT_URL, $url);
		}
		else if($method == 'post')
		{
			$url = $this->serverUrl[$server] . urlencode($this->apiKey) .'/' . urlencode($action) . '.json';
			curl_setopt($this->curl[$server], CURLOPT_POST, true);
			curl_setopt($this->curl[$server], CURLOPT_POSTFIELDS, $urlSuffix);
		}
	}
    
}