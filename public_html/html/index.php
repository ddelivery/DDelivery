<div id="ddelivery"></div>
<script src="//api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU"></script>

<script>
    if(typeof(DDelivery) == 'undefined')
    var DDelivery = {
        componentUrl: "",
        staticPath: "",
        htmlObject: null,
        includeScripts: false,
        jQuery: null,
        map: null,
        init: function(objectId, componentUrl, staticPath, params) {
            this.htmlObject = document.getElementById(objectId);

            if(staticPath.length > 0 && staticPath.substr(staticPath.length-2, 1) != '/'){
                staticPath += '/';
            }
            this.staticPath = staticPath;
            this.componentUrl = componentUrl;

            if(!this.includeScripts) {
                this.includeScripts = true;

                this.utils.requireScript(this.staticPath+'js/modernizr.custom.76185.js');

                if(typeof(jQuery) == 'undefined' || typeof(jQuery.fn) == 'undefined' || typeof(jQuery.fn.jquery) == 'undefined' || !jQuery.fn.jquery) {
                    var ref$ = typeof($) != 'undefined' ? $ : undefined;
                    this.utils.requireStyle('//fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext');
                    this.utils.requireScript(
                        '//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js',
                        function(){
                            DDelivery.jQuery = jQuery;
                            $.noConflict();
                            $ = ref$;
                            DDelivery.initStep2();
                        }
                    );
                }else{
                    this.jQuery = jQuery;
                    DDelivery.initStep2();
                }

                if(typeof(ymaps)=='undefined') {
                    this.utils.requireScript('//api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU');
                }

            }else{
                this.initStep2();
            }
        },
        initStep2: function(){
            this.jQuery.post( "ajax.php", function( data ) {
                DDelivery.jQuery( DDelivery.htmlObject ).html( data.html );
                var mapObject = DDelivery.jQuery('.map-canvas', DDelivery.htmlObject)[0];
                ymaps.ready(function(){
                    DDelivery.map = new ymaps.Map(mapObject, {
                        center: [55.76, 37.64],
                        zoom: 7
                    });

                    DDelivery.map.controls.add('zoomControl', { top: 65, left: 10 });
                });

            }, 'json');

            this.utils.requireStyle(this.staticPath+'css/screen.css');

            this.utils.requireScript(this.staticPath+'js/jquery.mCustomScrollbar.concat.min.js');
            this.utils.requireScript(this.staticPath+'js/jquery.custom-radio-checkbox.js');
            this.utils.requireScript(this.staticPath+'js/start.js');
        },
        mapAction:{
            showLeftPanel: function() {
                //$('.map-popup__main__right__btn').on('click', function () {
                jQuery('.map-popup__main__right').toggleClass('map-popup__main__right_open');
                jQuery('.map-popup__info').toggleClass('wide');
                //});
            }
        },
        utils:{
            requireScript: function(url, onload) {
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = url;
                if(typeof(onload) == "function"){
                    script.onload = onload;
                }
                document.childNodes[0].appendChild(script);
            },
            requireStyleInclude: [],
            requireStyle: function(url, onload) {
                if(this.requireStyleInclude.indexOf(url) != -1) {
                    if(typeof(onload) == "function"){
                        onload();
                    }
                    return;
                }
                var link = document.createElement('link');
                link.type = 'text/css';
                link.rel = 'stylesheet';
                link.href = url;
                if(typeof(onload) == "function"){
                    link.onload = onload;
                }
                document.childNodes[0].appendChild(link);
            }
        }
    };
    DDelivery.init('ddelivery', 'ajax.php', '',  {});
</script>
