<?php
/**
 * User: DnAp
 * Date: 18.03.14
 * Time: 23:05
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);


require_once 'application/bootstrap.php';
$DDeliverySDK = new DDelivery \ DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
//$result = $DDeliverySDK->calculatorPickup(1,2,3,4,5,6,7);
$result = $DDeliverySDK->deliveryPoints();
var_dump($result);