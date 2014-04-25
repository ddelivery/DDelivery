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
     * @deprecated
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
     * @return array
     */
    public function getTopCityList()
    {
        return array (
            151184 =>
                array (
                    '_id' => '151184',
                    'name' => 'Москва',
                    'area' => NULL,
                    'region' => 'Москва',
                    'kladr' => '77000000000',
                    'type' => 'г',
                    'dpd_id' => '49694102',
                    'priority' => NULL,
                ),
            151185 =>
                array (
                    '_id' => '151185',
                    'name' => 'Санкт-Петербург',
                    'area' => NULL,
                    'region' => 'Санкт-Петербург',
                    'kladr' => '78000000000',
                    'type' => 'г',
                    'dpd_id' => '49694167',
                    'priority' => NULL,
                ),
            293 =>
                array (
                    '_id' => '293',
                    'name' => 'Новосибирск',
                    'area' => NULL,
                    'region' => 'Новосибирская',
                    'kladr' => '54000001000',
                    'type' => 'г',
                    'dpd_id' => '49455627',
                    'priority' => NULL,
                ),
            375 =>
                array (
                    '_id' => '375',
                    'name' => 'Екатеринбург',
                    'area' => NULL,
                    'region' => 'Свердловская',
                    'kladr' => '66000001000',
                    'type' => 'г',
                    'dpd_id' => '48994107',
                    'priority' => NULL,
                ),
            282 =>
                array (
                    '_id' => '282',
                    'name' => 'Нижний Новгород',
                    'area' => NULL,
                    'region' => 'Нижегородская',
                    'kladr' => '52000001000',
                    'type' => 'г',
                    'dpd_id' => '49323117',
                    'priority' => NULL,
                ),
            54 =>
                array (
                    '_id' => '54',
                    'name' => 'Казань',
                    'area' => NULL,
                    'region' => 'Татарстан',
                    'kladr' => '16000001000',
                    'type' => 'г',
                    'dpd_id' => '49203292',
                    'priority' => NULL,
                ),
            345 =>
                array (
                    '_id' => '345',
                    'name' => 'Самара',
                    'area' => NULL,
                    'region' => 'Самарская',
                    'kladr' => '63000001000',
                    'type' => 'г',
                    'dpd_id' => '49590917',
                    'priority' => NULL,
                ),
            296 =>
                array (
                    '_id' => '296',
                    'name' => 'Омск',
                    'area' => NULL,
                    'region' => 'Омская',
                    'kladr' => '55000001000',
                    'type' => 'г',
                    'dpd_id' => '49125342',
                    'priority' => NULL,
                ),
            434 =>
                array (
                    '_id' => '434',
                    'name' => 'Челябинск',
                    'area' => NULL,
                    'region' => 'Челябинская',
                    'kladr' => '74000001000',
                    'type' => 'г',
                    'dpd_id' => '49265227',
                    'priority' => NULL,
                ),
            331 =>
                array (
                    '_id' => '331',
                    'name' => 'Ростов-на-Дону',
                    'area' => NULL,
                    'region' => 'Ростовская',
                    'kladr' => '61000001000',
                    'type' => 'г',
                    'dpd_id' => '49270397',
                    'priority' => NULL,
                )
        );
    }


}