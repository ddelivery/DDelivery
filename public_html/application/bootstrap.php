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
header('Content-type: text/plain; charset=utf-8');

/**
 * Для поиска недостающих классов, сканируем
 * на содержание пакетов в названиях классов
 *
 * @param   string  $className  Название класса
 *
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
    	$classPath = DDELIVERY_DS . $className;
    }
	
    $base = realpath(dirname(__FILE__) . DDELIVERY_DS ) ;
    $filename = $base . DDELIVERY_DS . "classes" . $classPath . ".php";
    
    if (is_readable($filename) && file_exists($filename)) 
    {	
    	
        require_once $filename;
    }
    else
    {	
    	die('Error loading libs ' . $filename);
    }
}

spl_autoload_register("autoloadClasses");
