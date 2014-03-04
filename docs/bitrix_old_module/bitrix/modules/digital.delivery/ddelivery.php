<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('sale');

$options = COption::GetOptionString('delivery', 'ddelivery');
if(empty($options) || !$options = @unserialize($options)){
    exit;
}

// инициализируем окружение
CSaleDeliveryHandler::GetBySID('DigitalDelivery');

$action = $_GET['action'];
if($action == 'getPrice'){
    $data = CDigitalDelivery::getPrice((int)$_REQUEST['point']);
    echo json_encode($data);
}
if($action == 'setPoint'){
    if( !empty($_REQUEST['id']) && (int)$_REQUEST['id']){
        $pointId = (int)$_REQUEST['id'];
        $data = CDigitalDelivery::getPrice($pointId, true);
        $_SESSION['ddelivery']['price'] = $data['response']['delivery_price'];
    }
}
if($action == 'getPoints'){
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Type: application/x-javascript');
    $datFileName = $_SERVER["DOCUMENT_ROOT"].'/upload/ddelivery_cache.dat';


    if(!is_file($datFileName) || filesize($datFileName)<1 || filemtime($datFileName) < time() - 24*3600){
        $context  = stream_context_create(array('http' =>array('timeout' => 29)));
        $url = 'http://'.dDeliveryLib::URL.'/api/v1/'.$options['API_KEY'].'/delivery_points.json';
		$data = file_get_contents($url, false, $context);

        if(!$data){ // error
            echo '{"success":false,"response":[]}';
        }else{
            echo $data;
        }
        file_put_contents($datFileName, $data);
    }else{
        $fp = fopen($datFileName, 'r');
        $data = "";
        while (!feof($fp)) {
            $data .= fread($fp, 1024);
        }
        fclose($fp);
    }
    $companyIgnore = array();
    $size = array($_GET['x'], $_GET['y'], $_GET['z']);
    sort($size);
    foreach(dDeliveryLib::getCompanySize() as $id => $conf){
        if($options['COMPANY_'.$id] == "N"){
            $companyIgnore[] = $id;
            continue;
        }
        if($_GET['w'] > $conf['w']){
            $companyIgnore[] = $id;
            continue;
        }
        if(isset($conf['s'])){
            if(array_sum($size) > $conf['s']){
                $companyIgnore[] = $id;
            }
            continue;
        }elseif($size[0] > $conf['x'] || $size[1] > $conf['y'] || $size[2] > $conf['z']){
            $companyIgnore[] = $id;
            continue;
        }
    }
    if($data){
        $data = json_decode($data, true);
        $filtredData = array();
        foreach($data['response'] as $point){
            if(in_array($point['company'], $companyIgnore)) {
                continue;
            }
            $filtredData[] = $point;
        }
        echo 'var DDeliveryPostomats = {"success":false,"response": '.json_encode($filtredData).' }';
    }else{
        echo 'var DDeliveryPostomats = {"success":false,"response":[]}';
    }



    echo ";if(typeof(ddEngine) == 'undefined'){ ddEngine = {}; }";
    echo "ddEngine.postomats = DDeliveryPostomats.response;";
}
/*
if($action == 'routes'){
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Type: application/x-javascript');
    $datFileName = $_SERVER["DOCUMENT_ROOT"].'/upload/ddelivery_cache_route.dat';

    echo "var DDeliveryRoutes = ";
    if(!is_file($datFileName) || filemtime($datFileName) < time() - 24*3600){
        $context  = stream_context_create(array('http' =>array('timeout' => 29)));
        $url = 'http://'.dDeliveryLib::URL.'/api/v1/'.$options['API_KEY'].'/prices_by_city.json';
		echo $url;
        $data = file_get_contents($url, false, $context);

        if(!$data){ // error
            echo '{"success":false,"response":[]}';
        }else{
            echo $data;
        }
        file_put_contents($datFileName, $data);
    }else{
        $fp = fopen($datFileName, 'r');
        while (!feof($fp)) {
            echo fread($fp, 1024);
        }
        fclose($fp);
    }
}*/
