<?php
/**
 * User: DnAp
 * Date: 02.04.14
 * Time: 15:19
 */

namespace DDelivery\DataBase;


use DDelivery\DDeliveryException;

class SQLite {
    public static $dbUri = '';
    private static $pdo;

    /**
     * Возвращает единственный экземпляр PDO SQLite
     * @return \PDO
     * @throws \DDelivery\DDeliveryException
     */
    public static function getPDO() {
        if(!self::$dbUri)
            throw new DDeliveryException('SQLite::dbUri is empty');
        if ( empty(self::$pdo) ) {
            // Проверка прав доступа к папке
            $dirs = explode(DIRECTORY_SEPARATOR, self::$dbUri );
            array_pop($dirs);
            $dbPath = implode( DIRECTORY_SEPARATOR, $dirs);
            if(  (!is_writable( $dbPath )) || ( !is_writable( self::$dbUri ) ) || (!is_dir( $dbPath )) )
            {
                throw new DDeliveryException('Папка с БД SQLite не существует или недоступна для записи');
            }
            self::$pdo = new \PDO('sqlite:'.self::$dbUri);
            self::$pdo->exec('PRAGMA journal_mode=WAL;');
        }

        return self::$pdo;
    }
} 