<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 15.05.14
 * Time: 23:27
 *
 * debuger.php - список заказов в sqlite
 * debuger.php?task=unfinished - список незавершенных заказов
 * debuger.php?task=createpull - создать пулл заказов
 * debuger.php?task=statuspull - получить пул статусов
 * debuger.php?task=products - получить дамп текущего заказа( при наличии товара в корзине )
 *
 */
header('Content-Type: text/html; charset=utf-8');
include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));

// Добавьте адаптер и необходимые файлы для работы CMS
include_once("IntegratorShop.php");

//$task = $_GET['task'];

$shopAdapter = new IntegratorShop();
$DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
$order = $DDeliveryUI->getOrder();
$order->city = 151184;
$order->type = 1;
$order->firstName = 'Васян';
$order->secondName = 'xxxx';
$order->toPhone = '3322322323';
$order->paymentVariant = 2;
$order->localStatus = 20;
$order->shopRefnum = 12;
$selfCompanyList = $DDeliveryUI->cachedCalculateSelfPrices( $order   );

$order->setPoint($selfCompanyList[0]);
$order->pointID = 2716;
//print_r($selfCompanyList[0]);
echo $DDeliveryUI->sendOrderToDD($order);
//print_r( $order->getJsonOrder() );
//print_r($selfCompanyList[0]);
/*
$order->city = 151184;
$selfCompanyList = $DDeliveryUI->cachedCalculateSelfPrices( $order   );
print_r($selfCompanyList);
$pointsJs = $DDeliveryUI->getSelfPointsList( $order, $selfCompanyList );
print_r($pointsJs);
*/
//echo 'ozk';
/*
$order = $DDeliveryUI->getOrder();
$order->setCacheValue('calculateCourier', 'xxx', '22');
echo $order->getCacheValue('calculateCourier', 'xxx');
*/
/*
$order->city = 151184;
echo '<pre>';
$prices = $DDeliveryUI->calculateSelfPrices( $order );
print_r( $DDeliveryUI->getSelfPointsList( $order, $prices ) );
//print_r( $DDeliveryUI->getSelfDeliveryInfoForCity( $order ) );
echo '</pre>';
exit();
*/
/*
echo '<pre>';
print_r( $DDeliveryUI->getCourierPointsForCity($order) );
echo '</pre>';
*/
$task = 'ozk';
if(function_exists($task))
{
    //$task( $DDeliveryUI );
}
else
{
    //dumpOrders( $DDeliveryUI );
}

function products( $DDeliveryUI )
{

   // print_r($DDeliveryUI->getOrder());
}

function dumpOrders( $DDeliveryUI )
{

    $orders = $DDeliveryUI->getAllOrders();
    if(count($orders))
    {
        foreach($orders as $item)
        {
            print_r($item);
            echo '<hr />';
        }
    }
}

function unfinished( $DDeliveryUI )
{
    $orders = $DDeliveryUI->getUnfinishedOrders();
    if(count($orders))
    {
        foreach($orders as $item)
        {
            print_r($item);
            echo '<hr />';
        }
    }
}

function createpull( $DDeliveryUI )
{

    $pull = $DDeliveryUI->createPullOrders();
    if(count($pull))
    {
        foreach($pull as $item)
        {
            print_r($item);
            echo '<hr />';
        }
    }
}

function statuspull($DDeliveryUI)
{

    $pull = $DDeliveryUI->getPullOrdersStatus();
    if(count($pull))
    {
        foreach($pull as $item)
        {
            print_r($item);
            echo '<hr />';
        }
    }
}