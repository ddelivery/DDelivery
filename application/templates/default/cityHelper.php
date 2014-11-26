<?foreach($cityList as $cityData):?>
    <?php if( !isset($cityData['name']) ) continue; ?>
    <li><a href="javascript:void(0)" data-id="<?=$cityData['_id']?>"
           <?if($cityId == $cityData['_id']):?>class="active"<?endif;?>>
            <strong><?=$cityData['type'].'. '.$cityData['name']?></strong>
            <?if($cityData['name'] != $cityData['region']):?>
                <?=$cityData['region']?> обл.
            <?endif;?>
        </a>
    </li>
<?endforeach;?>

