<?php
/**
 * Класс для работы с ddelivery
 * http://ddelivery.ru/
 */
class dDeliveryLib
{
    const URL = 'cabinet.ddelivery.ru';

    protected $apiKey;
    function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public static function getCompanySize()
    {
        return array(
            6 => array('w'=>75, 'x'=>40, 'y'=> 50, 'z'=>60), // СДЭК
            4  => array('w'=>15, 'x'=>50, 'y'=> 80, 'z'=>100), // Boxberry
            11 => array('w'=>5, 's'=>150), // "Hermes",
            2 => array('w'=>25, 's'=>150),  // "IM Logistics",
            16 => array('w'=>25, 's'=>150),  // GetMessage("DIGITAL_DELIVERY_CONFIG_GROUPS_COMPANY_IM_LOG2"),
            17 => array('w'=>25, 's'=>150),  // GetMessage("DIGITAL_DELIVERY_CONFIG_GROUPS_COMPANY_IM_LOG3"),
            3 => array('w'=>15, 'x'=>33, 'y'=> 35, 'z'=>58), //"Logibox",
            14 => array('w'=>5, 's'=>80),// "Maxima Express",
            1 => array('w'=>10, 'x'=>15, 'y'=> 36, 'z'=>60), //"PickPoint"
            7 => array('w'=>30, 'x'=>38, 'y'=> 41, 'z'=>64), //"QIWI"
        );
    }

    public function apiPrice(dDeliveryProduct $object, $pointId)
    {
        $data = array('width' => $object->X, 'height' => $object->Z, 'length'=>$object->Y, 'weight'=>$object->W, 'point'=>$pointId);
        $url = 'http://'.self::URL.'/api/v1/'.$this->apiKey.'/delivery_price.json?'.http_build_query($data);
        $context  = stream_context_create(array('http' =>array('timeout' => 5)));
		
        $result = file_get_contents($url, false, $context);
        if(!$result){
            return false;
        }
        $data = json_decode($result, true);
        return $data;
    }

    public function apiOrderCreate($width, $height, $length, $weight, $pointId, $declaredPrice, $paymentPrice, $name, $phone, $description)
    {

        $data = array(
            'dimension_side1' => $width,
            'dimension_side2' => $height,
            'dimension_side3' => $length,
            'weight' => $weight,
            'delivery_point' => $pointId,
            'goods_description' => $description,
            'declared_price' => $declaredPrice,
            'payment_price'=>$paymentPrice,
            'name' => $name,
            'phone' => $phone,
        );

        $url = 'http://'.self::URL.'/api/v1/'.$this->apiKey.'/order_create.json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        $res = curl_exec($curl);

        curl_close($curl);
        return $res;
    }

}



class dDeliveryProduct
{
    public $X = 0, $Y=0, $Z=0, $W=0, $count=1, $description="";
    function __construct($arConfig=null, $count = 1, $description = "")
    {
        $this->count = $count;
        if($arConfig!=null){
            if(is_array($arConfig['DEFAULT_X'])){
                $this->X = ceil($arConfig['DEFAULT_X']['VALUE']);
                $this->Y = ceil($arConfig['DEFAULT_Y']['VALUE']);
                $this->Z = ceil($arConfig['DEFAULT_Z']['VALUE']);
                $this->W = ceil($arConfig['DEFAULT_W']['VALUE']);
            }else{
                $this->X = ceil($arConfig['DEFAULT_X']);
                $this->Y = ceil($arConfig['DEFAULT_Y']);
                $this->Z = ceil($arConfig['DEFAULT_Z']);
                $this->W = ceil($arConfig['DEFAULT_W']);
            }
        }
    }

    // задача укладывания рюкзака
    /**
     * @param $products
     * @return dDeliveryProduct
     */
    static function merge($products){
        if(count($products) < 2){
            return reset($products);
        }
        //var_dump($products);
        /*
            1. Находим у каждого товара минимальную сторону из трех.
            2. Суммируем минимальные стороны этих товаров
            3. Дальше мы находим товар с максимальной стороной
            4. Находим сторону следующая за максимальной в порядке уменьшения.

            Итоговым размером является:

            Сторона 1: Сумма минимальных сторон каждого товара
            Сторона 2: Максимальная сторона товара
            Сторона 3: Сторона товара следующая за максимальнйо в порядке убывания.
         */

        $res = new dDeliveryProduct();

        $minSum = 0;
        $max = 0;
        $maxSecond = 0;

        $desciptions = array();

        /**
         * @var dDeliveryProduct $product
         */
        foreach($products as $product){
            for($i=0; $i<$product->count; $i++){
                $data = array($product->X, $product->Y, $product->Z);
                $minSum += min($data);
                $curMax = max($data);
                if($max < $curMax){
                    $maxSecond = $max;
                    $max = $curMax;
                    sort($data);
                    $curMax = $data[1];
                }elseif($max == $curMax){
                    sort($data);
                    $curMax = $data[1];
                }
                if( $maxSecond < $curMax ){
                    $maxSecond = $curMax;
                }
            }
            $res->W += $product->W * $product->count;
            $desciptions[] = $product->description;
        }
        $res->description = implode("\n", $desciptions);
        $res->X = (float)$minSum;
        $res->Y = (float)$max;
        $res->Z = (float)$maxSecond;

        return $res;
    }

    public function set($x, $y,$z) {
        $this->X = $x;
        $this->Y = $y;
        $this->Z = $z;
    }
}