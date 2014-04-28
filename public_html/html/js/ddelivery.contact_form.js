/**
 * Created by DnAp on 11.04.14.
 */
var ContactForm = (function () {
    var el;

    return {
        init: function () {
            $('.phone-mask').mask("+9(999)999-99-99");

            var form = $('#main_form').submit(function () {
                $('input', this).trigger('required');
                if($('.error', this).length > 0){
                    return false;
                }

                console.log($(this).serializeArray());
                return false;
            });
            $('input', form).on('required', function() {
                var el = $(this);
                if(el.prop('required') && el.val().trim().length == 0) {
                    el.closest('.row__inp').addClass('error');
                }else{
                    el.closest('.row__inp').removeClass('error');
                }
            }).blur(function(){
                $(this).trigger('required');
            }).on('keyup', function(){
                $(this).trigger('required');
            });

            $('.row-btns a.next').click(function () {
                $('#main_form').submit();
            });

            $('.row-btns a.prev').click(function () {
                DDeliveryIframe.ajaxPage({});
            });

            $('input[title]').formtips().on('focus', function () {
                $(this).parent().parent().find('.delivery-place__drop').slideDown(function () {
                    $('.map-popup__main').addClass('show-drop-2');
                    if ($('.no-touch').length) {
                        $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
                    }
                });
            });


        }
    }
})();
