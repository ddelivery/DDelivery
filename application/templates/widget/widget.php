<div class="widget-drop__choose theme-white choose-city">
    <div class="dd_loader"  style="text-align: center; padding: 20px;display: none" >
        <img  align="center" style="" src="<?=$url?>widget/img/ajax_loader.gif" />
    </div>
    <div class="widget-drop__choose_i">

        <div class="widget-drop__close"></div>
        <div class="widget-delivery-type">
            <?if($self != null):?>
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
        <a href="javascript:void(0)" id="choose_other_city" class="choose__other-city">Выбрать другой город</a>
    </div>
</div>