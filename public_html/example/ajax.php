<?php


ini_set("display_errors", "1");
error_reporting(E_ALL);

include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));
include_once("IntegratorShop.php");

use DDelivery\DDeliveryUI;


$IntegratorShop = new IntegratorShop();

$ddeliveryUI = new DDeliveryUI($IntegratorShop);
// В зависимости от параметров может выводить полноценный html или json
$ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());


