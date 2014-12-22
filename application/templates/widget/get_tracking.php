<div class="widget-drop__choose">
    <div class="widget-drop__choose_i">

        <div class="widget-drop__close"></div>

        <div class="widget-drop__select-city">
            <div style="margin: 10px 0px 0 0px;">
                <p>
                    <strong>
                        Статус заказа в системе DDelivery:
                    </strong>
                </p>
                <p><?=$statusDecr?></p>
            </div>
            <?if($statusMessage):?>
                <div style="margin: 10px 0px 0 0px;">
                    <p>
                        <strong>
                            Сопутсвующий статус:
                        </strong>
                    </p>
                    <p><?=$statusMessage?></p>
                </div>
            <?endif;?>

            <div style="margin: 10px 0px 0 0px;">
                <a id="dd_tracking_other" href="javascript:void(0);">отследить другой заказ</a>
            </div>
        </div>

    </div>
</div>