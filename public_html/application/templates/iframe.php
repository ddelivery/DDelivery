<!doctype html>
<html lang="ru-RU">
    <head>
        <meta charset="UTF-8">
        <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?=$staticURL?>css/screen.css?<?=$version?>"/>
    </head>
    <body>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/modernizr.custom.76185.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.mCustomScrollbar.concat.min.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.custom-radio-checkbox.min.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.formtips.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.maskedinput.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.JSON.min.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.iframe.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.map.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.header.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.courier.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.contact_form.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.type_form.js?<?=$version?>"></script>
    <script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" async="async" type="text/javascript"></script>
    <div id="ddelivery"></div>
    <div id="ddelivery_loader">
        <div class="map-popup">
            <div class="map-popup__head">
                <p>DDelivery. Доставка в удобную Вам точку.</p>

                <div class="map-popup__head__close">&nbsp;</div>
            </div>
            <!--map-popup__head end-->
            <div class="map-popup__main">
                <div class="map-popup__main__overlay">&nbsp;</div>
                <div class="map-popup__main__delivery">
                    <div class="loader">
                        <p>Подождите пожалуйста, мы ищем лучшие предложения</p>
                        <img src="<?=$staticURL?>/img/ajax_loader_horizont.gif"/>
                    </div>
                    <div>
                        <p class="load_error">
                            Произошла ошибка, <a href="javascript:void(0)">повторить запрос</a>
                        </p>
                    </div>
                </div>

            </div>
            <div class="map-popup__bott">
                <a href="http://ddelivery.ru/" target="blank">Сервис доставки DDelivery.ru</a>
            </div>

        </div>
    </div>
    <script>
        $(function(){
            DDeliveryIframe.init(<?=json_encode($scriptURL)?>, <?=json_encode($staticURL)?>);
        });
    </script>
    </body>
</html>