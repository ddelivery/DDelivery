<?
/**
 * @var DDelivery\Point\DDeliveryPointCourier[] $courierCompanyList
 * @var string $staticPath
 */


?>
<div class="map-popup">

    <div class="map-popup__head">
        <p>Я хочу</p>

        <div class="delivery-type">
            <?require(__DIR__.DIRECTORY_SEPARATOR.'typeHelper.php')?>
        </div>
        <!--delivery-type end-->

        <p class="in">в</p>

        <div class="delivery-place" style="width:354px;">
            <?
            $cityData = reset($cityList);
            $cityId = $cityData['_id'];
            ?>
            <input type="hidden" name="ddelivery_city" value="<?=$cityData['_id']?>"/>
            <div class="delivery-place__title">
                <input type="text" title="<?=$cityData['display_name']?>"/>
                <span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-place__drop">
                <div class="delivery-place__drop_i">
                    <h2 class="search">Поиск города:</h2>
                    <ul class="search"></ul>
                    <h2 class="pop">Популярные города:</h2>
                    <ul class="pop">
                        <?include(__DIR__.'/cityHelper.php');?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="map-popup__head__close">&nbsp;</div>
    </div>
    <!--map-popup__head end-->
    <div class="map-popup__main">
    <div class="map-popup__main__overlay">&nbsp;</div>
    <div class="map-popup__main__delivery small">
        <table>
            <?
            $courierCompanyListJson = array();
            foreach($courierCompanyList as $key => $courierCompany):
                $courierCompanyListJson[$courierCompany->delivery_company] = $courierCompany->toJson();
                ?>
                <tr>
                    <td class="col1">
                        <input type="radio" name="delivery_company" value="<?=$courierCompany->delivery_company?>" <?if($key==0):?>checked="checked"<?endif;?>/>
                    </td>
                    <td class="col2">
                        <img src="<?=$staticURL?>img/logo/<?php
                        echo ((isset(  $companies[$courierCompany->delivery_company]['ico'] ) )?$companies[$courierCompany->delivery_company]['ico']:'pack');
                        ?>.png" alt="title"/>
                    </td>
                    <td class="col3">
                        <p>
                            <strong><?=$courierCompany->delivery_company_name?></strong>
                        </p>
                    </td>
                    <td class="col4">
                        <strong><?=$courierCompany->getDeliveryInfo()->clientPrice?> <i class="icon-rub">&nbsp;</i></strong>
                    </td>
                    <td class="col5">
                        <strong><?=$courierCompany->delivery_time_min?></strong> <?=\DDelivery\Utils::plural($courierCompany->delivery_time_min, 'день', 'дня', 'дней', 'дней', false);?>
                    </td>
                </tr>
            <?endforeach;?>
            <script type="application/javascript">
                var couriers = <?=json_encode($courierCompanyListJson)?>;
            </script>
        </table>
    </div>
    <div class="map-popup__main__delivery__next">
        <a href="#">Далее<i>&nbsp;</i></a>
    </div>
</div>
    <div class="map-popup__bott">
        <a href="http://ddelivery.ru" target="blank">Сервис доставки DDelivery.ru</a>
    </div>

</div>