<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 22.04.14
 * Time: 21:41
 */

namespace DDelivery\Sdk;
use DDelivery\DataBase\Cache;
use DDelivery\DDeliveryException;

/**
 * Клас для кэширования в контексте объекта
 * Class DCache
 * @package DDelivery\Sdk
 */
class DCache
{
    /**
     * Контекст объекта в котором происходит кэширование
     * @var object
     */
    public $context;

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
     * @param      $context
     * @param      $expired
     * @param bool $enabled
     */
    public function __construct( $context, $expired, $enabled = true )
    {
        $this->context = $context;
        $this->expired = $expired;
        $this->enabled = $context;
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
     * @param $sig ключ вызова метода
     *
     * @return mixed|null
     */
    public function getCache( $sig )
    {
        $cache = new Cache();
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
        $cache = new Cache();
        $data_container = serialize( $data );
        $id = $cache->save($sig, $data_container, $expired);
        return $id;
    }

    /**
     * Очистить БД с кэшем
     */
    public function clean()
    {
        $cache = new Cache();
        return $cache->removeAll();
    }

    /**
     * Очистить устаревшие записи
     * @return bool
     */
    public function cleanExpired()
    {
        $cache = new Cache();
        return $cache->removeExpired();
    }





}