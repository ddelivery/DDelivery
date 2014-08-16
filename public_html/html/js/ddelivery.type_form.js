/**
 * Created by DnAp on 18.04.14.
 */
var TypeForm = (function () {
    var el;

    return {
        init: function () {

            $('.map-popup__main__delivery input[type="radio"]').Custom({
                customStyleClass: 'radio',
                customHeight: '20'
            });

            this.event();
        },
        renderData: function (data) {
            var table = $('.map-popup__main__delivery table');
            for (var key in data) {
                var cur = $('tr.' + key, table);
                if (data[key].disabled) {
                    cur.addClass('disabled');
                    $('input', cur).prop('disabled', 1);
                } else {
                    cur.removeClass('disabled');
                    $('input', cur).prop('disabled', false);
                    $('.min_price', cur).html(data[key].minPrice);
                    $('.min_time', cur).html(data[key].minTime);
                    $('.time_str', cur).html(data[key].timeStr);
                }
            }
            $('.radio input').trigger('custom.refresh');
            if ($('.radio input:checked', table).prop('disabled')) {
                $('.radio input:not([disabled])').click();
            }
        },
        event: function () {

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

            $('.map-popup__main__delivery__next a').click(function () {
                var cityTitle = $('.delivery-place__title').find('input').attr('title');
                var radio = $('input[type="radio"]:checked').val();
                if (radio) {
                    DDeliveryIframe.ajaxPage({
                        type: radio,
                        city_id: $('input[name=ddelivery_city]').val(),
                        city_alias: cityTitle
                    });
                }
            });

            $(window).on('ddeliveryCityPlace', function (e, data) {
                var table = $('.map-popup__main__delivery table');
                $('.col4 span', table).css('visibility', 'hidden');
                $('.col5 span', table).css('visibility', 'hidden');
                $('.col4 img', table).show();
                DDeliveryIframe.ajaxData({action: 'typeFormDataOnly', city_id: data.id, city_alias: data.title}, function (data) {
                    $('.col4 span', table).css('visibility', 'inherit');
                    $('.col5 span', table).css('visibility', 'inherit');
                    $('.col4 img', table).hide();
                    TypeForm.renderData(data.typeData);
                });
            });


        }
    }
})();
