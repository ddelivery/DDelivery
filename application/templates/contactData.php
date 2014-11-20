<div class="map-popup">

    <div class="map-popup__head">
        <p>Уточнение улицы</p>

        <div class="map-popup__head__close">&nbsp;</div>
    </div>
    <!--map-popup__head end-->
    <div class="map-popup__main">
        <div class="map-popup__main__form">
            <div class="row clearfix">
                <div class="row__title">
                    <label for="sname">Фамилия</label>
                </div>
                <div class="row__inp error">
                    <input type="text" title="Иванов" id="sname"/>
                    <div class="error-box">
                        <i>&nbsp;</i> Вы сделали что-то неверно
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="row__title">
                    <label for="name">Имя</label>
                </div>
                <div class="row__inp">
                    <input type="text" title="Иван" id="name"/>
                    <div class="error-box">
                        <i>&nbsp;</i> Вы сделали что-то неверно
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="row__title">
                    <label for="phone">Мобильный телефон</label>
                </div>
                <div class="row__inp">
                    <input type="tel" class="phone-mask" id="phone"/>
                    <div class="error-box">
                        <i>&nbsp;</i> Вы сделали что-то неверно
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="row__title">
                    <label for="address">Адрес</label>
                </div>
                <div class="row__inp">
                    <input type="text" title="Улица" id="address"/>
                    <div class="error-box">
                        <i>&nbsp;</i> Вы сделали что-то неверно
                    </div>
                </div>
            </div>

            <div class="row row_pl clearfix">
                <div class="row__inp">
                    <input type="text" title="Дом" class="small"/>
                    <input type="text" title="Корпус" class="small"/>
                    <input type="text" title="Квартира" class="small"/>
                    <div class="error-box">
                        <i>&nbsp;</i> Вы сделали что-то неверно
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="row__title">
                    <label for="comments">Комментарий к курьеру</label>
                </div>
                <div class="row__inp">
                    <textarea id="comments" title="Напишите комментарий"></textarea>
                    <div class="error-box">
                        <i>&nbsp;</i> Вы сделали что-то неверно
                    </div>
                </div>
            </div>

            <div class="row-btns clearfix">
                <a class="prev" href="#"><i>&nbsp;</i>назад</a>
                <a class="next" href="#"><i>&nbsp;</i>Доставить по этому адресу</a>
            </div>

        </div>
    </div>
    <div class="map-popup__bott">
        <a href="http://ddelivery.ru" target="blank">Сервис доставки DDelivery.ru</a>
    </div>

</div>