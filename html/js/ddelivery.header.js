/**
 * Created by DnAp on 09.04.14.
 * Оживляет шапку с выбором типа оплаты и поиска города.
 */

var Header = (function () {
    var el;
    var componentUrl, staticUrl;

    var eventAjaxPageRenderListen = false;
    var typeData = false;
    var lastType = false;

    var eventAjaxPageRender = function(e, data) {
        if (typeof(data.result.typeData) != 'undefined') {
            typeData = data.result.typeData;
        }
        if(typeof (data.result.type) != 'undefined'){
            lastType = data.result.type;
        }
    };

    return {
        init: function () {
            if ($('.delivery-place').length > 0) {
                this.renderPlace();
                this.event();
            }

            if($('.delivery-type').length > 0){
                this.renderType();
                this.eventType();
            }

            componentUrl = DDeliveryIframe.componentUrl;
            staticUrl = DDeliveryIframe.staticUrl;

            // Вырубаем все старые евенты, подпишитесь заново
            $(window).off('ddeliveryCityPlace');
            if(!eventAjaxPageRenderListen) {
                eventAjaxPageRenderListen = true;
                $(window).on('ajaxPageRender', eventAjaxPageRender);
                $(window).on('ajaxDataResult', eventAjaxPageRender);
            }

            $('.map-popup__head__close').click(function(){
                DDeliveryIframe.close();
            });

        },
        renderType: function() {

            if(lastType == 1){
                $('.delivery-type__title.self').show();
            }
            if(lastType == 2){
                $('.delivery-type__title.courier').show();
            }
            for(var key in typeData) {
                var curData = typeData[key];

                var el = $('.delivery-type__drop_'+key);
                if(curData.disabled){
                    el.hide();
                }else{
                    el.show();
                    $('.price span', el).html(curData.minPrice);
                    $('.date strong', el).html(curData.minTime);
                    $('.date span', el).html(curData.timeStr);

                    $('.delivery-type__title.'+key+' .price').html(curData.minPrice);
                }
            }

        },
        eventType: function(){
            var slideToggle = function () {
                $('.delivery-type__drop').slideToggle(function () {
                    if ($('.delivery-type__drop').css('display') == 'block') {
                        $('.map-popup__main').addClass('show-drop-1');
                        $('.map-popup__main__overlay').fadeIn();
                    } else {
                        $('.map-popup__main').removeClass('show-drop-1');
                        $('.map-popup__main__overlay').fadeOut();
                    }
                });
            };
            $('.delivery-type__title').on('click', slideToggle);
            $('.delivery-type__drop_self').click(function(){
                slideToggle();
                if(lastType != 1) {
                    DDeliveryIframe.ajaxPage({action: 'map'});
                }
            });
            $('.delivery-type__drop_courier').click(function(){
                slideToggle();
                if(lastType != 2) {
                    DDeliveryIframe.ajaxPage({action:'courier'});
                }
            });
        },
        renderPlace: function () {
            $('.map-popup__info__more__text, .delivery-place__drop_i').mCustomScrollbar({
                scrollInertia: 0
            });
        },

        event: function () {
            function citySelectEvent() {
                var cityId = $(this).data('id');

                $('.delivery-place__drop').slideUp(function () {
                    $('.map-popup__main').removeClass('show-drop-2');
                });

                if ($('input[name=ddelivery_city]').val() == cityId) {
                    return false;
                }

                var title = $(this).text().trim().replace(/[ ]{2,}/g, ' ').replace('\n', ', ');
                $('.delivery-place__title input').val('').attr('title', title).blur();

                $('.delivery-place__drop li a').removeClass('active');
                $(this).addClass('active');

                $('input[name=ddelivery_city]').val(cityId);


                $(window).trigger('ddeliveryCityPlace', {id: cityId, title: title});

                return false;
            }

            var searchTimeout = 0;
            $('.delivery-place__title > input[title]')
                .formtips()
                .on('focus', function () {
                    $(this).parent().parent().find('.delivery-place__drop').slideDown(function () {
                        $('.map-popup__main').addClass('show-drop-2');
                        if ($('.no-touch').length) {
                            $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
                        }
                    });
                })
                .keyup(function () {
                    var title = $(this).val();
                    var input = $(this);
                    if (title.length >= 1) {
                        $('.delivery-place__drop_i ul.search').html('<img class="loader_search" src="' + staticUrl + 'img/ajax_loader.gif"/>');
                        if(!searchTimeout)
                            clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function(){
                            DDeliveryIframe.ajaxData({action: 'searchCity', name: title}, function (data) {
                                if (data.request.name == input.val()) {
                                    $('.delivery-place__drop_i .pop').hide();
                                    $('.delivery-place__drop_i .search').show();
                                    $('.delivery-place__drop_i ul.search').html(data.html);
                                }
                                $('.delivery-place__drop .search li a').on('click', citySelectEvent);
                            });
                        }, 600);
                    }
                });

            $('.delivery-place__title > span').on('click', function () {
                $(this).parent().parent().find('input').focus();
            });
            $('.delivery-place__drop li a').on('click', citySelectEvent);


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

        }

    };
})();