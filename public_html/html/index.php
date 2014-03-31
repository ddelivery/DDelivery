<div id="ddelivery"></div>
<div style="width: 100px; height: 300px"></div>

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
            this.utils.requireStyle(this.staticPath+'css/screen.css');

            this.utils.requireScript(this.staticPath+'js/jquery.mCustomScrollbar.concat.min.js');
            this.utils.requireScript(this.staticPath+'js/jquery.custom-radio-checkbox.js');
            this.utils.requireScript(this.staticPath+'js/jquery.maskedinput.js');
            this.utils.requireScript(this.staticPath+'js/jquery.formtips.js');

            this.ajax({});
        },
        ajax: function(data){
            this.jQuery.post( "ajax.php", data, function( data ) {
                DDelivery.jQuery( DDelivery.htmlObject ).html(data.html );
                DDelivery.ajaxRequestInit();

                DDelivery.utils.requireScript(DDelivery.staticPath+'js/start.js');
            }, 'json');
        },
        ajaxRequestInit: function(){
            var $ = this.jQuery;

            var radio = $('.map-popup__main__delivery input[type="radio"]', DDelivery.htmlObject);
            if(radio.length > 0){
                radio.Custom({
                    customStyleClass: 'radio',
                    customHeight: '20'
                });
                $('.map-popup__main__delivery table tr', DDelivery.htmlObject).hover(function () {
                    $(this).addClass('hover');
                }, function () {
                    $(this).removeClass('hover');
                });

                $('.map-popup__main__delivery table tr', DDelivery.htmlObject).on('click', function (e) {
                    e.preventDefault();
                    $(this).find('input[type="radio"]').prop('checked', true).change();
                });
            }

            var mapObject = $('.map-canvas', DDelivery.htmlObject)[0];
            if(mapObject){
                ymaps.ready(function(){
                    DDelivery.map = new ymaps.Map(mapObject, {
                        center: [55.76, 37.64],
                        zoom: 7
                    });

                    DDelivery.map.controls.add('zoomControl', { top: 65, left: 10 });
                });
            }

            // Город

            $('.delivery-place__title > input[title]').formtips().on('focus', function () {
                $(this).parent().parent().find('.delivery-place__drop').slideDown(function () {
                    $('.map-popup__main').addClass('show-drop-2');
                    if ($('.no-touch').length) {
                        $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
                    }
                });
            });

            $('.delivery-place__title > span').on('click', function () {
                $(this).parent().parent().find('.delivery-place__drop').slideToggle(function () {
                    $('.map-popup__main').toggleClass('show-drop-2');
                    if ($('.no-touch').length) {
                        $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
                    }
                });
            });
            $('.delivery-place__drop li a').click(function(){
                var title = $(this)[0].innerText.replace('\n', ', ');
                $('.delivery-place__title input', DDelivery.htmlObject).val('').attr('title', title).blur();

                $('.delivery-place__drop li a').removeClass('active');
                var cityId = $(this).addClass('active').data('id');
                $('input[name=ddelivery_city]', DDelivery.htmlObject).val(cityId);

                $('.delivery-place__drop').slideUp(function () {
                    $('.map-popup__main').removeClass('show-drop-2');
                });
                return false;
            });

            $('.map-popup__main__right__btn').on('click', function () {
                $('.map-popup__main__right').toggleClass('map-popup__main__right_open');
                $('.map-popup__info').toggleClass('wide');
            });

            $('body').on('click', function (e) {
                if (!$(e.target).closest('.delivery-place__drop').length && !$(e.target).closest('.delivery-place__title').length) {
                    $('.delivery-place__drop').slideUp(function () {
                        $('.map-popup__main').removeClass('show-drop-2');
                    });
                }
                if (!$(e.target).closest('.delivery-type__drop').length && !$(e.target).closest('.delivery-type__title').length) {
                    $('.delivery-type__drop').slideUp(function () {
                        $('.map-popup__main').removeClass('show-drop-1');
                    });
                }
            });

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
            loadScriptProcess: 0,
            onLoadEvent: [],
            requireStyleInclude: [],
            // onload - функция которая будет вызвана после загрузки ВСЕХ requireScript скриптов
            requireScript: function(url, onload) {
                this.loadScriptProcess++;
                if(typeof(onload) == "function") {
                    this.onLoadEvent.push(onload);
                }
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = url;
                script.onload = function(){
                    DDelivery.utils.loadScriptProcess--;
                    if(DDelivery.utils.loadScriptProcess == 0) {
                        var event;
                        do{
                            event = DDelivery.utils.onLoadEvent.splice(0,1);
                            if(event.length > 0)
                                event[0]();
                        } while(event.length > 0);
                    }
                };
                document.childNodes[0].appendChild(script);
            },
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
        },

        // события

        typeFormSubmit: function(){
            var $ = this.jQuery;

            var radio = $('input[type="radio"]:checked', this.htmlObject).val();
            if(radio) {
                DDelivery.ajax({
                    type: radio,
                    city_id: $('input[name=ddelivery_city]', DDelivery.htmlObject).val()
                });
            }

        }

    };
    DDelivery.init('ddelivery', 'ajax.php', '',  {});
</script>
