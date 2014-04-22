<?
/**
 * @var \DDelivery\DDeliveryUI $this
 * @var array[] $cityList
 * @var DDelivery\Point\DDeliveryPointSelf[] $selfCompanyList
 */

?>
<style type="text/css">
    .map-popup div.map__search_dropdown {
        background-color: #fff;
        padding: 5px;
        margin-top: 36px;
        display: none;
    }
</style>
<div class="map-popup">

    <div class="map-popup__head">
        <p>Я хочу</p>

        <div class="delivery-type">
            <div class="delivery-type__title">
                <img src="img/icons/shipping-grey.png"/>забрать из пункта выдачи<span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-type__drop">
                <ul>
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
            ?>
            <input type="hidden" name="ddelivery_city" value="<?=$cityData['_id']?>"/>
            <div class="delivery-place__title">
                <input type="text" title="<?=$cityData['display_name']?>"/>
                <span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-place__drop">
                <div class="delivery-place__drop_i">
                    <h2>Популярные города:</h2>
                    <ul>
                        <?foreach($cityList as $cityData):?>
                            <li><a href="javascript:void(0)" data-id="<?=$cityData['_id']?>"
                                   <?if($cityId == $cityData['_id']):?>class="active"<?endif;?>>
                                    <strong><?=$cityData['type'].'. '.$cityData['name']?></strong>
                                    <?if($cityData['name'] != $cityData['region']):?>
                                        обл. <?=$cityData['region']?>
                                    <?endif;?>
                                </a></li>
                        <?endforeach;?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="map-popup__head__close">&nbsp;</div>
    </div>
    <!--map-popup__head end-->
    <div class="map-popup__main">
        <div class="map-popup__main__overlay">&nbsp;</div>
        <div class="map">
            <div class="map-canvas" style="width: 1000px; height: 568px"></div>
            <div class="map__search clearfix">
                <input type="text" placeholder="Адрес или объект"/>
                <input type="submit" value="ПОИСК"/>
                <div class="map__search_dropdown"></div>
            </div>
        </div>
        <!-- map end-->

        <div class="map-popup__main__right">
            <div class="map-popup__main__right__btn"><i>&nbsp;</i></div>
            <h2>Пункты:</h2>

            <div class="places">
                <?require(__DIR__.DIRECTORY_SEPARATOR.'mapCompanyHelper.php');?>
            </div>
            <!--places end-->

            <div class="filters">
                <h2>Фильтр</h2>

                <div class="filters__small clearfix">
                    <ul>
                        <li><a href="javascript:void(0)" title="Принимает наличные" class="border" data-filter="cash"><i class="icon-dollar">&nbsp;</i></a></li>
                        <li><a href="javascript:void(0)" title="Принимает пластиковые карты" class="border" data-filter="card"><i class="icon-credit">&nbsp;</i></a></li>
                        <li><a href="javascript:void(0)" title="Круглосуточно" data-filter="time"><i class="icon-time2">&nbsp;</i></a></li>
                        <li><a href="javascript:void(0)" title="Наличие примерочной" data-filter="has_fitting_room"><i class="icon-wear">&nbsp;</i></a></li>
                        <li><a href="javascript:void(0)" title="Ячейка самовывоза" class="border" data-filter="type1"><i class="icon-safe">&nbsp;</i></a></li>
                        <li><a href="javascript:void(0)" title="Живой пункт" class="border" data-filter="type2"><i class="icon-light">&nbsp;</i></a></li>
                    </ul>
                </div>

                <div class="filters__big">
                    <?//Если вставить пробелы или \n то все разъедется?>
                    <p><strong>Способ оплаты</strong><a
                            href="javascript:void(0)" class="border" data-filter="cash">Нал</a><a
                            href="javascript:void(0)" class="border" data-filter="card">Карта</a>
                    </p>

                    <p><strong>Доп услуги</strong><a
                            href="javascript:void(0)" class="" data-filter="time">24 часа</a><a
                            href="javascript:void(0)" class="" data-filter="has_fitting_room">Примерочная</a></p>

                    <p><strong>Тип пункта</strong><a
                            href="javascript:void(0)" class="border" data-filter="type1">Ячейка</a><a
                            href="javascript:void(0)" class="border" data-filter="type2">Живой пункт</a></p>
                </div>

            </div>
            <!--filters end-->

        </div>

        <div class="map-popup__info">
            <div class="map-popup__info__close">&nbsp;</div>
            <div class="map-popup__info__title">
                <p class="more">По данному адресу находится несколько пунктов. Выберите нужный.</p>

                <h2>ПВЗ: BoxBerry: Таганрог #845 Boxberry #1376</h2>
            </div>
            <div class="map-popup__info__table">
                <table>
                    <tr>
                        <td class="col1">Цена:</td>
                        <td class="col2"><strong><span class="rub">100</span> <i class="icon-rub">&nbsp;</i></strong>

                            <div class="tip-box">
                                <i>&nbsp;</i>

                                <div class="tip-box_i">
                                    <span class="tip-box__arr">&nbsp;</span>
                                    <span class="tip-box__close">&nbsp;</span>

                                    <div class="text">
                                        <h2>Описание</h2>

                                        <p>
                                            Угол ул. Социалистическая и
                                            пер. Парковый. Двухэтажное
                                            красное здание, крайняя дверь
                                            слева. (Маршрутка №1 и №29)
                                            Проезд по ул. Дзержинского:
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!--tip-box end-->

                        </td>
                    </tr>
                    <tr>
                        <td class="col1">Тип пункта:</td>
                        <td class="col2 day">от <strong>1</strong> дня</td>
                    </tr>
                    <tr>
                        <td class="col1">Вариант оплаты:</td>
                        <td class="col2 payType"></td>
                    </tr>
                </table>
            </div>
            <div class="map-popup__info__btn">
                <a href="#">Заберу здесь</a>
            </div>
            <div class="map-popup__info__more">
                <div class="map-popup__info__more__btn"><a href="javascript:void(0)">Подробнее<i>&nbsp;</i></a></div>
                <div class="map-popup__info__more__text">
                    <div class="map-popup__info__more__text_i">
                        <table>
                            <tr>
                                <td class="col1">Адрес:</td>
                                <td class="col2 address"></td>
                            </tr>
                            <tr>
                                <td class="col1">Расписание работы:</td>
                                <td class="col2 schedule"></td>
                            </tr>
                            <tr>
                                <td class="col1">Пункт выдачи:</td>
                                <td class="col2 company"></td>
                            </tr>
                            <tr>
                                <td class="col1">Подробнее:</td>
                                <td class="col2 more"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="map-popup__bott">
        <a href="http://ddelivery.ru" target="blank">Сервис доставки DDelivery.ru</a>
    </div>

</div>