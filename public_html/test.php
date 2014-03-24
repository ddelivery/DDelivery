<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 23:05
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);


require_once 'application/bootstrap.php';
//$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
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

$DDeliveryUI = new DDelivery\DDeliveryUI();
$result = $DDeliveryUI->getSelfPointsForCity( '4,6' );

//print_r($result);
