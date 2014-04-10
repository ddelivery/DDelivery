<?
/**
 * @var DDelivery\Point\DDeliveryPointCourier[] $courierCompanyList
 */


?>
<div class="map-popup">

    <div class="map-popup__head">
        <p>Я хочу</p>

        <div class="delivery-type">
            <div class="delivery-type__title">
                <img src="img/icons/icon-courier.png"/>забрать курьером от 100 руб<span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-type__drop">
                <ul>
                    <li>
                        <a href="#">
                            <span class="name">доаставка курьером</span>
                            <span class="price">100 <i class="icon-rub">&nbsp;</i></span>
                            <span class="date">от <strong>1</strong> дня</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="name">Пункт выдачи</span>
                            <span class="price">100 <i class="icon-rub">&nbsp;</i></span>
                            <span class="date">от <strong>1</strong> дня</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="name">доаставка курьером</span>
                            <span class="price">100 <i class="icon-rub">&nbsp;</i></span>
                            <span class="date">от <strong>1</strong> дня</span>
                        </a>
                    </li>
                </ul>
            </div>
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
    <div class="map-popup__main__delivery">
        <table>
            <?foreach($courierCompanyList as $courierCompany):
                ?>
                <tr>
                    <td class="col1">
                        <input type="radio" name="delivery" id="delivery1"/>
                    </td>
                    <td class="col2">
                        <img src="<?=$staticPath?>img/logo/<?=$companies[$courierCompany->delivery_company]['ico']?>.png" alt="title"/>
                    </td>
                    <td class="col3">
                        <p>
                            <strong><?=$courierCompany->delivery_company_name?></strong>
                        </p>
                    </td>
                    <td class="col4">
                        от <strong><?=$courierCompany->delivery_price?> <i class="icon-rub">&nbsp;</i></strong>
                    </td>
                    <td class="col5">
                        от <strong><?=$courierCompany->delivery_time_min?></strong> дня
                    </td>
                </tr>
            <?endforeach;?>

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