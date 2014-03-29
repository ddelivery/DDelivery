<?
/**
 * @var \DDelivery\DDeliveryUI $this
 */
?>
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
            <div class="delivery-place__title">
                <input type="text" title="г. Ханты-Мансийск, обл.Ханты-Мансийский"/>
                <span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-place__drop">
                <div class="delivery-place__drop_i">
                    <h2>Популярные города:</h2>
                    <ul>
                        <li><a href="#"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" class="active"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
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
            <div class="map-canvas" style="width: 1000px; height: 610px"></div>
            <div class="map__search clearfix">
                <input type="text" placeholder="Адрес или объект"/>
                <input type="submit" value="ПОИСК"/>
            </div>
        </div>
        <!-- map end-->

        <div class="map-popup__main__right">
            <div class="map-popup__main__right__btn"><i>&nbsp;</i></div>
            <h2>Пункты:</h2>

            <div class="places">
                <ul class="clearfix">
                    <li>
                        <a href="#info1" class="clearfix border">
                            <span class="img"><img src="img/67x49.png" alt="title"/></span>
                            <span class="price">100 $</span>
                            <span class="date">от <strong>1</strong> дня</span>
                            <i class="shadow">&nbsp;</i>
                        </a>
                    </li>
                    <li>
                        <a href="#info2" class="clearfix hasinfo">
                            <span class="img"><img src="img/67x49.png" alt="title"/></span>
                            <span class="price">100 $</span>
                            <span class="date">от <strong>1</strong> дня</span>
                            <i class="shadow">&nbsp;</i>
                        </a>
                    </li>
                    <li>
                        <a href="#info3" class="clearfix bg hasinfo">
                            <span class="img"><img src="img/67x49.png" alt="title"/></span>
                            <span class="price">100 $</span>
                            <span class="date">от <strong>1</strong> дня</span>
                            <i class="shadow">&nbsp;</i>
                        </a>
                    </li>
                    <li>
                        <a href="#info4" class="clearfix">
                            <span class="img"><img src="img/67x49.png" alt="title"/></span>
                            <span class="price">100 $</span>
                            <span class="date">от <strong>1</strong> дня</span>
                            <i class="shadow">&nbsp;</i>
                        </a>
                    </li>
                </ul>
            </div>
            <!--places end-->

            <div class="filters">
                <h2>Фильтр</h2>

                <div class="filters__small clearfix">
                    <ul>
                        <li><a href="#" class="bg"><i class="icon-dollar">&nbsp;</i></a></li>
                        <li><a href="#" class="border"><i class="icon-credit">&nbsp;</i></a></li>
                        <li><a href="#"><i class="icon-time">&nbsp;</i></a></li>
                        <li><a href="#" class="border"><i class="icon-wear">&nbsp;</i></a></li>
                        <li><a href="#" class="border"><i class="icon-safe">&nbsp;</i></a></li>
                        <li><a href="#"><i class="icon-safe">&nbsp;</i></a></li>
                    </ul>
                </div>

                <div class="filters__big">
                    <p><strong>Способ оплаты</strong><a href="#" class="bg">Нал</a><a href="#" class="border">Карта</a>
                    </p>

                    <p><strong>Доп услуги</strong><a href="#">24 часа</a><a href="#" class="bg">Примерочная</a></p>

                    <p><strong>Тип пункта</strong><a href="#" class="bg">Ячейка</a><a href="#">Живой пункт</a></p>
                </div>

            </div>
            <!--filters end-->

        </div>

        <div class="map-popup__info" id="info2">
            <div class="map-popup__info__close">&nbsp;</div>
            <div class="map-popup__info__title">
                <p>По данному адресу находится несколько пунктов. Выберите нужный.</p>

                <h2>ПВЗ: BoxBerry: Таганрог #845 Boxberry #1376</h2>
            </div>
            <div class="map-popup__info__table">
                <table>
                    <tr>
                        <td class="col1">Цена:</td>
                        <td class="col2"><strong>100 <i class="icon-rub">&nbsp;</i></strong>

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
                        <td class="col2">от <strong>1</strong> дня</td>
                    </tr>
                    <tr>
                        <td class="col1">Вариант оплаты:</td>
                        <td class="col2">Наличными</td>
                    </tr>
                </table>
            </div>
            <div class="map-popup__info__btn">
                <a href="#">Заберу здесь</a>
            </div>
            <div class="map-popup__info__more">
                <div class="map-popup__info__more__btn"><a href="#">Подробнее<i>&nbsp;</i></a></div>
                <div class="map-popup__info__more__text">
                    <div class="map-popup__info__more__text_i">
                        <table>
                            <tr>
                                <td class="col1">Адресс:</td>
                                <td class="col2">
                                    ул. Социалистическая, д. 138
                                </td>
                            </tr>
                            <tr>
                                <td class="col1">Расписание работы:</td>
                                <td class="col2">
                                    пн: 09:00-18:30;<br/>
                                    вт: 09:00-18:30;<br/>
                                    ср: 09:00-18:30;<br/>
                                    чт: 09:00-18:30;<br/>
                                    пт: 09:00-18:30;<br/>
                                    сб: 10:00-14:00;
                                </td>
                            </tr>
                            <tr>
                                <td class="col1">Подробнее:</td>
                                <td class="col2">
                                    Пункт выдачи BoxBerry
                                </td>
                            </tr>
                            <tr>
                                <td class="col1">Подробнее:</td>
                                <td class="col2">
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="map-popup__info" id="info3">
            <div class="map-popup__info__close">&nbsp;</div>
            <div class="map-popup__info__title">
                <p>2 По данному адресу находится несколько пунктов. Выберите нужный.</p>

                <h2>2 ПВЗ: BoxBerry: Таганрог #845 Boxberry #1376</h2>
            </div>
            <div class="map-popup__info__table">
                <table>
                    <tr>
                        <td class="col1">Тип пункта:</td>
                        <td class="col2">от <strong>1</strong> дня</td>
                    </tr>
                    <tr>
                        <td class="col1">Вариант оплаты:</td>
                        <td class="col2">Наличными</td>
                    </tr>
                </table>
            </div>
            <div class="map-popup__info__btn">
                <a href="#">Заберу здесь</a>
            </div>
            <div class="map-popup__info__more">
                <div class="map-popup__info__more__btn"><a href="#">Подробнее<i>&nbsp;</i></a></div>
                <div class="map-popup__info__more__text">
                    <div class="map-popup__info__more__text_i">
                        <table>
                            <tr>
                                <td class="col1">Адресс:</td>
                                <td class="col2">
                                    ул. Социалистическая, д. 138
                                </td>
                            </tr>
                            <tr>
                                <td class="col1">Расписание работы:</td>
                                <td class="col2">
                                    пн: 09:00-18:30;<br/>
                                    вт: 09:00-18:30;<br/>
                                    ср: 09:00-18:30;<br/>
                                    чт: 09:00-18:30;<br/>
                                    пт: 09:00-18:30;<br/>
                                    сб: 10:00-14:00;
                                </td>
                            </tr>
                            <tr>
                                <td class="col1">Подробнее:</td>
                                <td class="col2">
                                    Пункт выдачи BoxBerry
                                </td>
                            </tr>
                            <tr>
                                <td class="col1">Подробнее:</td>
                                <td class="col2">
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                    Угол ул. Социалистическая и
                                    пер. Парковый. Двухэтажное
                                    красное здание, крайняя дверь
                                    слева. (Маршрутка №1 и №29)
                                    Проезд по ул. Дзержинского:
                                </td>
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