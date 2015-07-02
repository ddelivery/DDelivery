<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 5/6/15
 * Time: 4:01 PM
 */
use DDelivery\DDeliveryUI;

include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));
include_once("IntegratorShop.php");

try{

    $IntegratorShop = new IntegratorShop();
    $ddeliveryUI = new DDeliveryUI($IntegratorShop);
    // В зависимости от параметров может выводить полноценный html или json
    $ddeliveryUI->onCmsOrderFinish('orderId извсплывающего окна', 'orderId извсплывающего окна', 12, 12);
    $ddeliveryUI->onCmsChangeStatus('orderId извсплывающего окна', 12);
}catch ( \DDelivery\DDeliveryException $e ){
    echo $e->getMessage();

    $IntegratorShop->logMessage($e);
}