var DDeliveryIframe = {
    delivery: function(componentUrl, staticUrl, params){
        var Delivery;
        Delivery = {
            includeScripts: false,
            init: function(params) {
                this.ajaxPage({});
            },
            ajaxPage: function(data){
                data.action = 'html';
                $('#ddelivery').html('<img class="loader" src="'+staticUrl+'/img/ajax_loader.gif"/>');
                $.post( componentUrl, data, function( data ) {
                    $( '#ddelivery' ).html(data.html );

                    Delivery.ajaxRequestInit(data);
                }, 'json');
            },
            ajaxData: function(data, callBack) {
                $.post( componentUrl, data, callBack, 'json');
            },

            citySelectEvent: function(){
                var title = $(this)[0].innerText.trim().replace('\n', ', ');
                $('.delivery-place__title input').val('').attr('title', title).blur();

                $('.delivery-place__drop li a').removeClass('active');
                var cityId = $(this).addClass('active').data('id');
                $('input[name=ddelivery_city]').val(cityId);

                $('.delivery-place__drop').slideUp(function () {
                    $('.map-popup__main').removeClass('show-drop-2');
                });
                return false;
            },
            ajaxRequestInit: function(data) {
                var radio = $('.map-popup__main__delivery input[type="radio"]');
                if(radio.length > 0){
                    radio.Custom({
                        customStyleClass: 'radio',
                        customHeight: '20'
                    });
                    var mapPopupTableTr = $('.map-popup__main__delivery table tr');
                    mapPopupTableTr.hover(function () {
                        $(this).addClass('hover');
                    }, function () {
                        $(this).removeClass('hover');
                    });
                    mapPopupTableTr.on('click', function (e) {
                        e.preventDefault();
                        $(this).find('input[type="radio"]').prop('checked', true).change();
                    });
                }

                if($('.map-canvas').length > 0){
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
                }).keyup(function(){
                    var title = $(this).val();
                    var input = $(this);
                    if(title.length >= 3){
                        $('.delivery-place__drop_i ul.search').html('<img class="loader_search" src="'+staticUrl+'/img/ajax_loader.gif"/>');
                        Delivery.ajaxData({action: 'searchCity', name: title}, function(data){
                            if(data.request.name == input.val()){
                                $('.delivery-place__drop_i .pop').hide();
                                $('.delivery-place__drop_i .search').show();
                                $('.delivery-place__drop_i ul.search').html(data.html);
                            }
                            $('.delivery-place__drop .search li a').on('click', Delivery.citySelectEvent);
                        });
                    }
                });

                $('.delivery-place__title > span').on('click', function () {
                    $(this).parent().parent().find('.delivery-place__drop').slideToggle(function () {
                        $('.map-popup__main').toggleClass('show-drop-2');
                        if ($('.no-touch').length) {
                            $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
                        }
                    });
                });
                $('.delivery-place__drop li a').on('click', Delivery.citySelectEvent);

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
                $('.map-popup__main__delivery__next a').click(Delivery.typeFormSubmit);
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

                var radio = $('input[type="radio"]:checked').val();
                if(radio) {
                    Delivery.ajaxPage({
                        type: radio,
                        city_id: $('input[name=ddelivery_city]').val()
                    });
                }

            },

            map: {
                yamap: null,
                init: function(data) {
                    var mapObject = $('.map-canvas');
                    if(mapObject.length != 1)
                        return;
                    ymaps.ready(function(){

                        ymaps.geocode($('.delivery-place__title input').attr('title'), {results: 1})
                            .then(function (res) {
                                // Выбираем первый результат геокодирования.
                                var firstGeoObject = res.geoObjects.get(0);
                                // Область видимости геообъекта.
                                var bounds = firstGeoObject.properties.get('boundedBy');
                                // Получаем где отрисовать карту
                                var centerAndZoom = ymaps.util.bounds.getCenterAndZoom(bounds, [mapObject.width(), mapObject.height()]);

                                Delivery.map.yamap = new ymaps.Map(mapObject[0], {
                                    center: centerAndZoom.center,
                                    zoom: centerAndZoom.zoom,
                                    behaviors: ['default', 'scrollZoom']
                                });

                                var yamap = Delivery.map.yamap;
                                // дебаг
                                mapDbg = yamap;
                                yamap.controls.add('zoomControl', { top: 65, left: 10 });


                                yamap.events.add('boundschange', function () {
                                    var bound = yamap.getBounds();
                                });

                                // Кластер

                                var clusterer = new ymaps.Clusterer({
                                    preset: 'twirl#invertedVioletClusterIcons',
                                    /**
                                     * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
                                     */
                                    groupByCoordinates: false,
                                    /**
                                     * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
                                     * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Cluster.xml
                                     */
                                    clusterDisableClickZoom: true
                                });

                                var geoObjects = [];
                                var point;

                                for(var pointKey in data.points) {
                                    point = data.points[pointKey];
                                    var myPlacemark =new ymaps.Placemark([point.latitude,point.longitude], {
                                            hintContent: "Хинт метки"
                                        },{
                                            iconLayout: 'default#image',
                                            iconImageHref: staticUrl+'/img/point_75x75.png',
                                            iconImageSize: [50, 50],
                                            // Смещение левого верхнего угла иконки относительно
                                            // её "ножки" (точки привязки).
                                            iconImageOffset: [-22, -46]
                                        }
                                    );
                                    geoObjects.push(myPlacemark);
                                    //yamap.geoObjects.add(myPlacemark);
                                }

                                clusterer.add(geoObjects);
                                yamap.geoObjects.add(clusterer);

                                clusterer.events
                                    // Можно слушать сразу несколько событий, указывая их имена в массиве.
                                    .add(['mouseenter', 'mouseleave'], function (e) {
                                        var target = e.get('target'), // Геообъект - источник события.
                                            eType = e.get('type'), // Тип события.
                                            zIndex = Number(eType === 'mouseenter') * 1000; // 1000 или 0 в зависимости от типа события.

                                        target.options.set('zIndex', zIndex);
                                    })
                                    .add('click', function(e){
                                        var target = e.get('target');
                                        // Вернет все геобъекты
                                        target.properties.get('geoObjects');

                                    });


                            });
                    });
                    // Инпут поиска
                    $('.map__search input[type=text]').keyup(Delivery.map.citySearch);
                    $('.map__search input[type=submit]').click(function(){
                        Delivery.map.citySearch();
                        return false;
                    });

                },
                citySearch: function() {
                    var input = $('.map__search input[type=text]')[0];
                    if(input.value.length < 3)
                        return;
                    ymaps.geocode(input.value, {results: 5}).then(function (res) {
                        if(res.metaData.geocoder.request == input.value) {
                            var html = '';
                            var boundList = [];
                            for(var i=0; i< res.geoObjects.getLength(); i++ ) {
                                var geoObject = res.geoObjects.get(i);
                                html += '<a data-id="'+i+'" href="javascript:void(0)">'+geoObject.properties.get('text')+'</a><br>';
                                boundList.push(geoObject.properties.get('boundedBy'));
                            }
                            var dropDown = $('div.map__search_dropdown');
                            dropDown.html(html).slideDown(300);
                            $('a', dropDown).click(function(){
                                Delivery.map.yamap.setBounds(boundList[parseInt($(this).data('id'))], {
                                    checkZoomRange: true // проверяем наличие тайлов на данном масштабе.
                                });
                                dropDown.slideUp(300);
                            });
                            dropDown[0].bound = boundList;
                        }
                    });
                }
            }
        };
        Delivery.init(params);
        return Delivery;
    }
};
