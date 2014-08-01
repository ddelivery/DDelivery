<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:19
 */

namespace DDelivery\DataBase;

use DDelivery\Adapter\DShopAdapter;
use PDO;
/**
 *
* Class Cache
* @package DDelivery\DataBase
*/
class Cache {

    /**
     * @var PDO
     */
    private $pdo;
    /**
     * @var int
     */
    private $pdoType;
    /**
     * @var string
     */
    private $prefix;


    public function __construct(PDO $pdo, $prefix = '')
    {
        $this->pdo = $pdo;
        $this->prefix = $prefix;
        if($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) == 'sqlite') {
            $this->pdoType = DShopAdapter::DB_SQLITE;
        }else{
            $this->pdoType = DShopAdapter::DB_MYSQL;
        }
    }

    public function createTable()
    {
        if($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'CREATE TABLE `'.$this->prefix.'cache` (
                      `id`  int NOT NULL AUTO_INCREMENT ,
                      `sig`  varchar(255) NULL ,
                      `data_container`  text NULL ,
                      `expired`  datetime NULL ,
                      PRIMARY KEY (`id`)
                )';

        }else{
            $query = 'CREATE TABLE `'.$this->prefix.'cache` (
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      sig TEXT,
                      data_container TEXT,
                      expired  TEXT

                )';
        }
        $sth = $this->pdo->prepare( $query );

        $sth->execute();
    }

    /**
     * Проверяет, существкт ли запись кэша
     *
     * @param $sig
     *
     * @return int
     */
    public function isRecordExist( $sig )
    {
        $data = $this->getCacheDataBySig($sig);
        $result = (count($data))?1:0;
        return $result;
    }
    /**
     * Проверяет, существкт ли запись кэша и ее актуальность
     *
     * @param $sig
     *
     * @return int
     */
    public function getCacheRec( $sig )
    {
        if($this->pdoType == DShopAdapter::DB_SQLITE) {
            $query = 'SELECT data_container
                FROM '.$this->prefix.'cache
                WHERE sig = :sig AND expired > datetime("now")';
        }elseif($this->pdoType == DShopAdapter::DB_MYSQL){
            $query = 'SELECT data_container
                FROM '.$this->prefix.'cache
                WHERE sig = :sig AND expired > NOW()';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $sig );
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        if( count( $result ) )
        {
            return $result[0]->data_container;
        }
        return null;
    }

    /**
     * Получить запись кэша
     *
     * @param $sig
     *
     * @return null
     */
    public function getCacheDataBySig( $sig )
    {
        if($this->pdoType == DShopAdapter::DB_SQLITE || $this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'SELECT data_container FROM '.$this->prefix.'cache WHERE sig = :sig';
        }

        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $sig );
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);

        if( count( $result ) )
        {
            return $result[0]->data_container;
        }
        else
        {
            return null;
        }

    }

    /**
     * Получить все записи кэша
     * @return array
     */
    public function getAll()
    {
        $query = 'SELECT * FROM '.$this->prefix.'cache';
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * Сохранить данные в кэш
     *
     * @param string $sig ключ
     * @param mixed $data_container данные
     * @param int $expired время истечения в минутах
     *
     * @return bool
     */
    public function save( $sig, $data_container, $expired )
    {
        $expired = (int)$expired;
        $this->pdo->beginTransaction();

        if( $this->isRecordExist( $sig ) )
        {
            if($this->pdoType == DShopAdapter::DB_SQLITE) {
                $query = 'UPDATE '.$this->prefix.'cache
                    SET data_container = :data_container,
                        expired = datetime("now", "+' . $expired . ' minutes")
                    WHERE sig = :sig';
            }elseif($this->pdoType == DShopAdapter::DB_MYSQL){
                $query = 'UPDATE '.$this->prefix.'cache
                    SET
                        data_container = :data_container,
                        expired = NOW() + INTERVAL ' . $expired . ' MINUTE
                    WHERE sig = :sig';
            }
            $sth = $this->pdo->prepare( $query );
        }
        else
        {
            if($this->pdoType == DShopAdapter::DB_SQLITE) {
                $query = 'INSERT INTO cache (sig, data_container, expired) VALUES
                          (:sig, :data_container, datetime("now", "+' . $expired . ' minutes"))';
            }elseif($this->pdoType == DShopAdapter::DB_MYSQL) {
                $query = 'INSERT INTO cache (sig, data_container, expired) VALUES
                          (:sig, :data_container, expired = NOW() + INTERVAL ' . $expired . ' MINUTE)';
            }
            $sth = $this->pdo->prepare( $query );
        }

        $sth->bindParam( ':sig', $sig );
        $sth->bindParam( ':data_container', $data_container );

        $result = $sth->execute();
        $this->pdo->commit();
        return $result;
    }

    /**
     * Удалить те записи у которых вышел срок хранения
     * @return array
     */
    public function removeExpired()
    {
        $this->pdo->beginTransaction();
        if($this->pdoType == DShopAdapter::DB_SQLITE) {
            $query = 'DELETE FROM cache WHERE expired < datetime("now")';
        }elseif($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'DELETE FROM cache WHERE expired < NOW()';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $this->pdo->commit();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * Получить просроченые
     * @return array
     */
    public function selectExpired()
    {
        $this->pdo->beginTransaction();
        if($this->pdoType == DShopAdapter::DB_SQLITE) {
            $query = 'SELECT expired,  datetime("now") AS expired2  FROM
                  cache WHERE expired < datetime("now")';
        }elseif($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'SELECT expired,  NOW() AS expired2
                FROM cache
                WHERE expired < NOW()';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $this->pdo->commit();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Удалить все
     * @return bool
     */
    public function removeAll( )
    {
        $this->pdo->beginTransaction();
        $query = 'DELETE FROM cache';

        $sth = $this->pdo->prepare( $query );
        $result = $sth->execute();
        $this->pdo->commit();
        return $result;
    }

    /**
     *
     * Удалить запись с кэшем
     *
     * @param $sig
     *
     * @return bool
     */
    public function remove( $sig )
    {
        $this->pdo->beginTransaction();
        $query = 'DELETE FROM cache WHERE sig = ":sig"';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $sig );
        if( $sth->execute() )
        {
            $result = true;
        }
        else
        {
            $result = false;
        }
        $this->pdo->commit();
        return $result;
    }



}