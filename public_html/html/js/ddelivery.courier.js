/**
 * Created by DnAp on 10.04.14.
 */
var Courier = (function () {
    var el;
    var componentUrl, staticUrl;

    return {
        init: function () {

            $('.map-popup__main__delivery').mCustomScrollbar({
                scrollInertia: 0
            });
            $('.map-popup__main__delivery input[type="radio"]').Custom({
                customStyleClass: 'radio',
                customHeight: '20'
            });


            this.event();
        },
        event: function(){

            var mapPopupTableTr = $('.map-popup__main__delivery table tr');
            mapPopupTableTr.hover(function () {
                if (!$(this).hasClass('disabled')) {
                    $(this).addClass('hover');
                }
            }, function () {
                $(this).removeClass('hover');
            });
            mapPopupTableTr.on('click', function (e) {
                e.preventDefault();
                if (!$(this).hasClass('disabled')) {
                    var radio = $(this).find('input[type="radio"]');
                    radio.prop('checked', true).change();
                }
            });

            $(window).on('ddeliveryCityPlace', function (e, city) {
                DDeliveryIframe.ajaxPage({action:'courier', city_id: city.id, city_alias:city.title });
            });

            $('.map-popup__main__delivery__next a').click(function () {
                var radio = $('input[type="radio"]:checked').val();
                if (radio) {
                    DDeliveryIframe.postMessage('courierChange', {point: couriers[radio]});
                    if(typeof(params) != 'undefined' && typeof(params.displayContactForm) == 'boolean' && !params.displayContactForm){
                        return;
                    }

                    DDeliveryIframe.ajaxPage({
                        point: radio,
                        action: 'contactForm',
                        type: 2
                    });
                }
            });
        }
    }
})();
