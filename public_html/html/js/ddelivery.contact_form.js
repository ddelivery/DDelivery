/**
 * Created by DnAp on 11.04.14.
 */
var ContactForm = (function(){
    var el;

    return {
        init: function(){

            $('.phone-mask').mask("+9(999)999-999-99");

            $('#main_form').submit(function(){
                console.log($(this).serializeArray());
                return false;
            });

            $('.row-btns a.next').click(function(){
                $('#main_form').submit();
            });

            $('.row-btns a.prev').click(function(){
                DDeliveryIframe.ajaxPage({});
            });

        }
    }
})();
