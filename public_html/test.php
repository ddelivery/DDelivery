<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 23:05
 */


header('Content-type: text/html; charset=utf-8');
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
$selfpoints = $DDeliveryUI->getSelfPoints( 	 );

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
/*
$productList[] = new \DDelivery\Order\DDeliveryProduct( 1, 2, 6, 2,
		1, 100, 2, 'Пиджак' );
$productList[] = new \DDelivery\Order\DDeliveryProduct( 2, 3, 1,
		1, 1, 200, 1,'Куртка кожанная') ;

$fixture = new DDelivery\Order\DDeliveryOrder($productList);
$fixture->getProductParams();
print_r($fixture);
*/

/*
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );

$order = $DDeliveryUI->getOrder();
$selfpoint = $DDeliveryUI->getSelfPoints(151185);

$order->setPoint($selfpoint[0]);
$order->toName = 'Дима Грушин';
$order->toPhone = '9999999999';
$order->shopRefnum = 'xxx';
$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';

$order_id = $DDeliveryUI->createSelfOrder();
echo $order_id;


$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$fixture = new DDelivery\DDeliveryUI( $shopAdapter );
$result = $fixture->getSelfPointsForCityAndCompany('4,6', '4,25');
print_r($result);

$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
$selfpoint = $DDeliverySDK->getSelfDeliveryPoints('', '151184');
$funki = array();
foreach ($selfpoint->response as $res)
{
	unset($res['metro']);
	unset($res['description_in']);
	unset($res['description_out']);
	unset($res['indoor_place']);
	$funki[] = $res;
}

print_r( json_encode($funki));

*/
/*
$fixture = new \DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
$result = $fixture->addSelfOrder( 50, 10,
    10, 10,true, 1, 'Дима Грушин', '9999999999',
    'Товар 1, шт', 0, 0, 12);
print_r($result);
$result = $fixture->calculatorPickupForPoint(50, 10, 10,  10, 1, 0);
*/
include 'example/IntegratorShop.php';

$shopAdapter = new IntegratorShop();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
$order = $DDeliveryUI->getOrder();
$order->city = 151184;
$order->type = 1;
$order->firstName = 'Дима';
$order->secondName = 'Грушин';
$order->toPhone = '9999999999';
$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';
$order->localId = 2;
$order->paymentVariant = 'cash';
$order->localStatus = 'xxx';
$order->shopRefnum = 14;
$order->comment = 'Олег Царьов';
//$id = $DDeliveryUI->saveFullOrder( $order );
//echo $id;
print_r( $DDeliveryUI->getMinPriceAndPeriodCourier($order) );

/*
$order2 = $DDeliveryUI->formatPhone('+7(1       00)100-10-01');
echo $order2;
*/

///print_r( $order2 );
//print_r( $DDeliveryUI->getDeliveryPrice(18) );

//$pointself = $DDeliveryUI->getSelfPoints($order);
//print_r($pointself);
//$order->setPoint($pointself[0]);


//echo $DDeliveryUI->createSelfOrder( $order );
//$pointself = $DDeliveryUI->getSelfPoints($order);

//$courierpoints = $DDeliveryUI->getCourierPointsForCity($order);

//$pointself = $DDeliveryUI->getCourierPointByCompanyID( 17, $order ) ;
//print_r($pointself);
//$city = $DDeliveryUI->get

//print_r($pointself);
//print_r($courierpoints);
/*
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
$order = $DDeliveryUI->getOrder();
$order->city = 151185;

$pointself = $DDeliveryUI->getSelfPoints($order);
courierpoints = $DDeliveryUI->getCourierPoints($order);
*/
//echo $id;

/*
$selfPoints = $DDeliveryUI->getCourierPointsForCity( $order );

$minAndMax = $DDeliveryUI->getMinPriceAndPeriodCourier( $order );
$minAndMaxSelf = $DDeliveryUI->getMinPriceAndPeriodSelf( $order );

print_r($minAndMax);
print_r($minAndMaxSelf);
*/

