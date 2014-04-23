<?
/**
 * @var string $staticPath
 */
?>
<div class="delivery-type__title courier">
    <img src="<?=$staticURL?>img/icons/icon-courier.png"/>забрать курьером от <span class="price"></span> руб<span class="arrow"><i>&nbsp;</i></span>
</div>
<div class="delivery-type__title self">
    <img src="<?=$staticURL?>img/icons/icon-courier.png"/>забрать самовывозом от <span class="price"></span> руб<span class="arrow"><i>&nbsp;</i></span>
</div>
<div class="delivery-type__drop">
    <ul>
        <li class="delivery-type__drop_self">
            <a href="javascript:void(0)">
                <span class="name">доаставка курьером</span>
                <span class="price"><span>100</span> <i class="icon-rub">&nbsp;</i></span>
                <span class="date">от <strong>1</strong> дня</span>
            </a>
        </li>
        <li class="delivery-type__drop_courier">
            <a href="javascript:void(0)">
                <span class="name">Пункт выдачи</span>
                <span class="price"><span>100</span> <i class="icon-rub">&nbsp;</i></span>
                <span class="date">от <strong>1</strong> <span>дня</span></span>
            </a>
        </li>
    </ul>
</div>