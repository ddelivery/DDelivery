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


class DDeliveryCurierOrder extends DDeliveryOrder
{	
	
	private $allowParams = array('id', 'type', 'delivery_point',
	                             'dimension_side1', 'dimension_side2',
	                             'dimension_side3', 'weight', 'declared_price',
	                             'payment_price', 'to_name', 'to_phone', 
	                             'goods_description');
	
}