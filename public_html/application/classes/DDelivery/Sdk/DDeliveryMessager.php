<?php
/**
 *
* @package    DDelivery.Sdk
*
* @author  mrozk
*/
	
namespace DDelivery\Sdk;

/**
 * Печаталка сообщений про ошибки
 * 
 * @author mrozk
 *
 */
class DDeliveryMessager
{   
	/**
	 * @var array массив сообщений для пользователя
	 */
    public $messages = array();
    
    /**
     * @var bool тестовый режим модуля
     */
	public $testMode;
	
	/**
	 * @param bool тестовый режим модуля
	 */
	public function __construct( $testMode )
    {
       $this->testMode = $testMode;
    }
    
    /**
     * @param string добавить
     */
    public function pushMessage( $message )
    {
        if( $this->testMode )
        {
            echo $message;
        }
        array_push( $this->messages, $message );
    }
}