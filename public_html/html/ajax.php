<?php
/**
 * User: DnAp
 * Date: 19.03.14
 * Time: 23:51
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);

$_SERVER['REMOTE_ADDR'] = '213.180.193.3';

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
        $products[] = new DDeliveryProduct(1, 20, 23, 25, 10, 1000, 1);
        $products[] = new DDeliveryProduct(2, 10, 13, 15, 4, 1500, 2);
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
        return \DDelivery\Sdk\DDeliverySDK::TYPE_SELF;
    }

    public function filterPointsSelf($courierPoints, $order)
    {
        new \DDelivery\Point\DDeliveryPointSelf();
        return parent::filterPointsSelf($courierPoints, $order);
    }


}

$shopAdapter = new ShopAdapter();
$ddeliveryUI = new DDeliveryUI($shopAdapter);
$ddeliveryUI->render(isset($_POST) ? $_POST : array());



