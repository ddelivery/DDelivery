<?php
/**
 * User: DnAp
 * Date: 02.04.14
 * Time: 13:59
 */

namespace DDelivery\DataBase;


use PDO;

/**
 * Class City
 * @package DDelivery\SQLite
 */
class City {

    /**
     * @var PDO
     */
    public $pdo;

    function __construct()
    {
        $this->pdo = SQLite::getPDO();
    }

    /**
     * Возвращает город по id
     * @param int $cityId
     * @return array
     */
    public function getCityById($cityId)
    {
        $cityId = (int)$cityId;
        $sth = $this->pdo->query("SELECT * FROM ps_dd_cities WHERE _id = $cityId");
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

}