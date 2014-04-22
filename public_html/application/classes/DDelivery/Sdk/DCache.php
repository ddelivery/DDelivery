<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:41
 */

namespace DDelivery\Sdk;
use DDelivery\DataBase\Cache;

/**
 * Class Cache
 * @package DDelivery\Sdk
 */

class DCache
{

    public static  function load( $key )
    {
        $cache = new Cache();
        if( $cache->isRecordExist( $key ) )
        {
            $data_container = $cache->getCacheDataBySig($key);
            return unserialize($data_container);
        }
        else
        {
            return null;
        }

    }

    public static function save( $key, $data, $expired )
    {
        $cache = new Cache();
        $data_container = serialize( $data );
        return $cache->save($key, $data_container, $expired);
    }


}