<?php
/**
 * User: DnAp
 * Date: 19.03.14
 * Time: 23:51
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);

$_SERVER['REMOTE_ADDR'] = '88.201.177.120';

include_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'application', 'bootstrap.php')));

use DDelivery\Adapter\DShopAdapter;
use DDelivery\DDeliveryUI;
use DDelivery\Order\DDeliveryProduct;

class ShopAdapter extends DShopAdapter
{
    /**
     * Возвращает товары находящиеся в корзине пользователя
     * @return \DDelivery\Order\DDeliveryProduct[]
     */
    public function getProductsFromCart()
    {
        $products = array();
        $products[] = new DDeliveryProduct(1, 20, 13, 25, 0.5, 1000, 1, 'Веселый клоун');
        $products[] = new DDeliveryProduct(2, 10, 13, 15, 0.3, 1500, 2, 'Грустный клоун');
        return $products;
    }

    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
    public function getApiKey()
    {
        return '4bf43a2cd2be3538bf4e35ad8191365d';
    }

    public function getSupportedType()
    {
        return parent::getSupportedType();
        //return \DDelivery\Sdk\DDeliverySDK::TYPE_SELF;
    }

    public function filterPointsSelf($courierPoints, \DDelivery\Order\DDeliveryOrder $order)
    {
        return parent::filterPointsSelf($courierPoints, $order);
    }


    public function getPathByDB()
    {
        return __DIR__.'/db.sqlite';
    }

    /**
     * Должен вернуть url до каталога с статикой
     * @return string
     */
    public function getStaticPath()
    {
        return '/html/';
    }

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public function getPhpScriptURL()
    {
        return '/html/ajax.php';
    }

    /**
     * Возвращает стоимоть заказа
     * @return float
     */
    public function getAmount()
    {
        // TODO: Implement getAmount() method.
    }
}

$shopAdapter = new ShopAdapter();


$ddeliveryUI = new DDeliveryUI($shopAdapter);
// В зависимости от параметров может выводить полноценный html или json
$ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());



