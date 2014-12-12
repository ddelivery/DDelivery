<div class="widget-wrap theme-violet">
    <div class="widget-wrap__title">
        <p class="title-text">Доставка данного товара до вашего <br/>населенного пункта:</p>
    </div>
    <div class="widget-delivery-type">
        <?if($self != null):?>
            <?php $self = $self[0];  ?>
            <a href="javascript:void(0);" class="type__item">
                <div class="type__item-title">Пункт выдачи:</div>
                <div class="typ__item_i clearfix">
                    <img src="<?=$url?>img/logo/<?=$companies[$self['delivery_company']]['ico']?>.png" class="type__item-img" alt="boxberry"/>
                    <div class="type__item-text">
                        <p>от <strong><?=$this->getClientPrice($self, $order)?></strong><span class="icon-rub"></span></p>
                        <p>от <strong><?=$self['delivery_time_min']?></strong> <?=\DDelivery\Utils::plural($self['delivery_time_min'], 'день', 'дня', 'дней', 'дней', false);?></p>
                    </div>
                </div>
            </a>
        <?endif;?>
        <?if($courier != null):?>
            <a href="javascript:void(0);" class="type__item">
                <div class="type__item-title">Курьерская доставка:</div>
                <div class="type__item_i clearfix">
                    <img src="<?=$url?>img/logo/<?=$companies[$courier['delivery_company']]['ico']?>.png" class="type__item-img" alt="dpd"/>
                    <div class="type__item-text">
                        <p>от <strong><?=$this->getClientPrice($courier, $order)?></strong><span class="icon-rub"></span></p>
                        <p>от <strong><?=$courier['delivery_time_min']?></strong> <?=\DDelivery\Utils::plural($courier['delivery_time_min'], 'день', 'дня', 'дней', 'дней', false);?></p>
                    </div>
                </div>
            </a>
        <?endif;?>
        <?if($post != null):?>
            <a href="javascript:void(0);" class="type__item">
                <div class="type__item-title">Почта:</div>
                <div class="type__item_i clearfix">
                    <img src="<?=$url?>img/logo/<?=$companies[$post['delivery_company']]['ico']?>.png" class="type__item-img" alt="dpd"/>
                    <div class="type__item-text">
                        <p>от <strong><?=$this->getClientPrice($post, $order)?></strong><span class="icon-rub"></span></p>
                        <p>от <strong><?=$post['delivery_time_min']?></strong> <?=\DDelivery\Utils::plural($post['delivery_time_min'], 'день', 'дня', 'дней', 'дней', false);?></p>
                    </div>
                </div>
            </a>
        <?endif;?>
    </div>
</div>

