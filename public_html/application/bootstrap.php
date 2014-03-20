<?php
/**
 * 
 * Отвечает за автозагрузку классов. Регистрирует в функции spl
 * не найденные классы и учитывая ихнее пакетное размещение 
 * добавляет в проэкт 
 * 
 * @package    DDelivery
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * @author  mrozk <mrozk2012@gmail.com>
 */

define("DDELIVERY_DS", "/");

/**
 * Для поиска недостающих классов
 *
 * @param   string  $className  Название класса
 *
 * @throws DDeliveryException
 *
 */
function autoloadClasses( $className ) {
	$classPath = '';
	
    if( (strpos($className, '\\')) > 0 )
    {
        $pathPieces = explode('\\', $className);
        for ($i = 0; $i < count($pathPieces); $i++)
        {
            $classPath .= ( DDELIVERY_DS . $pathPieces[$i] );
        }
    	          		
    }
    else 
    {
        $classPath = $className;
    }
	
    $base = realpath(dirname(__FILE__) . DDELIVERY_DS ) ;
    $filename = $base . DDELIVERY_DS . "classes" . $classPath . ".php";
    
    if (is_readable($filename) && file_exists($filename)) 
    {	
    	
        require_once $filename;
    }
    else
    {	
    	throw new DDeliveryException('Class' . $className . ' not found');
    }
}

spl_autoload_register("autoloadClasses");
