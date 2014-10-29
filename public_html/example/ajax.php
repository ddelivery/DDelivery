<?php


ini_set("display_errors", "1");
error_reporting(E_ALL);

include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));
include_once("IntegratorShop.php");

use DDelivery\DDeliveryUI;
// xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
try{
    echo 'xxxx';

    $IntegratorShop = new IntegratorShop();
    $ddeliveryUI = new DDeliveryUI($IntegratorShop);
    // В зависимости от параметров может выводить полноценный html или json
    $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());
}catch ( \DDelivery\DDeliveryException $e ){
    echo $e->getMessage();
    $IntegratorShop->logMessage($e);
}
/*
$xhprof_data = xhprof_disable();
include_once "/var/www/html/xhprof-0.9.4/xhprof_lib/utils/xhprof_lib.php";
include_once "/var/www/html/xhprof-0.9.4/xhprof_lib/utils/xhprof_runs.php";
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($xhprof_data, "test");
*/