/*
$order->city = 151184;
$order->localId = 1;
$order->type = 2;

//$order->setPoint($selfPoints[0]);
$order->firstName = 'Дима';
$order->secondName = 'Грушин';
$order->toPhone = '9999999999';

$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';
*/
//$DDeliveryUI->getDDStatusByLocal( $ddStatus = 20 );
//print_r( $DDeliveryUI->getDDOrderStatus(1007) );

//$id = $DDeliveryUI->saveFullOrder($order);
//echo $id;

//$DDeliveryUI->onCmsOrderFinish($order->localId, $shopOrderID = 5);

//$DDeliveryUI->changeOrderStatus( 5 );

//$data = $DDeliveryUI->getAllOrders();
///print_r($data);
//$DDeliveryUI->getOrderStatus();
//$data = $DDeliveryUI->getAllOrders();

//print_r($data);
//$DDeliveryUI->onCmsOrderFinish($order->localId, $shopOrderID = 5);
//$DDeliveryUI->checkOrderStatus(947);
//$DDeliveryUI->changeOrderStatus(5);
/*
print_r($DDeliveryUI->initIntermediateOrder(array(1,2,3,4,5,6)));
$DDeliveryUI->getSelfPoints(151185);
*/
/*
$id = $DDeliveryUI->saveFullOrder($order);
echo $id;
$data = $DDeliveryUI->getAllOrders();
print_r($data);
*/
//print_r($order);
/*
$selfpoint = $DDeliveryUI->getCourierPointsForCity(151185);

$order = $DDeliveryUI->getOrder();

$order->localId = 1;
$order->city = 151185;
$order->type = 2;
$order->setPoint($selfpoint[0]);
$order->toName = 'Дима Грушин';
$order->toPhone = '9999999999';
$order->shopRefnum = 'xxx';
$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';
*/
//print_r($order);
/*
$DDeliveryUI->saveFullOrder( $ddeliveryID = 5 );
$data = $DDeliveryUI->getAllOrders();
//print_r( $data );

$DDeliverySDK = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
print_r( $DDeliverySDK->getCityByIp('188.162.64.72') );
*/



/*
$shopAdapter = new DDelivery\Adapter\DShopAdapterImpl();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );

$selfpoint = $DDeliveryUI->getSelfPoints(151185);

$order = $DDeliveryUI->getOrder();


$order->city = 151185;
$order->type = 1;
$order->setPoint($selfpoint[0]);
$order->toName = 'Дима Грушин';
$order->toPhone = '9999999999';
$order->shopRefnum = 'xxx';
$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';

$id = $DDeliveryUI->saveIntermediateOrder(null);
$co = $DDeliveryUI->createSelfOrder(1);
$data = $DDeliveryUI->getAllOrders();
print_r($data);
*/

//$points = $DDeliveryUI->getMinPriceAndPeriodCourier(151185);
//print_r( $points);
//$price  = $DDeliveryUI->getMinPriceAndPeriodDelivery($points);
//	print_r( $price );
//$info = $DDeliveryUI->getSelfDeliveryInfoForCity(151185);
//var_dump($info);
/*
$selfpoint = $DDeliveryUI->getSelfPoints(151185);


$order->city = 151185;
$order->type = 1;
$order->setPoint($selfpoint[0]);
$order->toName = 'Дима Грушин';
$order->toPhone = '9999999999';
$order->shopRefnum = 'xxx';
$order->toStreet = 'Вознесенская';
$order->toHouse = '1а';
$order->toFlat = '42';
$order->toEmail = '';



$id = $DDeliveryUI->saveIntermediateOrder(null);
echo $id;
$DDeliveryUI->initIntermediateOrder(1);
*/

//$selfpoints = $DDeliveryUI->getSelfPoints( 151185 );


//print_r($selfpoints);