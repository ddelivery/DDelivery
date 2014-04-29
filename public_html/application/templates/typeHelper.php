<?
/**
 * @var string $staticPath
 * @var DDelivery\DDeliveryUI $this
 */
?>
<div class="delivery-type__title courier">
    <img src="<?=$staticURL?>img/icons/icon-courier.png"/>забрать курьером <span class="arrow"><i>&nbsp;</i></span>
</div>
<div class="delivery-type__title self">
    <img src="<?=$staticURL?>img/icons/shipping-grey.png"/>забрать самовывозом <i>&nbsp;</i></span>
</div>
<div class="delivery-type__drop">
    <ul>
        <?if(in_array(DDelivery\Sdk\DDeliverySDK::TYPE_COURIER, $this->supportedTypes)):?>
            <li class="delivery-type__drop_courier">
                <a href="javascript:void(0)">
                    <span class="name">доаставить курьером</span>
                    <span class="price"><span>100</span> <i class="icon-rub">&nbsp;</i></span>
                    <span class="date">от <strong>1</strong> дня</span>
                </a>
            </li>
        <?endif;?>
        <?if(in_array(DDelivery\Sdk\DDeliverySDK::TYPE_SELF, $this->supportedTypes)):?>
            <li class="delivery-type__drop_self">
                <a href="javascript:void(0)">
                    <span class="name">забрать самовывозом от</span>
                    <span class="price"><span>100</span> <i class="icon-rub">&nbsp;</i></span>
                    <span class="date">от <strong>1</strong> <span>дня</span></span>
                </a>
            </li>
        <?endif;?>
    </ul>
</div>