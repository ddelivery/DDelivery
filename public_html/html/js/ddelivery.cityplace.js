/**
 * Created by DnAp on 09.04.14.
 */
var CityPlace = (function(){
    var el;
    var componentUrl, staticUrl;

    return {
        init: function(){
            el = $('.delivery-place');
            if(el.length > 0) {
                this.render();
                this.event();
            }
            componentUrl = DDeliveryIframe.componentUrl;
            staticUrl = DDeliveryIframe.staticUrl;

            // Вырубаем все старые евенты, подпишитесь заново
            $(window).off('ddeliveryCityPlace');
        },
        render: function(){
            $('.map-popup__info__more__text, .delivery-place__drop_i').mCustomScrollbar({
                scrollInertia: 0
            });
        },
        event: function(){
            function citySelectEvent() {
                var cityId = $(this).data('id');

                if($('input[name=ddelivery_city]').val() == cityId) {
                    return false;
                }

                var title = $(this)[0].innerText.trim().replace('\n', ', ');
                $('.delivery-place__title input').val('').attr('title', title).blur();

                $('.delivery-place__drop li a').removeClass('active');
                $(this).addClass('active');

                $('input[name=ddelivery_city]').val(cityId);

                $('.delivery-place__drop').slideUp(function () {
                    $('.map-popup__main').removeClass('show-drop-2');
                });
                $(window).trigger('ddeliveryCityPlace', {id: cityId, title: title});

                return false;
            }

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
                .keyup(function(){
                    var title = $(this).val();
                    var input = $(this);
                    if(title.length >= 2){
                        $('.delivery-place__drop_i ul.search').html('<img class="loader_search" src="'+staticUrl+'/img/ajax_loader.gif"/>');
                        DDeliveryIframe.ajaxData({action: 'searchCity', name: title}, function(data){
                            if(data.request.name == input.val()){
                                $('.delivery-place__drop_i .pop').hide();
                                $('.delivery-place__drop_i .search').show();
                                $('.delivery-place__drop_i ul.search').html(data.html);
                            }
                            $('.delivery-place__drop .search li a').on('click', citySelectEvent);
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