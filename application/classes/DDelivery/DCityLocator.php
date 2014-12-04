<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 9/24/14
 * Time: 5:29 PM
 */
namespace DDelivery;

use DDelivery\Sdk\DDeliverySDK;

class DCityLocator{

    public $sdk;

    /**
     * @param DDeliverySDK $sdk
     */
    public function __construct( $sdk ){
        $this->sdk = $sdk;
    }

    /**
     * Возвращает информацию текущего города или пытается определить его
     *
     * @param int $cityId
     *
     * @return array
     */
    public function getCity( $cityId = 0 ){
        $topCityList = $this->getTopCityList();
        if( $cityId > 0 ){
            if( array_key_exists($cityId, $topCityList) ){
                $cityInfo = $topCityList[$cityId];
            }else{
                $city = $this->sdk->getCityById( $cityId );
                if( !empty( $city->response ) ){
                    $cityInfo = $city->response;
                }
            }
        }else{
            $cityInfo = $this->getCityByIp( $_SERVER['REMOTE_ADDR'] );
        }

        if( !isset($cityInfo['_id']) || empty($cityInfo['_id']) ){
            $cityInfo = reset($topCityList);
        }
        $this->getCityNameByDisplay( $cityInfo );
        return $cityInfo;
    }

    /**
     * @param $name
     * @return array|Sdk\DDeliverySDKResponse
     */
    public function getAutoCompleteCity( $name ){
        $cityList = $this->sdk->getAutoCompleteCity($name);
        $cityList = $cityList->response;
        if( !empty($cityList) ){
            foreach($cityList as $key => $city){
                $this->getCityNameByDisplay($cityList[$key]);
            }
            return $cityList;
        }
        return array();
    }

    /**
     * Получаем массив городов для отображения на странцие
     *
     * @param int $cityId
     * @param string $cityName
     *
     * @return array
     */
    public function getCityByDisplay($cityId, $cityName){
        $cityList = $this->getTopCityList();
        if(isset($cityList[$cityId])){
            $cityData = $cityList[$cityId];
            unset($cityList[$cityId]);
            array_unshift($cityList, $cityData);
        }

        if( !in_array($cityId, $cityList) ){
            $topCity = array('_id' => $cityId, 'display_name' => $cityName );
            array_unshift($cityList, $topCity);
        }
        return $cityList;
    }

    /**
     *  Костыль, на сервере города начинаются с маленькой буквы
     *
     * @param $cityData
     *
     * @return $cityData
     */
    public function getCityNameByDisplay( &$cityData ){
        //print_r($cityData);
        $cityData['name'] = Utils::firstWordLiterUppercase($cityData['name']);
        //Собирает строчку с названием города для отображения
        $displayCityName = $cityData['type'].'. '.$cityData['name'];
        if( strpos($cityData['region'], $cityData['name']) === false ) {
            $displayCityName .= ', '.$cityData['region'];
        }
        $cityData['display_name'] = $displayCityName;
        return $cityData;
    }

    /**
     * Получить город по ip адресу
     * @var string $ip
     *
     * @return array;
     */
    public function getCityByIp( $ip ){
        $response = $this->sdk->getCityByIp( $ip );
        if( $response->success && !empty($response->response)){
            $response->response['_id'] = $response->response['city_id'];
            $response->response['name'] = $response->response['city'];
            return $response->response;
        }else{
            return array();
        }
    }

   public function getTopCityList(){
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
                   'display_name' => 'г. Москва'
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
                   'display_name' => 'г. Санкт-Петербург'
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
                   'display_name' => 'г. Новосибирск, Новосибирская обл.'
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
                   'display_name' => 'г. Екатеринбург, Свердловская обл.'
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
                   'display_name' => 'г. Нижний Новгород, Нижегородская обл.'
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
                   'display_name' => 'г. Казань, Татарстан обл.'
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
                   'display_name' => 'г. Самара, Самарская обл.'
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
                   'display_name' => 'г. Омск, Омская обл.'
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
                   'display_name' => 'г. Челябинск, Челябинская обл.'
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
                   'display_name' => 'г. Ростов-на-Дону, Ростовская обл.'
               )
       );
   }
}