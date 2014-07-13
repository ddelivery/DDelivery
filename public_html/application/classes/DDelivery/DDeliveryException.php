<?php
/**
 * 
 * Исключения DDelivery
 * 
 * @package    DDelivery
 * 
 * @author  mrozk 
 */

namespace DDelivery;


class DDeliveryException extends \Exception{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}