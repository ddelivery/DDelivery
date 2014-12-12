<div class="widget-drop__choose theme-violet">
    <div class="widget-drop__choose_i">
        <div class="widget-drop__close"></div>
        <div class="widget-drop__city">
            <p>Выберите город</p>
            <form method="post" class="widget-drop__form">
                <input id="edit_city" type="text" value=""/>
                <input type="submit" id="widget__search__btn" value=""/>
            </form>
        </div>
        <div class="city-drop__choose">
            <div class="dd_loader"  style="text-align: center; padding: 20px;display: none" >
                <img  align="center" style="" src="<?=$url?>widget/img/ajax_loader.gif" />
            </div>
            <ul class="choose-list">

                <?foreach($topCity as $item ):?>
                    <li>
                        <a href="javascript:void(0)" data="<?=$item['_id']?>" class="choose-list__item"><strong><?=$item['name']?></strong><em><?=$item['display_name']?></em></a>
                    </li>
                <?endforeach?>
            </ul>
        </div>

    </div>
</div>
<!--widget-drop end-->
<?/*

<div class="widget-drop__choose theme-violet">
    <div class="widget-drop__choose_i">
        <div class="widget-drop__close"></div>
        <div class="widget-drop__city">
            <p>Выберите город</p>
            <form method="post" class="widget-drop__form">
                <input id="ddelivery_city_edit" placeholder="Введите город" type="text"/>
                <input type="submit" value=""/>
            </form>
        </div>
        <div class="city-drop__choose">
            <div id="dd_loader" class="dd_loader" style="text-align: center; padding: 20px;display: none" >
                <img  align="center" style="" src="<?=$url?>widget/img/ajax_loader.gif" />
            </div>
            <ul id="ddelivery_choose_list" class="choose-list">
                <?foreach($topCity as $item ):?>
                    <li>
                        <input type="hidden" class="choose_city_id" value="<?=$item['_id']?>" />
                        <a href="javascript:void(0)" class="choose-list__item"><strong><?=$item['name']?></strong><em><?=$item['display_name']?></em></a>
                    </li>
                <?endforeach?>
            </ul>
        </div>

    </div>
</div>
<!--widget-drop end-->
*/ ?>
<?php /*

<div class="widget-drop theme-violet">
    <div class="widget-drop_i">
        <div class="close"></div>
        <div class="city">
            <p>Выберите город</p>
            <form method="post" style="display:block">
                <input type="text" class="dd_widget_city_search" placeholder="Введите город"/>
                <input type="submit" value=""/>
            </form>
        </div>
        <div class="city-drop">
            <div class="loader" style="text-align: center; padding: 20px;display: none" >
                <img  align="center" style="" src="<?=$url?>html/widget/img/ajax_loader.gif" />
            </div>
            <ul>
                <?php
                foreach($cityList as $key=>$item){
                ?>
                <li>
                    <a class="dd_city_item" href="javascript:void(0);" >
                        <input type="hidden" class="dd_city_name" value="<?=$key?>" />
                        <strong><?=$item['name']?></strong></em>
                    </a>
                </li>
                <?php
                }
                ?>
                <!--
                <li><a href="#"><strong>Анапа</strong><em>обл. Анапская обл., Анапский район</em></a></li>
                <li><a href="#"><strong>Анапа</strong><em>обл. Анапская обл., Анапский район</em></a></li>
                <li><a href="#"><strong>Анапа</strong><em>обл. Анапская обл., Анапский район</em></a></li>
                <li><a href="#"><strong>Анапа</strong><em>обл. Анапская обл., Анапский район</em></a></li>
                <li><a href="#"><strong>Анапа</strong><em>обл. Анапская обл., Анапский район</em></a></li>
                <li><a href="#"><strong>Анапа</strong><em>обл. Анапская обл., Анапский район</em></a></li>
                -->
            </ul>
        </div>

    </div>
</div>
<!--widget-drop end-->
 */?>