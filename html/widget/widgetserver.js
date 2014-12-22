var WidgetServer = (function(){
    var ajaxUrl;
    var actionStart;

    function resizeDiv(arrow_color){
        WidgetServer.postMessage('resize', {size:$('body').height() + 'px',
                                            color:arrow_color}
        );
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


                //setCookie('city_name',$(this).find('strong').text(), 100);
                $.cookie('city_id', city, { expires: 100 });
                $.cookie('city_name', $(this).find('strong').text(), { expires: 100 });
                WidgetServer.postMessage('product_widget', {});
                WidgetServer.postMessage('geo', {cityId:$.cookie('city_id'), name:$.cookie('city_name')});


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
        get_tracking:function(data){

            $('#content').html(data.html);
            $('.widget-drop__close').on('click', function(){
                WidgetServer.postMessage('close', {});
            });
            $('#dd_tracking_other').on('click', function(){
                WidgetServer.ajaxData({action:'tracking', dd_widget:1});
            });
            resizeDiv('white');
        },
        tracking:function(data){
            $('#content').html(data.html);
            $('#dd_tracking_yes').on('click', function(){
                var tracking_val =  $('#tracking_val').val();
                if( tracking_val.length > 0 ){
                    $('.dd_loader').css('display', 'block');
                    $('.tracking_content').empty();
                    WidgetServer.ajaxData({action:'get_tracking', dd_widget:1, tracking_val:tracking_val});
                }
            });
            $('.widget-drop__close').on('click', function(){
                WidgetServer.postMessage('close', {});
            });
            resizeDiv('white');
        },
        by_name:function(data){
            $('.dd_loader').css('display', 'none');
            $('.choose-list').html(data.html);
            $('.choose-list__item').on('click', function(){
                $('.dd_loader').css('display', 'block');
                $('.choose-list').empty();
                var city = $(this).attr('data');

                $.cookie('city_id', city, { expires: 100 });
                $.cookie('city_name', $(this).find('strong').text(), { expires: 100 });

                WidgetServer.postMessage('product_widget', {});
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

            $.cookie('city_id', data.json._id, { expires: 100, path: '/' });
            $.cookie('city_name', data.json.display_name, { expires: 100, path: '/' });

            WidgetServer.ajaxData({action:actionStart, dd_widget:1, city:$.cookie('city_id')});

            if( actionStart != 'target_product' ){
                WidgetServer.postMessage('geo', {cityId: $.cookie('city_id'), name:$.cookie('city_name')});
            }
        }
    }
    return {
        init:function(url, action_start){
            ajaxUrl = url;
            actionStart = action_start;
            if( actionStart == 'geo' ){

                if( typeof ($.cookie('city_id')) == 'undefined' ){

                    this.ajaxData({action:'get_city'});
                }else {

                    WidgetServer.postMessage('geo', {cityId: $.cookie('city_id'), name: $.cookie('city_name')});
                }
            }else{

                if( typeof ($.cookie('city_id')) == 'undefined' ){
                    this.ajaxData({action:'get_city'});
                }else{
                    this.ajaxData({action:actionStart, dd_widget:1, city:$.cookie('city_id')});
                    if( actionStart != 'target_product' ){
                        WidgetServer.postMessage('geo', {cityId:$.cookie('city_id'), name:$.cookie('city_name')});

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