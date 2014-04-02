<?php
/**
 *
 * @package    DDelivery.Order
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */

namespace DDelivery\Order;


class DDeliveryCourierOrder extends DDeliveryOrder
{
    /**
     * @var
     */
    public $deliveryCompany;
    /**
     * @var
     */
    public $shopRefnum;
    /**
     * @var
     */
    public $toStreet;
    /**
     * @var
     */
    public $toHouse;
    /**
     * @var
     */
    public $toFlat;
    /**
     * @var
     */
    public $toEmail;

}