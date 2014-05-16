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

include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));

// Добавьте адаптер и необходимые файлы для работы CMS
include_once("IntegratorShop.php");

$task = $_GET['task'];

if(function_exists($task))
{
    $task();
}
else
{
    dumpOrders();
}

function products()
{
    $shopAdapter = new IntegratorShop();
    $DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter );
    print_r($DDeliveryUI->getOrder());
}

function dumpOrders()
{
    $shopAdapter = new IntegratorShop();
    $DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter, true );
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

function unfinished()
{
    $shopAdapter = new IntegratorShop();
    $DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter, true );
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

function createpull()
{
    $shopAdapter = new IntegratorShop();
    $DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter, true );
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

function statuspull()
{
    $shopAdapter = new IntegratorShop();
    $DDeliveryUI = new DDelivery\DDeliveryUI( $shopAdapter, true);
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