
<div class="ddelivery-container" id="ddelivery-container" style="display: none;">

<div class="ddelivery">

    <!-- +select item -->
    <div class="ddelivery-select-item">
        <div class="ddelivery-customer-city">Ваш город определен автоматически как &mdash; <span id="ddelivery-customer-city" class="ddelivery-t-style2" geo-city>...</span> <a href="#" class="ddelivery-change-city">Изменить</a>

            <a href="#" class="ddelivery-filter-additional" style="font-style: normal; font-weight: normal;">Фильтры</a>
        </div>
        <div class="ddelivery-change-city-block">
            <a class="ddelivery-change-city-block-close ddelivery-crosshair"></a>
            <div class="ddelivery-change-city-block-content">
                <div><input id="ddelivery-city" placeholder="Начните вводить название города"/></div>

                <select size="5" id="ddelivery-city-list"></select>

                <div><a class="ddelivery-button1 ddelivery-button-select-city ddelivery-disabled"><span class="ddelivery-button1-left"></span><span class="ddelivery-button1-center">Выбрать этот город</span><span class="ddelivery-button1-right"></span></a></div>
            </div>
        </div>
        <div class="ddelivery-t-style3">
            <!-- Для данного заказа доставка во все пункты самовывоза 100 руб. -->
        </div>

        <!-- +filters -->
        <div class="ddelivery-filters">
            <div class="ddelivery-filter-additional-block">
                <a class="ddelivery-filter-additional-block-close ddelivery-crosshair"></a>
                <div class="ddelivery-filter-additional-block-content">
                    <span class="ddelivery-filter-additional-item"><input id="dd_chkHuman" checked="checked" type="checkbox" onchange="ddEngine.apply_filters();" /> <label for="dd_chkHuman">Пункты самовывоза</label></span>
                    <span class="ddelivery-filter-additional-item"><input id="dd_chkFittingPlace" type="checkbox" onchange="ddEngine.apply_filters();" /> <label for="dd_chkFittingPlace">С примерочной</label></span>
                    <span class="ddelivery-filter-additional-item"><input id="dd_chkBank" type="checkbox" onchange="ddEngine.apply_filters();" /> <label for="dd_chkBank">Безналичный расчет</label></span>
                    <span class="ddelivery-filter-additional-item"><input id="dd_chkAuto" checked="checked" type="checkbox" onchange="ddEngine.apply_filters();" /> <label for="dd_chkAuto">Автоматизированные</label></span>
                </div>
            </div>
            <!--
            <span class="ddelivery-filter-item"><input type="radio" name="filter" id="ddelivery-filter-bestpriceterm" /><label for="ddelivery-filter-bestpriceterm">Лучшее соотношение цена/срок</label></span>
            <span class="ddelivery-filter-item"><input type="radio" name="filter" id="ddelivery-filter-cheapest" /><label for="ddelivery-filter-cheapest">Самые дешевые</label></span>
            <span class="ddelivery-filter-item"><input type="radio" name="filter" id="ddelivery-filter-faster" /><label for="ddelivery-filter-faster">Самые быстрые<sup class="ddelivery-tooltip-question" tooltip="#ddelivery-tooltip-faster">?</sup></label></span>
            <span class="ddelivery-filter-item"><input type="radio" name="filter" id="ddelivery-filter-showall" /><label for="ddelivery-filter-showall">Показать все</label></span>
            -->
        </div>
        <!-- -filters -->

        <!-- +map -->

        <div map-wrapper>
            Укажите удобный для Вас пункт самовывоза на карте:
            <div class="ddelivery-map">
                <div>
                    <div id="map_container" style="width: 778px; height: 442px; float: left;"></div>
                    <div id="info_container" style="width: 0px; height: 442px; float: left;" class="info-container"></div>
                </div>
            </div>
        </div>
        <!-- -map -->
    </div>
    <!-- -select item -->

    <!-- +result -->
    <div class="ddelivery-result">
        <div>
            <span class="ddelivery-t-style3">Вы выбрали пункт доставки</span> <span class="ddelivery-item-number" point-name></span>
        </div>
        <div class="ddelivery-delivery-info">
            <div class="ddelivery-item-info1">
                <div class="ddelivery-row">
                    <span class="ddelivery-label1">Город:</span> <span class="ddelivery-labeled1" city-name>-</span>
                </div>
                <div class="ddelivery-row">
                    <span class="ddelivery-label1">Цена:</span> <span class="ddelivery-labeled1" price-holder>-</span>
                    <span class="ddelivery-label1">Время доставки:</span> <span class="ddelivery-labeled1" time-holder>-</span>
                </div>
            </div>
            <div class="ddelivery-item-info2"></div>
        </div>
        <div class="ddelivery-row"><a class="ddelivery-button1 ddelivery-button-change-item"><span class="ddelivery-button1-left"></span><span class="ddelivery-button1-center">Выбрать другой пункт на карте</span><span class="ddelivery-button1-right"></span></a></div>
    </div>
    <!-- -result -->

    <!-- +tooltips -->
    <div id="ddelivery-tooltip-faster" class="ddelivery-tooltip-block">
        <div class="ddelivery-tooltip-content">
            Очень быстрые доставки.
        </div>
    </div>
    <div id="ddelivery-tooltip-info" class="ddelivery-tooltip-block">
        <div class="ddelivery-tooltip-content">
            <span class="ddelivery-t-style1">DDelivery</span> — компания, сотрудничающая со всеми интернет-магазинами <br />и занимающаяся доставкой товаров из интернет-магазинов в любую точку страны.
        </div>
    </div>
    <!-- -tooltips -->
