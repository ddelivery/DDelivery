<?if(count($topCity)):?>
    <?foreach($topCity as $item ):?>
        <li>
            <a href="javascript:void(0)" data="<?=$item['_id']?>" class="choose-list__item"><strong><?=$item['name']?></strong><em><?=$item['display_name']?></em></a>
        </li>
    <?endforeach?>
<?else:?>
    <li> <a href="javascript:void(0)" class="choose-list__item__noresult"><strong> Поиск не дал результатов </strong></li>
<?endif?>