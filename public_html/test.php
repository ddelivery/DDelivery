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
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
$selfpoints = $DDeliveryUI->getSelfPoints( 151185 );

$DDeliveryUI->setOrderPoint($selfpoints[0]);
$DDeliveryUI->createSelfOrder();

//$selfpoints = $DDeliveryUI->getSelfPoints( $order['city_id'] );
//print_r($selfpoints);
//print_r($selfpoints);
//$order = $DDeliveryUI->getOrder()->getOrderInfo();
/*
$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);

$response = $DDeliverySDK->addSelfOrder(50, 10, 10, 10, true, 1, 'ozy', 
                                        '9999999999', 'ozydescription', 1000, 1000);
print_r($response);
*/
/*
$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);

$response = $DDeliverySDK->addSelfOrder(50, 10, 10, 10, true, 1, 'ozy',
		'9999999999', 'ozydescription', 1000, 1000);


$response = $DDeliverySDK->addCourierOrder(151185, 17, 
                                           10, 10, 10, 
                                           'xxx', true, 1, 'Пяточкин Петр Петрович', '9999999999', 
                                           'Трос 1шт, Пробка от бутылки 2шт.', 1000, 
                                           1000, 'Вознесенская', '1а', '42', 'ozy@retyy.er');

print_r($response);
*/

/*
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );

$response = $DDeliveryUI->getCourierPointsForCity(151185);
print_r($response);
*/

