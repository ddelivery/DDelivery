<?php
namespace DDelivery;

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
	 * @var resource
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
	
	public function request($action, $params = array(), 
	                        $method = 'get', $server = '')
	{
	    if( empty( $server ) )
	        $server = $this->defaultServer;
	    
	    if(!$this->keepActive || !array_key_exists($server, $this->curl ) ) 
	    {
	    	$this->curl[$server] = curl_init();
	    	curl_setopt($this->curl[$server], CURLOPT_RETURNTRANSFER, TRUE);
	    	curl_setopt($this->curl[$server], CURLOPT_HEADER, 0);
	    	curl_setopt($this->curl[$server], CURLOPT_FOLLOWLOCATION, 1);
	    }
	    
	    $urlSyfix = '';
	    
	    foreach($params as $key => $value) {
	    	$urlSyfix .= '&'.urlencode($key).'='.urlencode($value);
	    }
	    
	    if( $method == 'get' && ($server == 'dev' || $server == 'stage') )
	    {	
	    	$url = $this->serverUrl[$server] . urlencode($this->apiKey) .'/' . urlencode($action) . '.json?';
	    	$url .= $urlSyfix;
	    	curl_setopt($this->curl[$server], CURLOPT_URL, $url);
	    }
	    else if( $method == 'get' && $server == 'node' )
	    {
	    	$url = $this->serverUrl[$server] . '?';
	    	$url .= $urlSyfix;
	    	curl_setopt($this->curl[$server], CURLOPT_URL, $url);
	    }
	    else if($method == 'post')
	    {
	    	$url = $this->serverUrl[$server] . urlencode($this->apiKey) .'/' . urlencode($action) . '.json';
	    	curl_setopt($this->curl[$server], CURLOPT_POST, true);
	    	curl_setopt($this->curl[$server], CURLOPT_POSTFIELDS, $urlSyfix);
	    }
	    
	    $result = curl_exec($this->curl[$server]);
	   
	    if(!$this->keepActive){
	    	curl_close($this->curl[$server]);
	    	unset($this->curl[$server]);
	    }
	    
	    return $result;
	}
    
}