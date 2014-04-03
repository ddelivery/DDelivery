if(typeof(DDelivery) == 'undefined')
var DDelivery = {
    delivery: function(objectId, componentUrl, staticPath, params){
        var $;
        var Delivery;
        Delivery = {
            componentUrl: "",
            staticPath: "",
            htmlObject: null,
            includeScripts: false,
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
                                jQuery.noConflict();
                                $ = jQuery;
                                Delivery.initStep2();
                            }
                        );
                    }else{
                        Delivery.initStep2();
                    }

                    if(typeof(ymaps)=='undefined') {

                        this.utils.requireScript('//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU');
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
                $.post( "ajax.php", data, function( data ) {
                    $( Delivery.htmlObject ).html(data.html );
                    Delivery.ajaxRequestInit(data);

                    Delivery.utils.requireScript(Delivery.staticPath+'js/start.js');
                }, 'json');
            },
            ajaxRequestInit: function(data){

                var radio = $('.map-popup__main__delivery input[type="radio"]', Delivery.htmlObject);
                if(radio.length > 0){
                    radio.Custom({
                        customStyleClass: 'radio',
                        customHeight: '20'
                    });
                    $('.map-popup__main__delivery table tr', Delivery.htmlObject).hover(function () {
                        $(this).addClass('hover');
                    }, function () {
                        $(this).removeClass('hover');
                    });

                    $('.map-popup__main__delivery table tr', Delivery.htmlObject).on('click', function (e) {
                        e.preventDefault();
                        $(this).find('input[type="radio"]').prop('checked', true).change();
                    });
                }

                if($('.map-canvas', Delivery.htmlObject).length > 0){
                    Delivery.map.init(data);
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
                    var title = $(this)[0].innerText.trim().replace('\n', ', ');
                    $('.delivery-place__title input', Delivery.htmlObject).val('').attr('title', title).blur();

                    $('.delivery-place__drop li a').removeClass('active');
                    var cityId = $(this).addClass('active').data('id');
                    $('input[name=ddelivery_city]', Delivery.htmlObject).val(cityId);

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

                // Ссылки
                $('.map-popup__main__delivery__next a', Delivery.htmlObject).click(Delivery.typeFormSubmit);
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
                        Delivery.utils.loadScriptProcess--;
                        if(Delivery.utils.loadScriptProcess == 0) {
                            var event;
                            do{
                                event = Delivery.utils.onLoadEvent.splice(0,1);
                                if(event.length > 0)
                                    event[0]();
                            } while(event.length > 0);
                        }
                    };
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(script, s);
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
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(link, s);

                }
            },

            // события

            typeFormSubmit: function(){

                var radio = $('input[type="radio"]:checked', this.htmlObject).val();
                if(radio) {
                    Delivery.ajax({
                        type: radio,
                        city_id: $('input[name=ddelivery_city]', Delivery.htmlObject).val()
                    });
                }

            },

            map: {
                yamap: null,
                init: function(data) {
                    var mapObject = $('.map-canvas', Delivery.htmlObject);
                    if(mapObject.length != 1)
                        return;
                    ymaps.ready(function(){

                        Delivery.map.yamap = new ymaps.Map(mapObject[0], {
                            center: [55.76, 37.64],
                            zoom: 7,
                            behaviors: ['default', 'scrollZoom']
                        });
                        var yamap = Delivery.map.yamap;

                        yamap.controls.add('zoomControl', { top: 65, left: 10 });

                        ymaps.geocode($('.delivery-place__title input').attr('title'), {results: 1})
                            .then(function (res) {
                                // Выбираем первый результат геокодирования.
                                var firstGeoObject = res.geoObjects.get(0);
                                // Область видимости геообъекта.
                                var bounds = firstGeoObject.properties.get('boundedBy');

                                // Масштабируем карту на область видимости геообъекта.
                                yamap.setBounds(bounds, {
                                    checkZoomRange: true // проверяем наличие тайлов на данном масштабе.
                                });
                            });

                    });
                }
            }
        };
        Delivery.init(objectId, componentUrl, staticPath, params);
        return Delivery;
    }
};
