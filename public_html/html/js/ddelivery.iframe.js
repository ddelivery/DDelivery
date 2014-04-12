var DDeliveryIframe = (function(){
    //Тут можно определить приватные переменные и методы

    var componentUrl, staticUrl;

    //Объект, содержащий публичное API
    return {
        componentUrl:null,
        staticUrl:null,
        init: function(_componentUrl, _staticUrl) {
            // Инициализация модуля. В ней мы инициализируем все остальные модули на странице
            this.componentUrl = componentUrl = _componentUrl;
            this.staticUrl = staticUrl = _staticUrl;
            this.ajaxPage({});
        },
        ajaxPage: function(data) {
            var th = this;
            $('#ddelivery').html('<img class="loader" src="'+staticUrl+'/img/ajax_loader.gif"/>');
            $.post( componentUrl, data, function( dataHtml ) {
                $( '#ddelivery' ).html(dataHtml.html );
                th.render(dataHtml);
            }, 'json');
            $(window).trigger('ajaxPage');
        },
        ajaxData: function(data, callBack) {
            $.post( componentUrl, data, callBack, 'json');
        },
        render: function(data) {
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

            // У всех
            CityPlace.init();

            if(typeof(data.js) != 'undefined' && data.js.length > 0) {
                var js = data.js.split(',');
                for(var k=0 ; k<js.length ; k++){
                    switch (js[k]){
                        case 'courier':
                            Courier.init();
                            break;
                        case 'map':
                            Map.init(data);
                            break;
                        case 'contactForm':
                            ContactForm.init();
                            break;
                    }
                }
            }

            $('.map-popup__main__right__btn').on('click', function () {
                $('.map-popup__main__right').toggleClass('map-popup__main__right_open');
                $('.map-popup__info').toggleClass('wide');
            });

            /*$(window).on('ddeliveryCityPlace', function(e, data){
                $this.getData(data.id)
            });*/

            // Ссылки
            $('.map-popup__main__delivery__next a').click(DDeliveryIframe.typeFormSubmit);
        },
        typeFormSubmit: function(){

            var radio = $('input[type="radio"]:checked').val();
            if(radio) {
                DDeliveryIframe.ajaxPage({
                    type: radio,
                    city_id: $('input[name=ddelivery_city]').val()
                });
            }

        }

    }
})();




var DDeliveryIframeOld = {
    delivery: function(componentUrl, staticUrl, params){
        var Delivery;
        Delivery = {
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
            // события

            typeFormSubmit: function(){

                var radio = $('input[type="radio"]:checked').val();
                if(radio) {
                    Delivery.ajaxPage({
                        type: radio,
                        city_id: $('input[name=ddelivery_city]').val()
                    });
                }

            }

        };
        Delivery.init(params);
        return Delivery;
    }
};
