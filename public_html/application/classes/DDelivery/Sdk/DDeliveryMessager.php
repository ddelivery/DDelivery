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
    public static $messages = array();
    
    /**
     * @param string добавить
     */
    public static function pushMessage( $message )
    {
        array_push( self::$messages, $message );
    }
}