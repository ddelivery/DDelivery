/**
 * Created by DnAp on 10.04.14.
 */
var Courier = (function(){
    var el;
    var componentUrl, staticUrl;

    return {
        init: function(){
            $('.map-popup__main__delivery__next a').click(function(){
                var radio = $('input[type="radio"]:checked').val();
                if(radio) {
                    DDeliveryIframe.ajaxPage({
                        point: $('input[name=delivery_company]').val(),
                        'action': 'contactForm'
                    });
                }
            });

        }
    }
})();
