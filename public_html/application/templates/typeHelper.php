<?
/**
 * @var string $staticPath
 * @var DDelivery\DDeliveryUI $this
 * @var array $headerData
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
                    <span class="price"><span><?=$headerData['courier']['minPrice']?></span> <i class="icon-rub">&nbsp;</i></span>
                    <span class="date">от <strong><?=$headerData['courier']['minTime']?></strong>
                        <?\DDelivery\Utils::plural($headerData['courier']['minTime'], 'дня', 'дней', 'дней', 'дней', false)?>
                    </span>
                </a>
            </li>
        <?endif;?>
        <?if(in_array(DDelivery\Sdk\DDeliverySDK::TYPE_SELF, $this->supportedTypes)):?>
            <li class="delivery-type__drop_self">
                <a href="javascript:void(0)">
                    <span class="name">забрать самовывозом от</span>
                    <span class="price"><span><?=$headerData['self']['minPrice']?></span> <i class="icon-rub">&nbsp;</i></span>
                    <span class="date">от <strong><?=$headerData['self']['minTime']?></strong>
                        <?\DDelivery\Utils::plural($headerData['self']['minTime'], 'дня', 'дней', 'дней', 'дней', false)?>
                    </span>
                </a>
            </li>
        <?endif;?>
    </ul>
</div>