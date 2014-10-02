<?php
/**
 * User: dnap
 * Date: 01.10.14
 * Time: 22:49
 */

namespace DDelivery\DB;


use DDelivery\Adapter\DShopAdapter;
use DDelivery\DDeliveryException;
use PDO;

class Utils {
    static function getDBType($dbObject)
    {
        if($dbObject instanceof ConnectInterface) {
            return DShopAdapter::DB_MYSQL;
        }elseif($dbObject instanceof PDO){
            $driverName = $dbObject->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driverName == 'sqlite') {
                return DShopAdapter::DB_SQLITE;
            } elseif($driverName == 'mysql') {
                return DShopAdapter::DB_MYSQL;
            }
        }
        throw new DDeliveryException("Driver of DB is not supported");
    }
} 