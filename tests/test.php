<?php
header('Content-Type: text/html; charset=utf-8');
include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));
include_once("../example/IntegratorShop.php");

$shopAdapter = new IntegratorShop();
$DDeliveryUI = new \DDelivery\DDeliveryUI($shopAdapter);
echo '<pre>';
$order = $DDeliveryUI->initOrder( 854 );
$DDeliveryUI->onCmsOrderFinish(854, 2, 2, 3);
$order = $DDeliveryUI->initOrder( 854 );

echo $DDeliveryUI->paymentPriceEnable($order);
print_r( $DDeliveryUI->getAvailablePaymentVariants($order));
echo '</pre>';