</div>
</div>


<!--- templates -->
<!-- singe item -->
<div id="single-item-template" style="display: none">

    <div class="ddelivery-crosshair" style="float: right; margin-right: 10px; margin-top: 10px;" onclick="ddEngine.resetPostamatSelection()"></div>

    <div class="ddelivery-map-item">
        [[ITEM_CONTENT]]
    </div>
</div>
<div id="multy-item-template" style="display: none">
    <div class="ddelivery-crosshair" style="float: right; margin-right: 10px; margin-top: 10px;" onclick="ddEngine.resetPostamatSelection()"></div>

    <div class="ddelivery-map-item">
        <div class="ddelivery-map-item-tabs-wrapper">
            <div class="ddelivery-map-item-tabs-text">По данному адресу находится несколько пунктов. <br />Выберите нужный.</div>
            <ul class="ddelivery-map-item-tabs">
                {% for point in points %}
                <li tab-content="#ddelivery-map-item-content-{{point._id}}">{{point.name}} #{{point._id}}</li>
                {% endfor %}
            </ul>
        </div>
        {% for point in points %}
        [[ITEM_CONTENT]]
        {% endfor %}
    </div>
</div>
<div id="single-item-content" style="display: none">
    <div class="ddelivery-map-item-content" id="ddelivery-map-item-content-{{point._id}}">
        <div class="ddelivery-item-info1">

            <div class="ddelivery-row" price-info-{{point._id}}>
            <span class="ddelivery-label1">Название:</span> <span class="ddelivery-labeled1">{{point.name}}</span>
        </div>

        <div class="ddelivery-row" price-indicator-{{point._id}}>
        <span class="ddelivery-label1">Идет просчет цены...</span><img src="/ddelivery/images/ajax-loader-inverse.gif" height="16" />
    </div>

    <div class="ddelivery-row" error-container-{{point._id}}>
    <span class="ddelivery-label1">ОШИБКА:</span> <span class="ddelivery-labeled1" error-message-{{point._id}}></span>
</div>

<div class="ddelivery-row" price-info-{{point._id}}>
<span class="ddelivery-label1">Цена:</span> <span class="ddelivery-labeled1" price-number-{{point._id}}></span>
<span class="ddelivery-label1">Время доставки:</span> <span class="ddelivery-labeled1" duration-{{point._id}}></span>
</div>

{% if point.metro != '' %}
<div class="ddelivery-row">
    <span class="ddelivery-label1">Станция метро:</span> <span class="ddelivery-labeled1">{{point.metro}}</span>
</div>
{% endif %}

<a delivery-button-{{point._id}} class="ddelivery-button1 ddelivery-button-select-item"><span class="ddelivery-button1-left"></span><span class="ddelivery-button1-center">Доставить сюда</span><span class="ddelivery-button1-right"></span></a>

</div>


<div class="ddelivery-item-info2">

    <div class="ddelivery-row">
        <span class="ddelivery-label2">Тип пункта:</span> <span class="ddelivery-labeled2">{% if point.type == 1 %}Автоматизированый{% else %}Обычный{% endif %}</span>
    </div>

    <div class="ddelivery-row">
        <span class="ddelivery-label2">Варианты оплаты:</span><span class="ddelivery-labeled2">{% if point.is_cash %}Наличными{% endif %}</span>
        {% if point.is_bank %}
        <span class="ddelivery-label2">&nbsp</span><span class="ddelivery-labeled2">Безналичный расчет</span>
        {% endif %}
    </div>

    <div class="ddelivery-row">
        <span class="ddelivery-label2">Адрес:</span>
        <span class="ddelivery-labeled2">{{  point.address }}</span>
    </div>

    <div class="ddelivery-row">
        <span class="ddelivery-label2">Расписание работы:</span><span class="ddelivery-labeled2">{{ point.fixed_schedule  }}</span>
    </div>

    {% if point.has_fitting_room %}
    <div class="ddelivery-row">
        <span class="ddelivery-label2">Примерочная:</span>
        <span class="ddelivery-labeled2">Есть</span>
    </div>
    {% endif %}

    {% if point.indoor_place != '' %}
    <div class="ddelivery-row">
        <span class="ddelivery-label2">Подробнее:</span>
        <span class="ddelivery-labeled2">{{point.indoor_place}}</span>
    </div>
    {% endif %}

    {% if point.description_in != '' %}
    <div class="ddelivery-row">
        <span class="ddelivery-label2">Как зайти:</span>
        <span class="ddelivery-labeled2">{{point.description_in}}</span>
    </div>
    {% endif %}

    {% if point.description_out != '' %}
    <div class="ddelivery-row">
        <span class="ddelivery-label2">Описание:</span>
        <span class="ddelivery-labeled2">{{point.description_out}}</span>
    </div>
    {% endif %}

    <div class="ddelivery-row">
        <span class="ddelivery-label2">Пункт сети:</span><span class="ddelivery-labeled2"><img width="130" height="30" src=""></span>
    </div>
</div>

</div>
</div>

