<?/**
 * @var array[] $cityList
 * @var \DDelivery\DDeliveryUI $this
 * @var array $config
 */ ?>
<div class="map-popup">
    <div class="map-popup__head">
        <p>Способы доставки в</p>

        <div class="delivery-place" style="width:280px;">
            <?
            $cityData = reset($cityList);
            $cityId = $cityData['_id'];
            ?>
            <input type="hidden" name="ddelivery_city" value="<?=$cityData['_id']?>"/>
            <div class="delivery-place__title">
                <input type="text" title="<?=htmlspecialchars($cityData['display_name'])?>"/>
                <span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-place__drop">
                <div class="delivery-place__drop_i">
                    <h2 class="search">Поиск города:</h2>
                    <ul class="search"></ul>
                    <h2 class="pop">Популярные города:</h2>
                    <ul class="pop">
                        <? include(__DIR__ . '/cityHelper.php');?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="map-popup__head__close">&nbsp;</div>
    </div>
    <!--map-popup__head end-->
    <div class="map-popup__main">
        <div class="map-popup__main__overlay">&nbsp;</div>
        <div class="map-popup__main__delivery">
            <table>
                <?
                $currentData = $data['courier'];
                ?>
                <tr class="courier <?=$currentData['disabled'] ? 'disabled' : ''?>">
                    <td class="col1">
                        <input type="radio" <?=$currentData['disabled'] ? 'disabled' : ''?> name="ddeliveryType" checked value="<?=\DDelivery\Sdk\DDeliverySDK::TYPE_COURIER?>"/>
                    </td>
                    <td class="col2">
                        <i class="icon-car">&nbsp;</i>
                    </td>
                    <td class="col3">
                        <p>
                            <strong> Доставка курьером</strong>
                            <span class="not_support">Данный способ доставки недоступен для выбранного вами населенного пункта.<br></span>
                            Доставка заказа по указанному вами адресу
                        </p>
                    </td>
                    <td class="col4" style="position: relative">
                        <span>от <strong><span class="min_price"><?=$currentData['minPrice']?></span> <i class="icon-rub">&nbsp;</i></strong></span>
                        <img src="<?=$styleUrl?>img/ajax_loader_min.gif" style="position: absolute; left:10px" class="h">
                    </td>
                    <td class="col5">
                        <span>от <strong><span class="min_time"><?=$currentData['minTime']?></span></strong> <span class="time_str"><?=$currentData['timeStr']?></span></span>
                    </td>
                </tr>
                <?
                $currentData = $data['self'];
                ?>
                <tr class="self <?=$currentData['disabled'] ? 'disabled' : ''?>">
                    <td class="col1">
                        <input type="radio" <?=$currentData['disabled'] ? 'disabled' : ''?> name="ddeliveryType" value="<?=\DDelivery\Sdk\DDeliverySDK::TYPE_SELF?>"/>
                    </td>
                    <td class="col2">
                        <i class="icon-pack">&nbsp;</i>
                    </td>
                    <td class="col3">
                        <p>
                            <strong>Пункт выдачи или ячейка </strong>
                            <span class="not_support">Данный способ доставки недоступен для выбранного вами населенного пункта.<br></span>
                            Доставка заказа до выбранного вами пункта выдачи заказов.
                        </p>
                    </td>
                    <td class="col4" style="position: relative">
                        <span>от <strong><span class="min_price"><?=$currentData['minPrice']?></span> <i class="icon-rub">&nbsp;</i></strong></span>
                        <img src="<?=$styleUrl?>img/ajax_loader_min.gif" style="position: absolute; left:10px" class="h">
                    </td>
                    <td class="col5">
                        <span>от <strong><span class="min_time"><?=$currentData['minTime']?></span></strong> <span class="time_str"><?=$currentData['timeStr']?></span></span>
                    </td>
                </tr>
                <?/*
                <tr>
                    <td class="col1">
                        <input type="radio" name="ddelivery" id="delivery3"/>
                    </td>
                    <td class="col2">
                        <i class="icon-letter">&nbsp;</i>
                    </td>
                    <td class="col3">
                        <p>
                            <strong>Почта</strong>
                            Пара слов о данном способе доставки
                        </p>
                    </td>
                    <td class="col4">
                        от <strong>100 <i class="icon-rub">&nbsp;</i></strong>
                    </td>
                    <td class="col5">
                        от <strong>1</strong> дня
                    </td>
                </tr>
                */?>
            </table>
        </div>
        <div class="map-popup__main__delivery__next">
            <a href="javascript:void(0)">Далее<i>&nbsp;</i></a>
        </div>
    </div>
    <div class="map-popup__bott">
        <a href="http://ddelivery.ru/" target="blank">Сервис доставки DDelivery.ru</a>
    </div>

</div>