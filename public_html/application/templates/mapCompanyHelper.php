<?
/**
 * @var \DDelivery\DDeliveryUI $this
 * @var \DDelivery\Point\DDeliveryInfo[] $selfCompanyList
 * @var string $staticURL
 */
?>
<ul class="clearfix">
    <?
    $companySubInfo = $this->getCompanySubInfo();
    $count = 0;
    foreach($selfCompanyList as $selfCompany):
        if($count >= 8)
            break;
        $count++;
        $ico = isset($companySubInfo[$selfCompany->delivery_company]) ? $companySubInfo[$selfCompany->delivery_company]['ico'] : 'pack';
        ?>
        <li>
            <a title="<?=$selfCompany->delivery_company_name?>" href="javascript:void(0)" data-id="<?=$selfCompany->delivery_company?>" class="clearfix border <?//hasinfo?>">
                                <span class="img">
                                    <img src="<?=$staticURL?>img/logo/<?=$ico?>_1.png" alt="<?=$selfCompany->delivery_company_name?>"/>
                                    <img class="big" src="<?=$staticURL?>img/logo/<?=$ico?>.png" alt="<?=$selfCompany->delivery_company_name?>"/>
                                </span>

                <span class="price"><?=floor($selfCompany->clientPrice)?> <i class="icon-rub">&nbsp;</i></span>

                <span class="date">
                    <strong><?=$selfCompany->delivery_time_min?></strong> <?=\DDelivery\Utils::plural($selfCompany->delivery_time_min, 'день', 'дня&nbsp;', 'дней', 'дней', false)?>
                </span>
                <i class="shadow">&nbsp;</i>
            </a>
        </li>
    <?endforeach;?>
</ul>