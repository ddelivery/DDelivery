<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:19
 */

namespace DDelivery\DataBase;

use DDelivery\Adapter\DShopAdapter;
use DDelivery\DB\ConnectInterface;
use DDelivery\DB\ConstPDO as PDO;

/**
 *
* Class Cache
* @package DDelivery\DataBase
*/
class Cache {

    /**
     * @var ConnectInterface
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


    /**
     * @param $pdo
     * @param string $prefix
     * @throws \DDelivery\DDeliveryException
     */
    public function __construct($pdo, $prefix = '')
    {
        $this->pdo = $pdo;
        $this->prefix = $prefix;
        $this->pdoType = \DDelivery\DB\Utils::getDBType($pdo);
    }

    public function createTable()
    {
        if($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'CREATE TABLE `'.$this->prefix.'cache` (
                      `id`  int NOT NULL,
                      `data_container`  MEDIUMTEXT NULL ,
                      `expired`  datetime NULL,
                      `filter_company` TEXT NULL,
                      PRIMARY KEY (`id`),
                      INDEX `dd_cache` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        }else{
            $query = 'CREATE TABLE `'.$this->prefix.'cache` (
                      id INTEGER PRIMARY KEY,
                      data_container TEXT,
                      expired  TEXT,
                      filter_company TEXT
                    )';
        }
        $sth = $this->pdo->prepare( $query );

        $sth->execute();
    }


    public function getCacheDataByCityID( $cityID ){
        $query = 'SELECT data_container, expired, filter_company
                  FROM '.$this->prefix.'cache
                  WHERE id = :sig';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $cityID );
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function setCacheData( $cityID, $data, $expired, $filter_company ){

        if($this->pdoType == DShopAdapter::DB_SQLITE) {
            $query = 'INSERT INTO '.$this->prefix.'cache (id, data_container, expired, filter_company) VALUES
                          (:sig, :data_container, datetime("now", "+' . $expired . ' minutes"), :filter_company)';
        }elseif($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'INSERT INTO '.$this->prefix.'cache (id, data_container, expired, filter_company) VALUES
                          (:sig, :data_container, ( NOW() + INTERVAL ' . $expired . ' MINUTE ), :filter_company )';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $cityID );
        $sth->bindParam( ':data_container', $data );
        $sth->bindParam( ':filter_company', $filter_company );
        $result = $sth->execute();
        return $result;
    }

    public function deleteItem( $cityID ){
        $query = 'DELETE FROM '.$this->prefix.'cache WHERE id=:sig';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $cityID );
        if( $sth->execute() ){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * Удалить все
     * @return bool
     */
    public function removeAll(){
        $query = 'DELETE FROM '.$this->prefix.'cache';
        $sth = $this->pdo->prepare( $query );
        $result = $sth->execute();
        return $result;
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
        if($this->pdoType == DShopAdapter::DB_SQLITE) {
            $query = 'DELETE FROM cache WHERE expired < datetime("now")';
        }elseif($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = 'DELETE FROM cache WHERE expired < NOW()';
        }
        $sth = $this->pdo->prepare( $query );
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * Получить просроченые
     * @return array
     */
    public function selectExpired()
    {
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
        $result = $sth->fetchAll(PDO::FETCH_OBJ);

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
        return $result;
    }



}