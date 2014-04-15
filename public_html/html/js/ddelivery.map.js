/**
 * Created by DnAp on 09.04.14.
 */
var Map = (function(){
    var yamap;
    var mapObject;
    var renderGeoObject;
    var points = [];

    var staticUrl;

    return {
        init: function(data) {
            staticUrl = DDeliveryIframe.staticUrl;
            points = data.points;
            mapObject = $('.map-canvas');
            if(mapObject.length != 1)
                return;
            var th = this;

            // Ожидаем загрузки объекта карты
            ymaps.ready(function(){
                // Получаем пользовательское местоположение и вызываем дальнейшую инициализацию
                ymaps.geocode($('.delivery-place__title input').attr('title'), {results: 1})
                    .then(function(res){
                        // Выбираем первый результат геокодирования.
                        renderGeoObject = res.geoObjects.get(0);
                        th.render();
                    });
            });

            // Инпут поиска
            $('.map__search input[type=text]').keyup(this.citySearch);
            $('.map__search input[type=submit]').click(function(){
                th.citySearch();
                return false;
            });

        },

        render: function(){

            // Область видимости геообъекта.
            var bounds = renderGeoObject.properties.get('boundedBy');
            // Получаем где отрисовать карту
            var centerAndZoom = ymaps.util.bounds.getCenterAndZoom(bounds, [mapObject.width(), mapObject.height()]);

            yamap = new ymaps.Map(mapObject[0], {
                center: centerAndZoom.center,
                zoom: centerAndZoom.zoom,
                behaviors: ['default', 'scrollZoom']
            },{
                maxZoom: 17
            });

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
                openBalloonOnClick: false,
                /**
                 * Опции кластеров указываем в кластеризаторе с префиксом "cluster".
                 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/Cluster.xml
                 */
                clusterDisableClickZoom: true
            });

            var geoObjects = [];
            var point;
            for(var pointKey in points) {
                point = points[pointKey];
                //console.log(point);
                var myPlacemark =new ymaps.Placemark([point.latitude,point.longitude], {
                        hintContent: point.address,
                        point: point
                    },{
                        iconLayout: 'default#image',
                        iconImageHref: staticUrl+'/img/point_75x75.png',
                        iconImageSize: [50, 50],
                        iconImageOffset: [-22, -46]
                    }
                );

                geoObjects.push(myPlacemark);
                //yamap.geoObjects.add(myPlacemark);
            }

            clusterer.add(geoObjects);
            yamap.geoObjects.add(clusterer);

            clusterer.events
                // Слушаем события кластера
                .add(['mouseenter', 'mouseleave'], function (e) {
                    var target = e.get('target'), // Геообъект - источник события.
                        eType = e.get('type'), // Тип события.
                        zIndex = Number(eType === 'mouseenter') * 1000; // 1000 или 0 в зависимости от типа события.

                    target.options.set('zIndex', zIndex);
                })
                .add('click', function(e){
                    var target = e.get('target');
                    t = target;
                    // Вернет все геобъекты
                    var geoObjects = target.properties.get('geoObjects');
                    if(geoObjects) { // Клик по кластеру
                        var bound = [[99,99],[0,0]];
                        for(var geoKey in geoObjects){

                            var coord = geoObjects[geoKey].geometry.getCoordinates();
                            if(bound[1][0] < coord[0])
                                bound[1][0] = coord[0];
                            if(bound[1][1] < coord[1])
                                bound[1][1] = coord[1];
                            if(bound[0][0] > coord[0])
                                bound[0][0] = coord[0];
                            if(bound[0][1] > coord[1])
                                bound[0][1] = coord[1];
                        }
                        // Точки эквивалентны в допустимой погрешности и зумить есть куда
                        if(!ymaps.util.math.areEqual(bound[0], bound[1], 0.0001) && yamap.getZoom() != yamap.options.get('maxZoom')){
                            yamap.setBounds(bound, {duration:400});
                        }else{
                            yamap.setBounds(bound, {duration:400});
                        }
                    }else{
                        Map.renderInfo(target.properties.getAll().point);
                    }

                });
        },

        renderInfo: function(point){
            if(!point.name){
                point.name = point.company+' #'+point._id
            }
            $('.map-popup__info__title h2').html(point.name);
            $('.map-popup__info__table .rub').html('load');
            var payType = [];
            if(point.is_cash){
                payType.push('Наличными');
            }
            if(point.is_card){
                payType.push('Visa/MasterCard');
            }
            if(payType.length == 0){
                payType.push('Предоплата');
            }
            $('.map-popup__info__table .payType').html(payType.join(', '));

            $('.map-popup__info').fadeIn();

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