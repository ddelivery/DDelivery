

jQuery(function ($) {
    $('.phone-mask').mask("+9(999)999-999-99");

    $('.delivery-type__title').on('click', function () {
        $('.delivery-type__drop').slideToggle(function () {
            if ($('.delivery-type__drop').css('display') == 'block') {
                $('.map-popup__main').addClass('show-drop-1');
                $('.map-popup__main__overlay').fadeIn();
            } else {
                $('.map-popup__main').removeClass('show-drop-1');
                $('.map-popup__main__overlay').fadeOut();
            }
        });
    });
/*
    $('.delivery-place__title > input[title]').formtips().on('focus', function () {
        $(this).parent().parent().find('.delivery-place__drop').slideDown(function () {
            $('.map-popup__main').addClass('show-drop-2');
            if ($('.no-touch').length) {
                $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
            }
        });
    });

    $('.delivery-place__title > span').on('click', function () {
        $(this).parent().parent().find('.delivery-place__drop').slideToggle(function () {
            $('.map-popup__main').toggleClass('show-drop-2');
            if ($('.no-touch').length) {
                $(this).find('.delivery-place__drop_i').mCustomScrollbar('update');
            }
        });
    });

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
    });*/

    if ($('.no-touch').length) {
        $('.map-popup__info__more__text, .delivery-place__drop_i').mCustomScrollbar({
            scrollInertia: 0
        });
    }

    $('.map-popup__info__more__btn').on('click', function (e) {
        e.preventDefault();
        var el = $(this).toggleClass('open');
        el.closest('.map-popup__info__more').find('.map-popup__info__more__text').slideToggle(function () {
            if ($('.no-touch').length) {
                $(this).mCustomScrollbar('update');
            }
        });
    });

    $('.map-popup__main__right .places a.hasinfo').on('click', function (e) {
        e.preventDefault();
        $('.map-popup__main__right .places a').removeClass('active');
        var el = $(this).addClass('active');
        $('.map-popup__info').not($(el.attr('href'))).fadeOut();
        $(el.attr('href')).fadeIn();
        $('.map-popup__main__right .places').addClass('info-open');
    });

    $('.map-popup__info__close').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.map-popup__info').fadeOut();
        $('.map-popup__main__right .places a').removeClass('active');
        $('.map-popup__main__right .places').removeClass('info-open');
    });

    /*
    $('.map-popup__main__delivery input[type="radio"]').Custom({
        customStyleClass: 'radio',
        customHeight: '20'
    });

    $('.map-popup__main__delivery table tr').hover(function () {
        $(this).addClass('hover');
    }, function () {
        $(this).removeClass('hover');
    });

    $('.map-popup__main__delivery table tr').on('click', function (e) {
        e.preventDefault();
        $(this).find('input[type="radio"]').prop('checked', true).change();
    });
    */

    if ($('.no-touch').length) {
        $('.map-popup__main__delivery').mCustomScrollbar({
            scrollInertia: 0
        });
    }

    $('.map-popup__main__form input[type="text"][title], .map-popup__main__form textarea[title]').formtips();

    $('.tip-box > i').on('click', function(){
        $(this).parent().find('.tip-box_i').fadeIn();
    });

    $('.tip-box__close').on('click', function(){
        $(this).closest('.tip-box_i').fadeOut();
    });
});