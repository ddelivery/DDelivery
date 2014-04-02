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

$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );

//$order = $DDeliveryUI->getCityByIp('188.162.64.72');
//echo $order['city_id'] ;
// point id = 50
//city_id = 151185

//$points = $DDeliveryUI->getSelfPointsForCity( $order['city_id'] );
//print_r($points);

$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
$selfpoints = $DDeliveryUI->getSelfPoints( 151185 );

$DDeliveryUI->setOrderPoint($selfpoints[0]);
$DDeliveryUI->createSelfOrder();


$selfpoints = $DDeliveryUI->getSelfPoints( $order['city_id'] );
print_r($selfpoints);
//print_r($selfpoints);
//$order = $DDeliveryUI->getOrder()->getOrderInfo();
/*
$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);



*/

