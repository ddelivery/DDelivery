var WidgetServer = (function(){
    var ajaxUrl;
    var actionStart;

    function resizeDiv(arrow_color){
        WidgetServer.postMessage('resize', {size:$('body').height() + 'px',
                                            color:arrow_color}
        );
    }
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
        }
        return "";
    }
    var callBacks = {
        demo_stand:function(data){
            $('#content').html(data.html);
            $('#choose_other_city').on('click', function(){
                $('.widget-drop__choose_i').empty();
                $('.dd_loader').css('display', 'block');
                WidgetServer.ajaxData({ dd_widget:1, action: 'change_city'})
            });
            $('.widget-drop__close').on('click', function(){
                WidgetServer.postMessage('close', {});
                //console.log('close');
            });
            resizeDiv('white');
        },
        change_city:function(data) {
            $('#content').html(data.html);
            $('.city-drop__choose').mCustomScrollbar({
                axis:"y",
                theme:"dark"
            });
            $('#widget__search__btn').on('click', function(){
                var title = $('#edit_city').val();
                if (title.length >= 1) {
                    $('.dd_loader').css('display', 'block');
                    $('.choose-list').empty();
                }
                WidgetServer.ajaxData({action: 'by_name', name: title});
                return false;
            });
            $('.choose-list__item').on('click', function(){
                $('.dd_loader').css('display', 'block');
                $('.choose-list').empty();
                var city = $(this).attr('data');

                setCookie('city_id', city, 100);
                setCookie('city_name', encodeURI($(this).find('strong').text()), 100);

                WidgetServer.postMessage('geo', {cityId:getCookie('city_id'), name:getCookie('city_name')});
                WidgetServer.ajaxData({action:'demo_stand', dd_widget:1, city:city});
            });
            var searchTimeout = 0;
            $('#edit_city').keyup(function () {
                var title = $(this).val();
                var input = $(this);
                if (title.length >= 1) {
                    $('.dd_loader').css('display', 'block');
                    $('.choose-list').empty();
                    //$('.delivery-place__drop_i ul.search').html('<img class="loader_search" src="' + staticUrl + 'img/ajax_loader.gif"/>');
                    if(!searchTimeout)
                        clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function(){
                        WidgetServer.ajaxData({action: 'by_name', name: title});
                    }, 600);
                }
            });
            $('.widget-drop__close').on('click', function(){
                WidgetServer.postMessage('close', {});
                //console.log('close');
            });
            resizeDiv('red');
        },
        by_name:function(data){
            $('.dd_loader').css('display', 'none');
            $('.choose-list').html(data.html);
            $('.choose-list__item').on('click', function(){
                $('.dd_loader').css('display', 'block');
                $('.choose-list').empty();
                var city = $(this).attr('data');
                WidgetServer.ajaxData({action:'demo_stand', dd_widget:1, city:city});
            });
        },
        default_callback:function(data){
            $('#content').html(data.html);
            $('.widget-drop__close').on('click', function(){
                WidgetServer.postMessage('close', {});
                //console.log('close');
            });
            $('#dd_calc_yes').on('click',function(){
                WidgetServer.ajaxData({action:'demo_stand', dd_widget:1, city:151184});
            });
            $('#dd_calc_no').on('click',function(){
                $('.dd_loader').css('display','block');
                WidgetServer.ajaxData({action:'change_city', dd_widget:1});
            });
            resizeDiv('white');
        },
        target_product:function(data){
            $('#content').html(data.html);
            resizeDiv('');
        },
        get_city:function(data){
            setCookie('city_id', data.json._id, 100);
            setCookie('city_name', decodeURI(data.json.display_name), 100);
            WidgetServer.ajaxData({action:actionStart, dd_widget:1, city:getCookie('city_id')});
            if( actionStart != 'target_product' ){
                WidgetServer.postMessage('geo', {cityId:getCookie('city_id'), name:getCookie('city_name')});
            }
        }
    }
    return {
        init:function(url, action_start){
            ajaxUrl = url;
            actionStart = action_start;
            if( actionStart == 'geo' ){
                WidgetServer.postMessage('geo', {cityId:getCookie('city_id'), name:getCookie('city_name')});
            }else{
                if( getCookie('city_id') == '' ){
                    this.ajaxData({action:'get_city'});
                }else{
                    this.ajaxData({action:actionStart, dd_widget:1, city:getCookie('city_id')});
                    if( actionStart != 'target_product' ){
                        WidgetServer.postMessage('geo', {cityId:getCookie('city_id'), name:getCookie('city_name')});
                    }
                }
            }
        },
        postMessage: function(action, data) {
            // Отправляем сообщение родительскому окну
            var dataJSON = {action:action, data: data};
            window.parent.postMessage(dataJSON, '*');
        },
        ajaxData: function (data) {
            $.post(ajaxUrl, data, function(result){
                var params = result.data;
                callBacks[result.action](params);
            }, 'json');
        }
    }
})();