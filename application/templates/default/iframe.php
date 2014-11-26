<!doctype html>
<html lang="ru-RU">
    <head>
        <meta charset="UTF-8">
        <link href='//fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

        <link rel="stylesheet" href="<?=$styleUrl?>css/screen.css?<?=$version?>"/>
    </head>
    <body>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="application/javascript">
        var ddCaptionConfig = {
            caption1:"<?=$captions['CAPTION6']?>",
            caption2:"<?=$captions['CAPTION7']?>",
            caption3:"<?=$captions['CAPTION8']?>",
            caption4:"<?=$captions['CAPTION9']?>",
            caption5:"<?=$captions['CAPTION10']?>"
        }
    </script>
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
    <style type="text/css">
        .map-popup{
            width: <?=$this->shop->getModuleWidth();?>px;
            height: <?=$this->shop->getModuleHeight();?>px;
        }
        .map-canvas{
            width: <?=$this->shop->getModuleWidth();?>px;
            height: <?=((int)$this->shop->getModuleHeight() - 90);?>px
        }
        .map-popup .map-popup__main{
            height: <?=((int)$this->shop->getModuleHeight() - 90);?>px
        }
    </style>
    <div id="ddelivery"></div>
    <div id="ddelivery_loader">
        <div class="map-popup">
            <div class="map-popup__head">
                <p><?=$captions['CAPTION1']?></p>

                <div class="map-popup__head__close">&nbsp;</div>
            </div>
            <!--map-popup__head end-->
            <div class="map-popup__main">
                <div class="map-popup__main__overlay">&nbsp;</div>
                <div class="map-popup__main__delivery">
                    <div class="loader">
                        <p><?=$captions['CAPTION2']?></p>
                        <img src="<?=$styleUrl?>img/ajax_loader_horizont.gif"/>
                    </div>
                    <div>
                        <p class="load_error">
                            <?=$captions['CAPTION3']?><a href="javascript:void(0)"><?=$captions['CAPTION4']?></a>
                        </p>
                    </div>
                </div>

            </div>
            <div class="map-popup__bott">
                <a href="http://ddelivery.ru/" target="blank"><?=$captions['CAPTION5']?></a>
            </div>

        </div>
    </div>
    <script>

        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter25675664 = new Ya.Metrika({id:25675664,
                        webvisor:true,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true});
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");

        $(function(){
            DDeliveryIframe.init(<?=json_encode($scriptURL)?>, <?=json_encode($styleUrl)?>);
        });
    </script>
    </body>
</html>