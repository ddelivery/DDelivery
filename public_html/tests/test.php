<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 23:05
 */


header('Content-type: text/html; charset=utf-8');
ini_set("display_errors", "1");
error_reporting(E_ALL);
require_once '../application/bootstrap.php';
require_once '../example/IntegratorShop.php';

$shopAdapter = new IntegratorShop();
$ddeliveryUI = new \DDelivery\DDeliveryUI($shopAdapter);

/*
$order = $ddeliveryUI->getOrder();
$order->city = 151184;
$points = $ddeliveryUI->calculateSelfPrices($order);
echo '<pre>';
//print_r($points);
echo '</pre>';
//echo '<pre>';
//print_r( $points[0] );
//echo '</pre>';

$p = $ddeliveryUI->getSelfPointsList( $order, array( $points[1] ));
echo '<pre>';
print_r( $p );
echo '</pre>';
*/
$order = $ddeliveryUI->initOrder(766);
$order->paymentVariant = 12;
$order->shopRefnum = 12;
$order->localStatus = 10;
echo '<pre>';
print_r($order);
echo '</pre>';
echo $ddeliveryUI->sendOrderToDD($order);
/*
echo $ddeliveryUI->sendOrderToDD($order);

echo '<pre>';
print_r($order->getPoint());
echo '</pre>';
*/