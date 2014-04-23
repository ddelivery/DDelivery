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

    public function getCityListByName($name)
    {
        // Сортировка по типу населенного пункта
        $sql = "SELECT * FROM ps_dd_cities WHERE name LIKE :name
                    ORDER BY
                        CASE (type)
                            WHEN 'г' THEN 0
                            WHEN 'пгт' THEN 1
                            WHEN 'городок' THEN 2
                            WHEN 'остров' THEN 3
                            WHEN 'дп' THEN 4
                            WHEN 'кп' THEN 5
                            WHEN 'д' THEN 6
                            WHEN 'п' THEN 7
                            WHEN 'п/ст' THEN 8
                            WHEN 'рп' THEN 9
                            WHEN 'с' THEN 10
                            WHEN 'ст-ца' THEN 11
                            WHEN 'у' THEN 12
                            WHEN 'х' THEN 12
                            ELSE 20
                        END, dpd_id DESC
                    LIMIT 0, 30";

        $sth = $this->pdo->prepare($sql);
        $sth->bindParam( ':name', $name );
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Собирает строчку с названием города для отображения
     * @param $cityData
     * @return string
     */
    public function getDisplayCityName($cityData)
    {
        $displayCityName = $cityData['type'].'. '.$cityData['name'];
        if($cityData['region'] != $cityData['name']) {
            $displayCityName .= ', '.$cityData['region'];
        }
        return $displayCityName;
    }


}