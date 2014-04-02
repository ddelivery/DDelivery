<?php
/**
 *
 * @package    DDelivery.Adapter
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */

namespace DDelivery\Adapter;

use DDelivery\Order\DDeliveryOrder;
use DDelivery\Order\DDeliveryProduct;
use DDelivery\Point\DDeliveryAbstractPoint;
use DDelivery\Point\DDeliveryPointCourier;
use DDelivery\Point\DDeliveryPointSelf;
use DDelivery\Sdk\DDeliverySDK;

/**
 * Class DShopAdapter
 * @package DDelivery\Adapter
 */
abstract class DShopAdapter
{
    /**
     * Возвращает товары находящиеся в корзине пользователя
     * @return DDeliveryProduct[]
     */
    public abstract function getProductsFromCart();

    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
    public abstract function getApiKey();

    /**
     * Если вы знаете имя покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientName() {
        return null;
    }

    /**
     * Если вы знаете телефон покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientPhone() {
        return null;
    }

    /**
     * Вызывается перед отображением цены точки самовывоза, можно что-то изменить
     *
     * @param DDeliveryOrder $order
     * @param DDeliveryPointSelf $ddeliveryPointSelf
     *
     * @return \DDelivery\Point\DDeliveryPointSelf
     */
    public function preDisplayPoint( DDeliveryOrder $order, $ddeliveryPointSelf) {
        return $ddeliveryPointSelf;
    }

    /**
     * Срабатывает когда выбрана точка доставки
     *
     * @param DDeliveryAbstractPoint $point

    public function onChangePoint( DDeliveryAbstractPoint $point) {}
     */

    /**
     * Если необходимо фильтрует курьеров и добавляет новых
     *
     * @param DDeliveryPointCourier[] $courierPoints
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @return \DDelivery\Point\DDeliveryPointCourier[]
     */
    public function filterPointsCourier($courierPoints, DDeliveryOrder $order) {
        return $courierPoints;
    }

    /**
     * Если необходимо фильтрует пункты самовывоза и добавляет новых
     *
     * @param DDeliveryPointSelf[] $courierPoints
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @return \DDelivery\Point\DDeliveryPointSelf[]
     */
    public function filterPointsSelf($courierPoints, DDeliveryOrder $order) {
        return $courierPoints;
    }

    /**
     * Верните id города в системе DDelivery
     * @return int
     */
    public function getClientCityId() {
        return 0;
    }

    /**
     * Возвращает оценочную цену для товаров в послыке
     * @param DDeliveryProduct[] $productList
     * @return int
     */
    public function getDeclaredPrice($productList) {
        $declaredPrice = 0;
        foreach($productList as $product) {
            $declaredPrice = $product->getPrice();
        }

        return $declaredPrice;
    }

    /**
     * Возвращает поддерживаемые магазином способы доставки
     * @return array
     */
    public function getSupportedType()
    {
        return array(DDeliverySDK::TYPE_COURIER, DDeliverySDK::TYPE_SELF);
    }
}