<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 23:05
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);


require_once 'application/bootstrap.php';
//$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('	', true);
//$result = $DDeliverySDK->getSelfDeliveryPoints('4,6', '4,25');
//$result = $DDeliverySDK->deliveryPoints();
// $result = $DDeliverySDK->getCityByIp('188.162.64.72');
//$result = $DDeliverySDK->getSelfDeliveryPoints('4,6');
//$result = $DDeliverySDK->calculatorPickup( 1, 10, 10, 10, 1 , 0);
/*
	$order = new DDelivery\Order\DDeliverySelfOrder();
	$order->set('type');
	$result = $DDeliverySDK->sendSelfOrder($order);
*/

//$DDeliveryUI = new DDelivery\DDeliveryUI();
//$result = $DDeliverySDK->getSelfPointsForCity( '4,6' );

//print_r($result);
/*
 * */

//$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
//$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );

//$order = $DDeliveryUI->getCityByIp('188.162.64.72');
//echo $order['city_id'] ;
// point id = 50
//city_id = 151185

//$points = $DDeliveryUI->getSelfPointsForCity( $order['city_id'] );
//print_r($points);
/*
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
$selfpoints = $DDeliveryUI->getSelfPoints( 151185 );

$DDeliveryUI->setOrderPoint($selfpoints[0]);
$DDeliveryUI->createSelfOrder();


$selfpoints = $DDeliveryUI->getSelfPoints( $order['city_id'] );
print_r($selfpoints);
*/
//print_r($selfpoints);
//$order = $DDeliveryUI->getOrder()->getOrderInfo();
/*
$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);



*/
//$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
//$calc = $DDeliverySDK->calculatorCourier(151185, 10, 10, 10, 1, 0);
//$order = $DDeliverySDK->calculatorCourier( 151185, 10, 10, 10, 1, 0 );
//print_r(  $order  );
$productList[] = new \DDelivery\Order\DDeliveryProduct( 1, 2, 6, 2,
		1, 100, 2, 'Пиджак' );
$productList[] = new \DDelivery\Order\DDeliveryProduct( 2, 3, 1,
		1, 1, 200, 1,'Куртка кожанная') ;

$fixture = new DDelivery\Order\DDeliveryOrder($productList);
$fixture->getProductParams();
print_r($fixture);

/*
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );

$order = $DDeliveryUI->getOrder();
$selfpoint = $DDeliveryUI->getCourierPointsForCity(151185);

$order->setPoint($selfpoint[0]);
$order->toName = 'Дима Грушин';
$order->toPhone = '9999999999';
$order->shopRefnum = 'xxx';
$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';

$order_id = $DDeliveryUI->createCourierOrder();
echo $order_id;
*/

