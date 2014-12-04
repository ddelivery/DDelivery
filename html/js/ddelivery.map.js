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
    var currentPointExtendData = false;
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

    var initPoint = function (point) {
        point.display = true;
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
        return point;
    };

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
            for (var pointKey in points) {
                initPoint(points[pointKey]);
                geoObjects.push(points[pointKey].placemark);
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
                    // Вернет все геобъекты
                    var geoObjects = target.properties.get('geoObjects');
                    if (geoObjects) { // Клик по кластеру
                        var bound = [
                            [500, 500],
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
                        // Вычисляем центр и зум которые нам нужны, отступ 20 - первое число которое указал и оно нормально работает
                        //var centerAndZoom = ymaps.util.bounds.getCenterAndZoom(bound, yamap.container.getSize(), ymaps.projection.wgs84Mercator, {margin:20});


                        // Отсупы с всех сторон
                        var correctSize = [35, $('.map-popup__main__right').width()+10, 25, 35]; // top, right, bottom, left
                        var displayMapSize = yamap.container.getSize();
                        displayMapSize = [
                            displayMapSize[0] - correctSize[1] - correctSize[3],
                            displayMapSize[1] - correctSize[0] - correctSize[2]];

                        // Получаем зум для неперекрытого квадрата
                        var centerAndZoomFake = ymaps.util.bounds.getCenterAndZoom(bound, displayMapSize,
                            ymaps.projection.wgs84Mercator);

                        // Теперь двигаем видимый центр в реальный центр
                        var projection = yamap.options.get('projection');
                        var pixelCenter = projection.toGlobalPixels( centerAndZoomFake.center, centerAndZoomFake.zoom );
                        centerAndZoom = {center:[], zoom:centerAndZoomFake.zoom};
                        centerAndZoom.center = projection.fromGlobalPixels(
                            [
                                pixelCenter[0] - correctSize[3]/2 + correctSize[1]/2,
                                pixelCenter[1] - correctSize[2]/2 + correctSize[0]/2
                            ],
                            centerAndZoomFake.zoom
                        );

                        // Точки эквивалентны в допустимой погрешности и зумить есть куда
                        if (!ymaps.util.math.areEqual(bound[0], bound[1], 0.0002) && yamap.getZoom() != yamap.options.get('maxZoom')) {
                            yamap.setCenter(centerAndZoom.center, centerAndZoom.zoom, {duration: 400});
                            //yamap.setBounds(bound, {duration: 400});
                        } else {
                            //yamap.setBounds(bound, {duration: 400});

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
            var isDisplayPoint = function (point) {
                // Если не удовлетворяет одному из вариантов
                if (!((filter.card && point.is_card) || (filter.cash && point.is_cash))) {
                    return false;
                }

                if (point.type == 1 && !filter.type1) {
                    return false;
                }
                if (point.type == 2 && !filter.type2) {
                    return false;
                }

                if (filter.time && point.schedule) {
                    return false;
                }
                if (filter.has_fitting_room && !point.has_fitting_room) {
                    return false;
                }

                if (filter.hideCompany.indexOf(point.company_id) != -1) {
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
                if (filter[filterName]) {
                    $('.filters a[data-filter=' + filterName + ']').addClass('border');
                } else {
                    $('.filters a[data-filter=' + filterName + ']').removeClass('border');
                }
                Map.filterPoints();
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

            $('.map-popup__info__btn a').click(function(){
                if(!currentPointExtendData)
                    return;
                var point = $.extend({}, current_point, currentPointExtendData);
                point.placemark = undefined;
                DDeliveryIframe.postMessage('mapPointChange', {point: point});
                if(typeof(params) != 'undefined' && typeof(params.displayContactForm) == 'boolean' && !params.displayContactForm){
                    return;
                }
                DDeliveryIframe.ajaxPage({action:'contactForm', point: current_point._id, type:1, custom: current_point.is_custom ? 1 : ''});
            });

            $(window).on('ddeliveryCityPlace', function (e, city) {
                Map.changeCity(city.id, city.title);
            });
            this.placeEvent();
        },

        changeCity: function(cityId, cityFullName) {
            ymaps.geocode(cityFullName, {results: 1})
                .then(function (res) {
                    // Выбираем первый результат геокодирования.
                    renderGeoObject = res.geoObjects.get(0);
                    yamap.setBounds(renderGeoObject.properties.get('boundedBy'));
                });

            $('.map-popup__main__right .places').html('').addClass('info-open');

            $('.delivery-type__drop ul').hide();
            $('.map-popup .delivery-type__drop p.loader_center').show();

            DDeliveryIframe.ajaxData({action: 'mapDataOnly', city_id: cityId, city_alias:cityFullName}, function (data) {
                Map.renderData(data);
            });

            // Удаляем старые поинты, какраз пока ждем ответа ajax
            points = [];
            clusterer.removeAll();
        },

        placeEvent: function () {
            $('.map-popup__main__right .places a').click(function () {
                if (current_points.length > 0) {
                    if(current_points.length == 1){
                        return;
                    }

                    var id = parseInt($(this).data('id'));
                    if (current_point.company_id != parseInt($(this).data('id'))) {
                        for (var i = 0; i < current_points.length; i++) {
                            if (current_points[i].company_id == id) {
                                Map.renderInfo(current_points[i], current_points);
                                break;
                            }
                        }
                    }
                } else {
                    var check = $(this).hasClass('border');
                    if (check) {
                        $(this).removeClass('border').addClass('hasinfo');
                        filter.hideCompany.push($(this).data('id'));
                    } else {
                        $(this).addClass('border').removeClass('hasinfo');
                        filter.hideCompany.splice(filter.hideCompany.indexOf($(this).data('id')), 1);
                    }
                    Map.filterPoints();
                }
            });
        },
        // Рендерим то что к нам пришло по ajax
        renderData: function (data) {

            $('.map-popup__main__right .places').removeClass('info-open').html(data.html);

            var geoObjects = [];
            points = data.points;
            if (points.length == 0) {
                DDeliveryIframe.ajaxPage({});
                return;
            }

            for (var pointKey in points) {
                initPoint(points[pointKey]);
                geoObjects.push(points[pointKey].placemark);
            }
            clusterer.add(geoObjects);
            filter.hideCompany = [];
            Map.filterPoints(); // Фильтр покажет все точки
            Map.placeEvent();

            if(typeof(data.headerData) != 'undefined') {
                for(var key in data.headerData ) {
                    var headerData = data.headerData[key];
                    $('.delivery-type__drop_'+key+' .price span').html(headerData.minPrice);
                    $('.delivery-type__drop_'+key+' .date strong').html(headerData.minTime);
                    $('.delivery-type__drop_'+key+' .date span').html(headerData.timeStr);
                }
            }
            $('.delivery-type__drop ul').show();
            $('.map-popup .delivery-type__drop p.loader_center').hide();

        },
        renderInfo: function (point, points) {
            currentPointExtendData = false;
            $('.map-popup__main__right .places').addClass('info-open');
            $('.map-popup__main__right .places a').removeClass('active').removeClass('hasinfo');

            //cp = points;
            if (!points) {
                points = [];
            }

            current_points = points;
            current_point = point;

            if (points.length > 1) {
                $('.map-popup__info__title .more').show();
                for (var i = 0; i < points.length; i++) {
                    $('.map-popup__main__right .places a[data-id=' + points[i].company_id + ']').addClass('hasinfo');
                }
                $('.map-popup__main__right .places a[data-id=' + point.company_id + ']').addClass('active');
            } else {
                $('.map-popup__info__title .more').hide();
                $('.map-popup__main__right .places a[data-id=' + point.company_id + ']').addClass('active').addClass('hasinfo');
            }

            if (!point.name) {
                point.name = point.company + ' #' + point._id;
            }
            $('.map-popup__info__title h2').html(point.name);
            $('.map-popup__info__table .rub').html('<img src="' + DDeliveryIframe.staticUrl + 'img/ajax_loader_min.gif"/> ');
            var payType = [];
            if (point.is_cash) {
                payType.push( ddCaptionConfig.caption3);
            }
            if (point.is_card) {
                payType.push(ddCaptionConfig.caption4);
            }
            if (payType.length == 0) {
                payType.push(ddCaptionConfig.caption5);
            }
            $('.map-popup__info__table .payType').html(payType.join('<br>'));
            $('.map-popup__info__table .type').html(point.type == 1 ? ddCaptionConfig.caption1 :  ddCaptionConfig.caption2);

            $('.map-popup__info__table .day').hide();

            // Подробнее
            var more = $('.map-popup__info__more__text_i table');
            $('.address', more).html(point.address);
            $('.schedule', more).html(point.schedule.replace(/\n/g, "<br>"));
            $('.company', more).html(point.company);
            $('.more', more).html('');

            $('.map-popup__info').fadeIn();

            DDeliveryIframe.ajaxData(
                {action: 'mapGetPoint', id: point._id, 'custom': point.is_custom ? 1 : ''},
                function (data) {
                    if(typeof(data.length) == 'undefined') { // object
                        currentPointExtendData = data.point;
                        $('.map-popup__info__table .rub').html(data.point.total_price);
                        var day = $('.map-popup__info__table .day').show();
                        $('strong', day).html(data.point.delivery_time_min);
                        $('span', day).html(data.point.delivery_time_min_str);
                        if(data.point.indoor_place)
                            $('.address', more).html(point.address + ', ' + data.point.indoor_place);


                        $('.schedule', more).html(data.point.schedule.replace(/\n/g, "<br>"));

                        var description = (data.point.description_in + '<br/>' + data.point.description_out).replace(/\n/g, '<br/>');
                        $('.more', more).html(description);

                        if(!data.point.metro) {
                            $('.metro_row', more).hide();
                        }else{
                            $('.metro_row', more).show();
                            $('.metro_row .col2', more).html(data.point.metro);
                        }

                    }else{
                        // Если ошибка что-то нужно делать
                    }
                }
            );
        },

        citySearch: function () {
            var input = $('.map__search input[type=text]')[0];
            if (input.value.length < 3)
                return;

            // Область видимости геообъекта.
            var bounds = renderGeoObject.properties.get('boundedBy');
            var options = {
                results: 5,
                boundedBy: bounds,
                strictBounds: true
            };
            ymaps.geocode(input.value, options).then(function (res) {
                if (res.metaData.geocoder.request == input.value) {
                    var html = '';
                    var geoObjectList = [];
                    for (var i = 0; i < res.geoObjects.getLength(); i++) {
                        var geoObject = res.geoObjects.get(i);
                        html += '<a data-id="' + i + '" href="javascript:void(0)">' + geoObject.properties.get('name')+', ' + geoObject.properties.get('description') + '</a><br>';
                        geoObjectList.push(geoObject.properties.get('boundedBy'));
                    }

                    var dropDown = $('div.map__search_dropdown');
                    dropDown.html(html).slideDown(300);

                    $('a', dropDown).click(function () {
                        yamap.setBounds(geoObjectList[parseInt($(this).data('id'))], {
                            checkZoomRange: true // проверяем наличие тайлов на данном масштабе.
                        });
                        dropDown.slideUp(300);
                    });


                    dropDown[0].bound = geoObjectList;
                }
            });
        }
    };
})();