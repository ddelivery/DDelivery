<?
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

IncludeModuleLangFile(__FILE__);
CModule::AddAutoloadClasses(
    'digital.delivery',
    array(
        'dDeliveryLib' => 'dDeliveryLib.php',
        'dDeliveryProduct' => 'dDeliveryLib.php',
    )
);

if (!function_exists('str_utf8')) {
    function str_utf8($str) {
        if (defined('BX_UTF')) {
            return $str;
        }
        global $APPLICATION;
        return $APPLICATION->ConvertCharset($str, 'utf-8', SITE_CHARSET);
    }
}


class CDigitalDelivery
{
    const PAYMENT_TYPE_ANY = 0;
    const PAYMENT_TYPE_ONLINE = 1;
    const PAYMENT_TYPE_OFFLINE = 2;
    const URL = 'cabinet.ddelivery.ru';
    function Init()
    {
        UnRegisterModuleDependences('sale', 'OnOrderAdd', 'digital.delivery', 'CDigitalDelivery', 'OnOrderAdd');
        UnRegisterModuleDependences('sale', 'OnOrderUpdate', 'digital.delivery', 'CDigitalDelivery', 'OnOrderUpdate');
        RegisterModuleDependences('sale', 'OnOrderNewSendEmail', 'digital.delivery', 'CDigitalDelivery', 'OnOrderNewSendEmail');
        include(__DIR__.'/install/version.php');
        $options = COption::GetOptionString('delivery', 'ddelivery');

/*
        $dbPaySystem = CSalePaySystem::GetList(
            array("SORT" => "ASC", "PSA_NAME" => "ASC"),
            array("ACTIVE" => 'Y')
        );
        while ($arPaySystem = $dbPaySystem->Fetch())
        {

           var_dump($arPaySystem);
        }*/

        if($options && $options = unserialize($options)){
            CJSCore::Init(array('jquery'));
            $products = array();
            /**
             * @var dDeliveryProduct $object
             */
            $object = self::Calc($options, $products);

            $tplHtml = file_get_contents(__DIR__.'/template.php');
            $tplHtml = str_utf8($tplHtml);

            $html = GetMessage('DIGITAL_DELIVERY_PROFILE_DESCRIPTION');
            $html.='
<script type="text/javascript">
if(window.jQuery==undefined) {
    document.write(unescape("%3Cscript src=\'//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js\' type=\'text/javascript\'%3E%3C/script%3E"));
}
</script>
'.$tplHtml.'
<link rel="stylesheet" href="/ddelivery/stylesheet.css" type="text/css" media="screen" />
<script src="http://'.CDigitalDelivery::URL.'/media/map/js/js.js" charset="utf-8"></script>
<script src="http://'.CDigitalDelivery::URL.'/media/js/ddengine/v.0.0.1/engine_v2.js" charset="utf-8"></script>
<script src="/ddelivery/ddelivery.php?action=getPoints&x='.$object->X.'&z='.$object->Z.'&y='.$object->Y.'&w='.$object->W.'" charset="utf-8"></script>
<script src="/ddelivery/ddelivery.js?'.$arModuleVersion['VERSION'].'" charset="UTF-8"></script>
<script>DDeliveryBitrix.Init('.json_encode(array('x'=>$object->X, 'y'=>$object->Y, 'z'=>$object->Z, 'w'=>$object->W)).');</script>
';

            $html = str_replace(array("\n", "\r"), array(' ', ''), $html);
        }else{
            $html = GetMessage('DIGITAL_DELIVERY_NOT_INSTALL');
        }
        return array(
            /* Basic description */
            "SID" => "DigitalDelivery",
            "NAME" => GetMessage('DIGITAL_DELIVERY_NAME'),
            "DESCRIPTION" => GetMessage('DIGITAL_DELIVERY_DESCRIPTION'),
            "DESCRIPTION_INNER" => GetMessage('DIGITAL_DELIVERY_DESCRIPTION_INNER'),
            "BASE_CURRENCY" => "RUB",//COption::GetOptionString("sale", "default_currency", "RUB"),

            "HANDLER" => __FILE__,

            /* Handler methods */
            "DBGETSETTINGS" => array(__CLASS__, "GetSettings"),
            "DBSETSETTINGS" => array(__CLASS__, "SetSettings"),
            "GETCONFIG" => array(__CLASS__, "GetConfig"),

            "COMPABILITY" => array(__CLASS__, "Compability"),
            "CALCULATOR" => array(__CLASS__, "Calculate"),

            /* Список профилей */
            "PROFILES" => array(
                "all" => array(
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_PROFILE_NAME'),
                    "DESCRIPTION" => $html,
                    "RESTRICTIONS_WEIGHT" => array(0),
                    "RESTRICTIONS_SUM" => array(0),
                ),
            )
        );
    }
    /* Запрос конфигурации службы доставки */
    function GetConfig()
    {
        $dbProps = CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                "ACTIVE" => 'Y',
                "USER_PROPS" => "Y",
                'REQUIED' => 'Y',
            ),
            false,
            false,
            array()
        );
        $props = array();
        while($prop = $dbProps->Fetch()){
            $props[$prop['CODE']] = $prop['NAME'];
        }

        $companyList = array(6 => GetMessage("DIGITAL_DELIVERY_CONFIG_GROUPS_COMPANY_SDEC"),
            4 => "Boxberry",
            11 => "Hermes",
            2 => "IM Logistics",
            16 => GetMessage("DIGITAL_DELIVERY_CONFIG_GROUPS_COMPANY_IM_LOG2"),
            17 => GetMessage("DIGITAL_DELIVERY_CONFIG_GROUPS_COMPANY_IM_LOG3"),
            3 => "Logibox",
            14 => "Maxima Express",
            1 => "PickPoint",
            7 => "QIWI",
        );

        $arConfig = array(
            "CONFIG_GROUPS" => array(
                "all" => GetMessage('DIGITAL_DELIVERY_CONFIG_GROUPS_ALL'),
                "price" => GetMessage('DIGITAL_DELIVERY_CONFIG_GROUPS_PRICE'),
                "company" => GetMessage("DIGITAL_DELIVERY_CONFIG_GROUPS_COMPANY")
            ),

            "CONFIG" => array(
                "API_KEY"=> array(
                    "TYPE" => "STRING",
                    "DEFAULT" => '',
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_API_KEY'),
                    "GROUP" => "all"
                ),
                /*"PAYMENT_TYPE"=> array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => self::PAYMENT_TYPE_ANY,
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_PAYMENT_TYPE'),
                    "VALUES" => array(
                        self::PAYMENT_TYPE_ANY => GetMessage('DIGITAL_DELIVERY_CONFIG_PAYMENT_TYPE_ANY'),
                        self::PAYMENT_TYPE_ONLINE => GetMessage('DIGITAL_DELIVERY_CONFIG_PAYMENT_TYPE_ONLINE'),
                        self::PAYMENT_TYPE_OFFLINE => GetMessage('DIGITAL_DELIVERY_CONFIG_PAYMENT_TYPE_OFFLINE')
                    ),
                    "GROUP" => "all"
                ),*/
                "DEFAULT_X" => array(
                    "TYPE" => "INTEGER",
                    "DEFAULT" => "",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_DEFAULT_X'),
                    "GROUP" => "all"
                ),
                "DEFAULT_Z" => array(
                    "TYPE" => "INTEGER",
                    "DEFAULT" => "",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_DEFAULT_Z'),
                    "GROUP" => "all"
                ),
                "DEFAULT_Y" => array(
                    "TYPE" => "INTEGER",
                    "DEFAULT" => "",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_DEFAULT_Y'),
                    "GROUP" => "all"
                ),
                "DEFAULT_W" => array(
                    "TYPE" => "INTEGER",
                    "DEFAULT" => "",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_DEFAULT_W'),
                    "GROUP" => "all"
                ),
                "PROP_FIO" => array(
                    "TYPE"=>"DROPDOWN",
                    "DEFAULT" => "FIO",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_PROP_FIO'),
                    "GROUP" => "all",
                    "VALUES" => $props,
                ),
                "PROP_PHONE" => array(
                    "TYPE"=>"DROPDOWN",
                    "DEFAULT" => "PHONE",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_PROP_PHONE'),
                    "GROUP" => "all",
                    "VALUES" => $props,
                ),

                "ASSESSED_VALUE"=> array(
                    "TYPE" => "INTEGER",
                    "DEFAULT" => '100',
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_ASSESSED_VALUE'),
                    "GROUP" => "all"
                )
            ),
        );

        foreach($companyList as $key => $company){
            $arConfig['CONFIG']["COMPANY_".$key] = array(
                "TYPE" => "CHECKBOX",
                "DEFAULT" => 'Y',
                "TITLE" => $company,
                "GROUP" => "company",
            );
        }


        for($i=1; $i<=3;$i++){
            $arConfig['CONFIG']["PRICE_IF_".$i] = array(
                "TYPE" => "DROPDOWN",
                "DEFAULT" => '',
                "TITLE" => '#'.$i.'  '.GetMessage('DIGITAL_DELIVERY_CONFIG_PRICE_IF'),
                "GROUP" => "price",
                "VALUES" => array(
                    "" => "...",
                    ">" => GetMessage("DIGITAL_DELIVERY_CONFIG_PRICE_MORE"),
                    "<" => GetMessage("DIGITAL_DELIVERY_CONFIG_PRICE_LESS"),
                ),
            );

            $arConfig['CONFIG']["PRICE_SUM_".$i] = array(
                "TYPE" => "INTEGER",
                "DEFAULT" => '',
                "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_PRICE_SUM'),
                "GROUP" => "price",
            );

            $arConfig['CONFIG']["PRICE_TYPE_".$i] = array(
                "TYPE" => "DROPDOWN",
                "DEFAULT" => '100',
                "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_PRICE'),
                "GROUP" => "price",
                "VALUES" => array(
                    "A" => GetMessage("DIGITAL_DELIVERY_CONFIG_PRICE_VAL_ALL"),
                    "F" => GetMessage("DIGITAL_DELIVERY_CONFIG_PRICE_FACTOR"),
                    "M" => GetMessage("DIGITAL_DELIVERY_CONFIG_PRICE_MINUS"),
                    "AC" => GetMessage("DIGITAL_DELIVERY_CONFIG_PRICE_VAL_ALL_CLIENT"),
                ),
            );

            $arConfig['CONFIG']["PRICE_VALUE_".$i] = array(
                "TYPE" => "INTEGER",
                "DEFAULT" => '',
                "TITLE" => GetMessage('DIGITAL_DELIVERY_CONFIG_PRICE_VALUE'),
                "GROUP" => "price",
            );
        }

        $cCatalog = new CCatalog();
        $res = $cCatalog->GetList();
        while($catalog = $res->Fetch() ){
            $key = 'IBLOCK_'.$catalog['IBLOCK_ID'];
            $iblockProperty = array(0 => GetMessage('DIGITAL_DELIVERY_DEFAULT'));
            $res = CIBlockProperty::GetList(Array(), Array( "IBLOCK_ID"=>3));
            while($prop = $res->Fetch()){
                $iblockProperty[$prop['ID']] = $prop['NAME'];
            }

            $arConfig['CONFIG_GROUPS'][$key] = $catalog['NAME'];
            foreach(array('X', 'Y', 'Z', 'W') as $key2){
                $arConfig['CONFIG'][$key.'_'.$key2] = array(
                    "TYPE" => "DROPDOWN",
                    "TITLE" => GetMessage('DIGITAL_DELIVERY_'.$key2),
                    "GROUP" => $key,
                    "DEFAULT" => 0,
                    "VALUES" => $iblockProperty,
                );
            }
        }
        // var_dump($arConfig);

        return $arConfig;
    }

    function GetSettings($strSettings)
    {
        return unserialize($strSettings);
    }

    function SetSettings($arSettings)
    {
        $string = serialize($arSettings);
        if($arSettings){
            $oldSetting = COption::GetOptionString('delivery', 'ddelivery', $string);
            if($oldSetting) {
                $oldSetting = unserialize($oldSetting);
                if( $oldSetting && $oldSetting['API_KEY'] != $arSettings['API_KEY']){
                    self::clearCache();
                }
            }
        }
        COption::SetOptionString('delivery', 'ddelivery', $string);

        return $string;
    }

    private static function clearCache(){
        unlink($_SERVER["DOCUMENT_ROOT"].'/upload/ddelivery_cache.dat');
    }

    /**
     * @param $arConfig
     * @return CDigitalDeliveryProduct
     */
    static function Calc($arConfig, &$products = array())
    {
        $arSelect = array('ID');
		
        foreach($arConfig as $name => $val){
            if(preg_match('/^IBLOCK_[0-9]+_([XYZW])$/', $name, $math) > 0){
			    $arSelect[$math[1]] = 'PROPERTY_'.(is_array($val) ? $val['VALUE'] : $val);
            }
        }

        $CSB = new CSaleBasket();
        $res = $CSB->GetList(array(), array("FUSER_ID" => $CSB->GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"));
        //$products  = array();
        while($product = $res->Fetch()){
            if( $product['DELAY'] == 'N'){
                $products[$product['PRODUCT_ID']] = new dDeliveryProduct($arConfig, $product['QUANTITY'], $product['NAME'].' ('.$product['QUANTITY'].' шт)');
            }
        }
		$CIBlockElement = new CIBlockElement();
        $res = $CIBlockElement->GetList(
            Array(),
            array('ID' => array_keys($products)),
            false, Array(), $arSelect);

        while($element = $res->Fetch()){
            foreach($arSelect as $key => $val){
                if(!empty($element[$val.'_VALUE'])){ // PROPERTY_8_VALUE
                    $products[$element['ID']]->$key = $element[$val.'_VALUE'];
                }
            }
        }
        //var_dump($arConfig);
        //var_dump($products);

		$result = dDeliveryProduct::merge($products);
        return $result;
    }

    /* Калькуляция стоимости доставки*/
    static function Calculate($profile, $arConfig, $arOrder = false, $STEP= false, $TEMP = false)
    {
        if($_REQUEST['DELIVERY_ID'] != "DigitalDelivery:all"){
            return array("RESULT" => "ERROR");
        }
        //$res = self::Calc($arConfig);

        if($STEP == 1){
            return array(
                "RESULT" => "NEXT_STEP",
                //"VALUE" => 1
            );
        }
        if(!empty($_SESSION['ddelivery']) && !empty($_SESSION['ddelivery']['price'])){
            return array(
                "RESULT" => "OK",
                "VALUE" => $_SESSION['ddelivery']['price']
            );
        }

        return array(
            "RESULT" => "ERROR",
            "ERROR" => GetMessage('DIGITAL_DELIVERY_EMPTY_POINT')
        );
    }

    /* Проверка соответствия профиля доставки заказу */
    function Compability($arOrder, $arConfig)
    {
        return array("all");
    }

    public static function getOptions()
    {
        $options = COption::GetOptionString('delivery', 'ddelivery');
        if(!$options)
            return false;
        $options = unserialize($options);
        return $options;
    }

    public static function getPrice($point, $save=false)
    {
        $options = COption::GetOptionString('delivery', 'ddelivery');
        $options = unserialize($options);
        $products = array();
        /**
         * @var dDeliveryProduct $object
         */
        $object = self::Calc($options, $products);
        $descr = array();
        foreach($products as $product){
            $descr[] = $product;
        }

        $data = array('width' => $object->X, 'height' => $object->Z, 'length'=>$object->Y, 'weight'=>$object->W,
            'point'=>$point, 'description' => $object->description
        );
        if($save)
            $_SESSION['DIGITAL_DELIVERY']['DATA'] = $data;

        $dDeliveryLib = new dDeliveryLib($options['API_KEY']);
        $data = $dDeliveryLib->apiPrice($object, $point);

        if(!$data && !$data['success']){
            return false;
        }
        $dataSource = $data;
        $i=1;
        while($i <= 3){
            if(($options['PRICE_IF_'.$i] == '>' && $data['response']['delivery_price'] > $options['PRICE_SUM_'.$i])
                || ($options['PRICE_IF_'.$i] == '<' && $data['response']['delivery_price'] < $options['PRICE_SUM_'.$i]) )
            {
                if($options['PRICE_TYPE_'.$i] == 'A') {
                    $data['response']['delivery_price'] = 0;
                }elseif($options['PRICE_TYPE_'.$i] == 'F') {
                    if($options['PRICE_VALUE_'.$i] > 100){
                        $options['PRICE_VALUE_'.$i] = 100;
                    }
                    $data['response']['delivery_price'] = round($data['response']['delivery_price'] * (1 - ($options['PRICE_VALUE_'.$i] / 100)));
                }elseif($options['PRICE_TYPE_'.$i] == 'M') {
                    if($options['PRICE_VALUE_'.$i] > $data['response']['delivery_price']){
                        $data['response']['delivery_price'] = 0;
                    }else{
                        $data['response']['delivery_price'] = $data['response']['delivery_price'] - $options['PRICE_VALUE_'.$i];
                    }
                }elseif($options['PRICE_TYPE_'.$i] != 'AC'){
                    // фигня, а не правило, пропускаем
                    continue;
                }
                break;
            }
            $i++;
        }

        $userData = array(
            'products' => $products,
            'package' => $object,
            'response' => $data,
            'responseSource' => $dataSource,
        );

        foreach(GetModuleEvents("digital.delivery", "getPrice", true)  as $arEvent){
            ExecuteModuleEventEx($arEvent, array($$userData));
        }

        return $userData['response'];
    }

    function OnOrderNewSendEmail($iOrderID, $eventName, $arFieldsUpdate){
        $cso = new CSaleOrder();
        $arFields = $cso->GetByID($iOrderID);
        if($arFields["DELIVERY_ID"]=="DigitalDelivery:all" && !empty($_SESSION['DIGITAL_DELIVERY']['DATA']))
        {
            $options = self::getOptions();

            $dataTmp = $_SESSION['DIGITAL_DELIVERY']['DATA'];

            // @TODO переделать из костыля
            $paymentPrice = '';
            if($arFields['PAY_SYSTEM_ID'] == 1){ // оплата у нас
                $paymentPrice = $arFields['PRICE'];
            }else{ // оплата где-то там.
            }

            $db_vals = CSaleOrderPropsValue::GetList(
                array("SORT" => "ASC"),
                array(
                    "ORDER_ID" => $iOrderID,
                    "CODE" => array($options['PROP_FIO'], $options['PROP_PHONE'])
                )
            );
            $name = '';
            $phone = '';
            while($prop = $db_vals->Fetch()){
                if($prop['CODE'] == $options['PROP_FIO']){
                    $name = $prop['VALUE'];
                }elseif($prop['CODE'] == $options['PROP_PHONE']){
                    $phone = $prop['VALUE'];
                }
            }

            $dDeliveryLib = new dDeliveryLib($options['API_KEY']);
            $dDeliveryLib->apiOrderCreate(
                $dataTmp['width'], $dataTmp['height'], $dataTmp['length'], $dataTmp['weight'],
                $dataTmp['point'], round(($arFields['PRICE']/100) * $options['ASSESSED_VALUE']),
                $paymentPrice, $name, $phone, 'OrderId: '.$iOrderID."\n"
            );


        }
        unset($_SESSION["DIGITAL_DELIVERY"]['DATA']);
    }
}
?>