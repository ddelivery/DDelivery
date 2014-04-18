/**
 * Created by DnAp on 09.04.14.
 */
var Map;
Map = (function () {
    var yamap;
    var mapObject;
    var renderGeoObject;
    var points = [];
    var current_points = false;
    var current_point = false;
    var clusterer;
    var filter = {
        cash: true,
        card: true,
        time: false,
        has_fitting_room: false,
        type1: true,
        type2: true,
        hideCompany: []
    };

    var staticUrl;

    return {
        init: function (data) {
            staticUrl = DDeliveryIframe.staticUrl;
            points = data.points;
            mapObject = $('.map-canvas');
            if (mapObject.length != 1)
                return;
            var th = this;

            // Ожидаем загрузки объекта карты
            ymaps.ready(function () {
                // Получаем пользовательское местоположение и вызываем дальнейшую инициализацию
                ymaps.geocode($('.delivery-place__title input').attr('title'), {results: 1})
                    .then(function (res) {
                        // Выбираем первый результат геокодирования.
                        renderGeoObject = res.geoObjects.get(0);
                        th.render();
                        th.event();
                    });
            });

            // Инпут поиска
            $('.map__search input[type=text]').keyup(this.citySearch);
            $('.map__search input[type=submit]').click(function () {
                th.citySearch();
                return false;
            });

        },

        render: function () {

            // Область видимости геообъекта.
            var bounds = renderGeoObject.properties.get('boundedBy');
            // Получаем где отрисовать карту
            var centerAndZoom = ymaps.util.bounds.getCenterAndZoom(bounds, [mapObject.width(), mapObject.height()]);

            yamap = new ymaps.Map(mapObject[0], {
                center: centerAndZoom.center,
                zoom: centerAndZoom.zoom,
                behaviors: ['default', 'scrollZoom']
            }, {
                maxZoom: 17
            });

            // дебаг
            mapDbg = yamap;
            yamap.controls.add('zoomControl', { top: 65, left: 10 });


            yamap.events.add('boundschange', function () {
                var bound = yamap.getBounds();
            });

            // Кластер

            clusterer = new ymaps.Clusterer({
                preset: 'twirl#invertedVioletClusterIcons',
                /**
                 * Ставим true, если хотим кластеризовать только точки с одинаковыми координатами.
                 */
                groupByCoordinates: false,
                openBalloonOnClick: false,
                /**
                 * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
                 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Cluster.xml
                 */
                clusterDisableClickZoom: true,
                gridSize: 100,
                synchAdd: true // Добавлять объекты на карту сразу, може тупить на медленных пк
            });

            var geoObjects = [];
            var point;

            for (var pointKey in points) {
                point = points[pointKey];
                point.display = true;
                //console.log(point);
                point.placemark = new ymaps.Placemark([point.latitude, point.longitude], {
                        hintContent: point.address,
                        point: point
                    }, {
                        iconLayout: 'default#image',
                        iconImageHref: staticUrl + '/img/point_75x75.png',
                        iconImageSize: [50, 50],
                        iconImageOffset: [-22, -46]
                    }
                );

                geoObjects.push(point.placemark);
                //yamap.geoObjects.add(myPlacemark);
            }

            clusterer.add(geoObjects);
            yamap.geoObjects.add(clusterer);
            cl = clusterer;
            clusterer.events
                // Слушаем события кластера
                .add(['mouseenter', 'mouseleave'], function (e) {
                    var target = e.get('target'), // Геообъект - источник события.
                        eType = e.get('type'), // Тип события.
                        zIndex = Number(eType === 'mouseenter') * 1000; // 1000 или 0 в зависимости от типа события.

                    target.options.set('zIndex', zIndex);
                })
                .add('click', function (e) {
                    var target = e.get('target');
                    t = target;
                    // Вернет все геобъекты
                    var geoObjects = target.properties.get('geoObjects');
                    if (geoObjects) { // Клик по кластеру
                        var bound = [
                            [99, 99],
                            [0, 0]
                        ];
                        for (var geoKey in geoObjects) {

                            var coord = geoObjects[geoKey].geometry.getCoordinates();
                            if (bound[1][0] < coord[0])
                                bound[1][0] = coord[0];
                            if (bound[1][1] < coord[1])
                                bound[1][1] = coord[1];
                            if (bound[0][0] > coord[0])
                                bound[0][0] = coord[0];
                            if (bound[0][1] > coord[1])
                                bound[0][1] = coord[1];
                        }
                        // Точки эквивалентны в допустимой погрешности и зумить есть куда
                        if (!ymaps.util.math.areEqual(bound[0], bound[1], 0.0002) && yamap.getZoom() != yamap.options.get('maxZoom')) {
                            yamap.setBounds(bound, {duration: 400});
                        } else {
                            yamap.setBounds(bound, {duration: 400});
                            var myPoints = [];
                            for (var geoKey in geoObjects) {
                                myPoints.push(geoObjects[geoKey].properties.get('point'));
                            }
                            Map.renderInfo(myPoints[0], myPoints);
                        }
                    } else {
                        Map.renderInfo(target.properties.get('point'));
                    }

                });
        },
        // Удаляет с карты лишние точки
        filterPoints: function () {
            var pointsRemove = [];
            var pointsAdd = [];
            var point, display;
            // В рамках функции красивей решается
            var isDisplayPoint = function(point) {
                // Если не удовлетворяет одному из вариантов
                if (!((filter.card && point.is_card) || (filter.cash && point.is_cash))) {
                    return false;
                }

                if(point.type == 1 && !filter.type1) {
                    return false;
                }
                if(point.type == 2 && !filter.type2) {
                    return false;
                }

                if (filter.time && point.schedule) {
                    return false;
                }
                if (filter.has_fitting_room && !point.has_fitting_room) {
                    return false;
                }
                if(filter.hideCompany.indexOf(point.company_id) != -1){
                    return false;
                }
                return true;
            };

            for (var pointKey in points) {
                point = points[pointKey];
                display = isDisplayPoint(point);
                if (point.display != display) {
                    if (display) { // Скрыта, пказать
                        pointsAdd.push(point.placemark);
                    } else {
                        pointsRemove.push(point.placemark);
                    }
                    point.display = display;
                }
            }

            if (pointsRemove.length) {
                clusterer.remove(pointsRemove);
            }
            if (pointsAdd.length) {
                clusterer.add(pointsAdd);
            }
        },
        event: function () {
            $('.map-popup__info__close').click(function () {
                $('.map-popup__info').fadeOut();
                $('.map-popup__main__right .places').removeClass('info-open');
                $('.map-popup__main__right .places a').removeClass('active').removeClass('hasinfo');
                current_points = [];
            });
            $('.map-popup__main__right__btn').on('click', function () {
                $('.map-popup__main__right').toggleClass('map-popup__main__right_open');
                $('.map-popup__info').toggleClass('wide');
            });

            $('.filters a').click(function () {
                var $th = $(this);
                $th.toggleClass('border');
                var filterName = $th.data('filter');
                filter[filterName] = $th.hasClass('border');
                if(filter[filterName]) {
                    $('.filters a[data-filter='+filterName+']').addClass('border');
                }else{
                    $('.filters a[data-filter='+filterName+']').removeClass('border');
                }
                Map.filterPoints();
            });

            $('.map-popup__main__right .places a').click(function(){
                if(current_points.length > 0) {
                    var id = parseInt($(this).data('id'));
                    if(current_point.company_id != parseInt($(this).data('id'))) {
                        for(var i =0 ; i < current_points.length; i++) {
                            if(current_points[i].company_id == id) {
                                Map.renderInfo(current_points[i] , current_points);
                                break;
                            }
                        }
                    }
                }else{
                    var check = $(this).hasClass('border');
                    if(check) {
                        $(this).removeClass('border').addClass('hasinfo');
                        filter.hideCompany.push(parseInt($(this).data('id')));
                    }else{
                        $(this).addClass('border').removeClass('hasinfo');
                        filter.hideCompany.splice(filter.hideCompany.indexOf(parseInt($(this).data('id'))), 1);
                    }
                    Map.filterPoints();
                }
            });

            $('.map-popup__info__more__btn').on('click', function (e) {
                e.preventDefault();
                var el = $(this).toggleClass('open');
                el.closest('.map-popup__info__more').find('.map-popup__info__more__text').slideToggle(function () {
                    if ($('.no-touch').length) {
                        $(this).mCustomScrollbar('update');
                    }
                });
            });

        },
        renderInfo: function (point, points) {

            $('.map-popup__main__right .places').addClass('info-open');
            $('.map-popup__main__right .places a').removeClass('active').removeClass('hasinfo');

            cp = points;
            if(!points){
                points = [];
            }

            current_points = points;
            current_point = point;

            if(points.length > 1){
                $('.map-popup__info__title .more').show();
                for(var i=0;i<points.length;i++){
                    $('.map-popup__main__right .places a[data-id='+points[i].company_id+']').addClass('hasinfo');
                }
                $('.map-popup__main__right .places a[data-id='+point.company_id+']').addClass('active');
            }else{
                $('.map-popup__info__title .more').hide();
                $('.map-popup__main__right .places a[data-id='+point.company_id+']').addClass('active').addClass('hasinfo');
            }

            if (!point.name) {
                point.name = point.company + ' #' + point._id;
            }
            $('.map-popup__info__title h2').html(point.name);
            $('.map-popup__info__table .rub').html('<img src="'+DDeliveryIframe.staticUrl+'/img/ajax_loader_min.gif"/> ');
            var payType = [];
            if (point.is_cash) {
                payType.push('Наличными');
            }
            if (point.is_card) {
                payType.push('Банковскими картами');
            }
            if (payType.length == 0) {
                payType.push('Предоплата');
            }
            $('.map-popup__info__table .payType').html(payType.join('<br>'));

            $('.map-popup__info__table .day').hide();

            // Подробнее
            var more = $('.map-popup__info__more__text_i table');
            $('.address', more).html(point.address);
            $('.schedule', more).html(point.schedule.replace(/\n/g, "<br>"));
            $('.company', more).html(point.company);
            $('.more', more).html('');

            $('.map-popup__info').fadeIn();

            DDeliveryIframe.ajaxData(
                {action: 'mapGetPoint', id: point._id},
                function(data) {
                    //console.log(data);
                }
            );
        },

        citySearch: function () {
            var input = $('.map__search input[type=text]')[0];
            if (input.value.length < 3)
                return;
            ymaps.geocode(input.value, {results: 5}).then(function (res) {
                if (res.metaData.geocoder.request == input.value) {
                    var html = '';
                    var boundList = [];
                    for (var i = 0; i < res.geoObjects.getLength(); i++) {
                        var geoObject = res.geoObjects.get(i);
                        html += '<a data-id="' + i + '" href="javascript:void(0)">' + geoObject.properties.get('text') + '</a><br>';
                        boundList.push(geoObject.properties.get('boundedBy'));
                    }
                    var dropDown = $('div.map__search_dropdown');
                    dropDown.html(html).slideDown(300);
                    $('a', dropDown).click(function () {
                        yamap.setBounds(boundList[parseInt($(this).data('id'))], {
                            checkZoomRange: true // проверяем наличие тайлов на данном масштабе.
                        });
                        dropDown.slideUp(300);
                    });
                    dropDown[0].bound = boundList;
                }
            });
        }
    };
})();