<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:41
 */

namespace DDelivery\Sdk;
use DDelivery\DataBase\Cache;
use DDelivery\DB\ConnectInterface;
use DDelivery\DDeliveryException;
use DDelivery\DDeliveryUI;

/**
 * Клас для кэширования в контексте объекта
 * Class DCache
 * @package DDelivery\Sdk
 */
class DCache
{

    /**
     * Срок хранения кэша
     * @var int
     */
    public $expired;

    /**
     * Исполььзовать кэш
     * @var bool
     */
    public $enabled;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Cache
     */
    private $cache = null;

    private $pdoTablePrefix;

    /**
     * @param int $expired
     * @param bool $enabled
     * @param ConnectInterface $PDO
     * @param string $pdoTablePrefix
     */
    public function __construct( $expired, $PDO, $enabled = true, $pdoTablePrefix = '' )
    {
        $this->pdo = $PDO;
        $this->pdoTablePrefix = $pdoTablePrefix;
        $this->expired = $expired;
        $this->enabled = $enabled;

        $cache = new Cache($this->pdo, $this->pdoTablePrefix);
        $this->cache = $cache;
    }

    public function get($cityID, $filter_company = ''){
        $cityID = (int) $cityID;
        $data = $this->cache->getCacheDataByCityID($cityID);

        if( count( $data ) ){
            $datetime1 = new \DateTime();
            $datetime2 = new \DateTime( $data[0]->expired );

            if( $datetime1 > $datetime2 || ( (strlen($filter_company) > 0) && ($filter_company!= $data[0]->filter_company))){
                $this->cache->deleteItem($cityID);
                $result = array();
            }else{
                $result = unserialize( $data[0]->data_container );
            }
        }else{
            $result = array();
        }
        return $result;
    }

    public function set($cityID, $content, $filter_company){
        $cityID = (int) $cityID;
        $this->cache->setCacheData( $cityID, serialize($content), $this->expired, $filter_company );
    }

    /**
     * Очистить БД с кэшем
     */
    public function clean(){
        return $this->cache->removeAll();
    }

    /**
     * @return Cache
     */
    private function getCacheObject()
    {
        if(!$this->cache){
            $cache = new Cache($this->pdo, $this->pdoTablePrefix);
            $this->cache = $cache;
        }
        return $this->cache;
    }

    /**
     * Обращение к кэшируемому методу
     *
     * @param string $method - название метода
     * @param array $params - параметры
     *
     * @return mixed
     * @throws DDeliveryException
     */
    public function render( $method, $params = array() )
    {
        if(  method_exists( $this->context, $method ) )
        {
            $sig = $method . '_' . implode('_', $params);

            if( ($result = $this->getCache( $sig )) && $this->enabled )
            {
                return $result;
            }
            else
            {
                $reflectionMethod = new \ReflectionMethod($this->context, $method);
                $result  =  $reflectionMethod->invokeArgs($this->context, $params );
                $this->setCache( $sig, $result, $this->expired);
                return $result;
            }
        }
        throw new DDeliveryException('Cache: method not Exists');
    }

    /**
     * Загрузить запись кэша
     *
     * @param string $sig ключ вызова метода
     *
     * @return mixed|null
     */
    public function getCache( $sig )
    {
        $cache = $this->getCacheObject();
        if( $data_container = $cache->getCacheRec($sig) )
        {
            return unserialize($data_container);
        }
        else
        {
            return null;
        }
    }

    /**
     * Сохранить запись кэша
     *
     * @param string $sig ключ вызова метода
     * @param mixed $data данные для сохранения
     * @param int $expired время жизни кеша в минтуах
     * @return bool
     */
    public function setCache( $sig, $data, $expired )
    {
        $cache = $this->getCacheObject();
        $data_container = serialize( $data );
        if(strlen($data_container) > 65000)
            return false;
        $id = $cache->save($sig, $data_container, $expired);
        return $id;
    }


    /**
     * Очистить устаревшие записи
     * @return bool
     */
    public function cleanExpired()
    {
        return $this->getCacheObject()->removeExpired();
    }





}