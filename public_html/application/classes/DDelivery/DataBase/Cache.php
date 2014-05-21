<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:19
 */

namespace DDelivery\DataBase;

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
    public $pdo;


    public function __construct()
    {
        $this->pdo = SQLite::getPDO();
        $this->createTable();
    }

    /**
     * Создать таблицу
     */
    public function createTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cache (
                                 id INTEGER PRIMARY KEY AUTOINCREMENT,
				                 sig TEXT,
				                 data_container TEXT,
				                 expired TEXT
				         )");
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
        $query = 'SELECT data_container FROM cache WHERE sig = :sig AND expired > datetime("now")';
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
     * Получить запись кэша
     *
     * @param $sig
     *
     * @return null
     */
    public function getCacheDataBySig( $sig )
    {
        $query = 'SELECT data_container FROM cache WHERE sig = :sig';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $sig );
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
        $query = 'SELECT * FROM cache ';
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
        $this->pdo->beginTransaction();

        if( $this->isRecordExist( $sig ) )
        {
            $query = 'UPDATE cache SET data_container = :data_container,
                      expired = datetime("now", "+' . $expired . ' minutes") WHERE sig = :sig';
            $sth = $this->pdo->prepare( $query );
        }
        else
        {

            $query = 'INSERT INTO cache (sig, data_container, expired) VALUES
                          (:sig, :data_container, datetime("now", "+' . $expired . ' minutes"))';
            $sth = $this->pdo->prepare( $query );
        }

        $sth->bindParam( ':sig', $sig );
        $sth->bindParam( ':data_container', $data_container );

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

    /**
     * Удалить те записи у которых вышел срок хранения
     * @return array
     */
    public function removeExpired()
    {
        $this->pdo->beginTransaction();
        $query = 'DELETE FROM cache WHERE expired < datetime("now")';
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
        $query = 'SELECT expired,  datetime("now") AS expired2  FROM
                  cache WHERE expired < datetime("now")';
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