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

namespace DDelivery;

/**
 * Для поиска недостающих классов, сканируем
 * на содержание пакетов в названиях классов
 *
 * @param   string  $className  Название класса
 *
 *
 */
spl_autoload_register(function ( $className ) {
    if(strpos($className, __NAMESPACE__) === 0) {
        $classPath = implode(DIRECTORY_SEPARATOR, explode('\\', $className));

        $filename = __DIR__ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . $classPath . ".php";

        if (is_readable($filename) && file_exists($filename))
        {
            require_once $filename;
        }
        // Тут не должно быть ошибок чтоб работали другие автолоадеры
    }
});
