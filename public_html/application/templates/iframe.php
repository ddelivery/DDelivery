<!doctype html>
<html lang="ru-RU">
    <head>
        <meta charset="UTF-8">
        <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?=$staticURL?>css/screen.css"/>
    </head>
    <body>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/modernizr.custom.76185.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.custom-radio-checkbox.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.formtips.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.maskedinput.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.iframe.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.map.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.header.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.courier.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.contact_form.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.type_form.js"></script>
    <script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" async="async" type="text/javascript"></script>
    <div id="ddelivery">

    </div>
    <script>
        $(function(){
            DDeliveryIframe.init(<?=json_encode($scriptURL)?>, <?=json_encode($staticURL)?>);
        });
    </script>
    </body>
</html>