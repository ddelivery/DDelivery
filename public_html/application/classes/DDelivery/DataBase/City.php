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

    /**
     * Получает много городов по их id
     * @param $cityIds
     * @param bool $sort отсортировать в том порядке в котором были переданы id?
     */
    public function getCityListById($cityIds, $sort = false)
    {
        foreach($cityIds as $key => $cityId){
            $cityIds[$key] = (int)$cityId;
        }

        $sth = $this->pdo->query("SELECT * FROM ps_dd_cities WHERE _id IN(".implode(',', $cityIds).")");
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $result = array();

        foreach($data as $city) {
            $result[$city['_id']] = $city;
        }


        if($sort){
            uksort($result, function($a, $b) use($cityIds){
                return array_search($a, $cityIds) - array_search($b, $cityIds);
            });
        }
        return $result;
    }

    /**
     * Собирает строчку с названием города для отображения
     * @param $cityData
     * @return string
     */
    public function getDisplayCityName($cityData)
    {
        $displayCityName = $cityData['type'].'. '.$cityData['region'];
        if($cityData['region'] != $cityData['name']) {
            $displayCityName .= ', '.$cityData['name'];
        }
        return $displayCityName;
    }


}