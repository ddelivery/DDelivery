var DDeliveryBitrix = {
    options: null,
    isLoad: false,
    Init: function(options){
        options.customCallback = this.onChangePoint;
        this.options = options;
        var func = function(){
            var radio = $('#ID_DELIVERY_DigitalDelivery_all');
            ddEngine.API_URL = options.API_URL;
            ddEngine.BASE_DOMAIN = options.BASE_DOMAIN;
            $('#delivery_info_DigitalDelivery_all').hide();
            ddEngine.inited = false;
            if(radio[0].checked){
                $('#ddelivery_main').show();
                ddEngine.showMap(DDeliveryBitrix.options);
            }else{
                radio.bind('change', function(){
                    ddEngine.showMap(DDeliveryBitrix.options);
                });
            }
        };
        if(DDeliveryBitrix.isLoad){
            func();
        }else{
            $(func);
        }
    },
    showMap: function(){
        $('#'+DDeliveryBitrix.options.mainContainer).show();
        $('#'+DDeliveryBitrix.options.resultContainer).hide();
    },
    onChangePoint: function(point){
        $.getJSON('/ddelivery/ddelivery.php', {action: 'setPoint', id: point._id}, function(){
            $('#delivery_info_DigitalDelivery_all').click();
        });

        $('#'+DDeliveryBitrix.options.mainContainer).hide();
        var resContent = $('#'+DDeliveryBitrix.options.resultContainer);

        var adrr_arr = point.schedule.split(',');
        var addr = '';
        if(adrr_arr.count > 1){
            var dayName = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
            for (var k = 0; k < adrr_arr.length; k++) {
                if (adrr_arr[k] != 'NODAY') {
                    addr += dayName[k] + ': ' + adrr_arr[k] + "<br>";
                }
            }
        }else{
            addr = point.schedule;
        }
        var content = '';
        if(typeof(point.price) != 'undefined') {
            content+='<p>Цена: '+point.price+' руб, время доставки : '+point.time+' </p>';
        }
        content += '<p><b>Адрес:</b>г. '+ point['city']['name'] + ', ' + point.address
            + ( point.indoor_place != '' ? '<br>' + point.indoor_place : '' )
            + ( point.description_in != '' ? '<br>' + point.description_in : '' )
            + ( point.description_out != '' ? '<br>' + point.description_out : '' )
            + '</p>'
            + ( point.metro != '' ? '<p><b>Метро:</b> ' + point.metro + '</p>' : '' )
            + '<p><b>Расписание работы:</b> ' + addr
            + '</p><p><b>Варианты оплаты:</b><br>' + ( point.is_cash ? '- Наличными<br>' : '')
            + ( point.is_card ? '- Безналичный расчет<br>' : '')
            + '<p><b>Примерочная:</b> ' + ( point.has_fitting_room ? 'Есть<br>' : 'Нет<br>')
            + '<p><b>Автоматическая выдача (постамат):</b> ' + ( point.type == 1 ? 'Да' : 'Нет')
            + '</p><p><a href="javascript:DDeliveryBitrix.showMap()">Изменить точку</a></p>';

        resContent.html(content).show();
    }
};
