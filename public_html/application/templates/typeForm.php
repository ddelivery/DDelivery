<?/**
 * @var array $cityData
 * @var \DDelivery\DDeliveryUI $this
 */ ?>
<div class="map-popup">
    <div class="map-popup__head">
        <p>Способы доставки в</p>

        <div class="delivery-place" style="width:280px;">
            <input type="hidden" name="ddelivery_city" value="<?=$cityData['_id']?>"/>
            <div class="delivery-place__title">
                <input type="text" title="<?=$cityData['display_name']?>"/>
                <span><i>&nbsp;</i></span>
            </div>
            <div class="delivery-place__drop">
                <div class="delivery-place__drop_i">
                    <h2>Популярные города:</h2>
                    <ul>
                        <li>
                            <a href="#" data-id="<?=$cityData['_id']?>" class="active">
                                <strong><?=$cityData['type'].'. '.$cityData['name']?></strong>
                                <?if($cityData['name'] != $cityData['region']):?>
                                    , <?=$cityData['region']?>
                                <?endif;?>
                            </a>
                        </li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
                        <li><a href="#" data-id="151184"><strong>г. Ханты-Мансийск</strong> обл.Ханты-Мансийский</a></li>
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
                <?if(in_array(\DDelivery\Sdk\DDeliverySDK::TYPE_SELF,  $this->supportedTypes)):?>
                    <tr>
                        <td class="col1">
                            <input type="radio" name="ddeliveryType" checked value="<?=\DDelivery\Sdk\DDeliverySDK::TYPE_SELF?>"/>
                        </td>
                        <td class="col2">
                            <i class="icon-car">&nbsp;</i>
                        </td>
                        <td class="col3">
                            <p>
                                <strong>Доставка курьером</strong>
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
                <?endif;?>
                <?if(in_array(\DDelivery\Sdk\DDeliverySDK::TYPE_COURIER,  $this->supportedTypes)):?>
                    <tr>
                        <td class="col1">
                            <input type="radio" name="ddeliveryType" value="<?=\DDelivery\Sdk\DDeliverySDK::TYPE_COURIER?>"/>
                        </td>
                        <td class="col2">
                            <i class="icon-pack">&nbsp;</i>
                        </td>
                        <td class="col3">
                            <p>
                                <strong>Пункт выдачи или ячейка</strong>
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
                <?endif;?>
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