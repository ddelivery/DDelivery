<?php
/**
*
* @package    DDelivery
*
* @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
*
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*
* @author  mrozk <mrozk2012@gmail.com>
*/
namespace DDelivery;
use DDelivery\Adapter\DShopAdapterImpl;

class DDeliveryUI
{
    private $sdk;

    private $shop;
    
    private $order;
    
    public function __construct()
    {
        $this->sdk = new Sdk\DDeliverySDK();
        
        $this->shop = new Adapter\DShopAdapterImpl();

    }
    
    
}