<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:19
 */

namespace DDelivery\DataBase;

/**
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

    public function createTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS cache (
                                 id INTEGER PRIMARY KEY AUTOINCREMENT,
				                 sig TEXT,
				                 data_container TEXT,
				                 expired TEXT
				         )");
    }

    public function isRecordExist( $sig )
    {
        $data = $this->getCacheDataBySig($sig);
        $result = (count($data))?1:0;
        return $result;
    }
    public function isValidCacheRec( $sig )
    {
        $query = 'SELECT data_container FROM cache WHERE sig = ":sig"';
        $sth = $this->pdo->prepare( $query );
    }
    public function getCacheDataBySig( $sig )
    {
        $query = 'SELECT data_container FROM cache WHERE sig = :sig';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $sig );
        $sth = $this->pdo->query( $query );
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
    public function save( $sig, $data_container, $expired )
    {
        $this->pdo->beginTransaction();
        if( $this->isRecordExist( $sig ) )
        {
            $query = 'UPDATE cache SET data_container = :data_container,
                      expired = :expired WHERE sig = :sig';

            $sth = $this->pdo->prepare( $query );
            $sth->bindParam( ':id', $localId );
            $wasUpdate = 1;
        }
        else
        {
            $query = 'INSERT INTO cache (sig, data_container, expired) VALUES
                      (:sig, :data_container, :expired)';
            $sth = $this->pdo->prepare( $query );
        }
        $sth->bindParam( ':data_container', $data_container );
        $sth->bindParam( ':expired', $expired );
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
    public function remove( $sig )
    {
        $this->pdo->beginTransaction();
        $query = 'DELETE FROM cache WHERE sig = ":sig"';
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':sig', $sig );
        $sth->execute();
        $this->pdo->commit();
    }

}