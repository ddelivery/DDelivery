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

/**
 * Class DShopAdapter
 * @package DDelivery\Adapter
 */
abstract class DShopAdapter
{
    /**
     * Возвращает товары находящиеся в корзине пользователя
     * @return array
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
     * @param DDeliveryOrder $order
     * @param DDeliveryPointSelf[] $ddeliveryPointSelfList
     *
     * @return \DDelivery\Point\DDeliveryPointSelf[]
     */
    public function preDisplayPoint( DDeliveryOrder $order, $ddeliveryPointSelfList) {
        return $ddeliveryPointSelfList;
    }

    /**
     * Срабатывает когда выбрана точка доставки
     *
     * @param DDeliveryAbstractPoint $point
     */
    public function onChangePoint( DDeliveryAbstractPoint $point) {}

    /**
     * Если необходимо фильтрует курьеров и добавляет новых
     *
     * @param DDeliveryPointCourier[] $courierPoints
     * @return \DDelivery\Point\DDeliveryPointCurier[]
     */
    public function filterPointsCourier($courierPoints) {
        return $courierPoints;
    }

    /**
     * Если необходимо фильтрует пункты самовывоза и добавляет новых
     *
     * @param DDeliveryPointSelf[] $courierPoints
     * @return \DDelivery\Point\DDeliveryPointSelf[]
     */
    public function filterPointsSelf($courierPoints) {
        return $courierPoints;
    }

    /**
     * Верните id города в системе DDelivery
     * @return int
     */
    public function getClientCityId() {
        return 0;
    }
}