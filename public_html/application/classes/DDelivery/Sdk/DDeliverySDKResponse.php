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
 * DDeliverySDKResponse - объект содержащий информацию про 
 * ответ от сервера DDelivery
 * 
 * @package     DDelivery
 */
class DDeliverySDKResponse {
    /**
     * Возвращает true при успехе
     * @var bool
     */
    public $success;
    /**
     * Сообщение об ошибке
     * @var null|string
     */
    public $errorMessage = null;
    /**
     * @var array
     */
    public $response = array();

    /**
     * @param string $jsonRaw
     */
    public function __construct( $jsonRaw )
    {	
        $jsonData = json_decode($jsonRaw, true);
        
        if(!$jsonData) {
            $this->success = false;
            $this->errorMessage = 'Unknown error';
            return;
        }
		
        $responseVar = $this->_analiseRequest($jsonData);
        
        $this->success = (bool)$jsonData['success'];
        
        if($this->success) 
        {
        	$this->response = $jsonData[$responseVar];
        }
        elseif(isset($jsonData[$responseVar]) && isset($jsonData[$responseVar]['message'])) 
        {
            $this->errorMessage = $jsonData[$responseVar]['message'];
        }
        else 
        {
            $this->errorMessage = 'Unknown error';
        }
    }

    /**
     * Анализирует в какой переменной находится
     * ответ сервера
     *
     * @param object $jsonData
     * @return string
     */
    private function _analiseRequest( $jsonData )
    {
    	if( array_key_exists( 'response', $jsonData ) )
    	{
    		return 'response';
    	}
    	elseif ( array_key_exists( 'points', $jsonData ) )
    	{
    		return 'points';
    	}
    	elseif ( array_key_exists( 'options', $jsonData ) )
    	{
    		return 'options';
    	}
    	else if( array_key_exists( 'result', $jsonData ) )
    	{
    		return 'result';
    	}
    }

} 