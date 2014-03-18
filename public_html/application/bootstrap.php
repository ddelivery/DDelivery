<?php

define("DDELIVERY_DS", "/");

function autoloadClasses( $className ) {
	
    $base = realpath(dirname(__FILE__) . DDELIVERY_DS ) ;
    
    $filename = $base . DDELIVERY_DS . "classes" . DDELIVERY_DS 
                . 'Ddelivery' . DDELIVERY_DS 
                . $className . ".php";
    
    if (is_readable($filename) && file_exists($filename)) 
    {
        require_once $filename;
    }
    else
    {
    	throw Exception("Class ddelivery not found");
    }
}

spl_autoload_register("autoloadClasses");