/**
 * Created by DnAp on 18.04.14.
 */
var TypeForm = (function(){
    var el;

    return {
        init: function(){

            $('.map-popup__main__delivery input[type="radio"]').Custom({
                customStyleClass: 'radio',
                customHeight: '20'
            });

            this.event();
        },
        renderData: function (data) {
            var table = $('.map-popup__main__delivery table');
            console.log(data);
            for(var key in data) {
                var cur = $('tr.'+key, table);
                if(data[key].disabled) {
                    cur.addClass('disabled');
                } else {
                    cur.removeClass('disabled');
                    console.log($('.min_price', cur));
                    console.log($('.min_price', data[key].minPrice));
                    $('.min_price', cur).html(data[key].minPrice);
                    $('.min_time', cur).html(data[key].minTime);
                }
            }
        },
        event: function(){

            var mapPopupTableTr = $('.map-popup__main__delivery table tr');
            mapPopupTableTr.hover(function () {
                if(!$(this).hasClass('disabled')){
                    $(this).addClass('hover');
                }
            }, function () {
                $(this).removeClass('hover');
            });
            mapPopupTableTr.on('click', function (e) {
                e.preventDefault();
                var radio = $(this).find('input[type="radio"]');
                if(!radio.attr('disabled')){
                    radio.prop('checked', true).change();
                }
            });

            $('.map-popup__main__delivery__next a').click(function(){
                var radio = $('input[type="radio"]:checked').val();
                if(radio) {
                    DDeliveryIframe.ajaxPage({
                        type: radio,
                        city_id: $('input[name=ddelivery_city]').val()
                    });
                }
            });


            $(window).on('ddeliveryCityPlace', function(e, data){

                DDeliveryIframe.ajaxData({action: 'typeFormDataOnly', city_id: data.id}, function(data) {
                    TypeForm.renderData(data.data);
                });
            })

        }
    }
})();
