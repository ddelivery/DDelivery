
var DDeliveryBitrix = {
    jQuery: null,
    options: {
        mapContainer:'map_container',
        cityContainer:'ddelivery-city-list',
        hiddenContainer:'hidden_postomat',
        filtersContainer:'filters_container',
        pointsContainer:'points_container',
        infoContainer: 'info_container',
        singleItemTemplate: 'single-item-template',
        singleItemContentTemplate: 'single-item-content',
        multyItemTemplate: 'multy-item-template',
        customCallback: null,
        clusterClickCallback: false,
        priceCache: 0,
        priceURL: '/ddelivery/ddelivery.php?action=getPrice&r='+Math.random(),
        mediaURL: '/media/map'
    },
    isLoad: false,
    rand: Math.random(),
    Init: function(params){
        DDeliveryBitrix.options.customCallback = this.onChangePoint;
        var func = function(){
            DDeliveryBitrix.isLoad = true;
            var radio = $('#ID_DELIVERY_DigitalDelivery_all');
            $('#delivery_info_DigitalDelivery_all').hide();
            ddEngine.inited = false;
            if(radio[0].checked){
                $('#ddelivery_main').show();
                $('#ddelivery-container').show();
				$('#ddelivery-container').click(function(){return false});

                ddEngine.setData('width', params.x);
                ddEngine.setData('height', params.y);
                ddEngine.setData('length', params.z);
                ddEngine.setData('weight', params.w);
                ddEngine.showMap(DDeliveryBitrix.options);
                //ddEngine.setData('declared_price', params.p);
            }else{
                radio.bind('change', function(){
                    $('#ddelivery-container').show();
					$('#ddelivery-container').click(function(){return false});
					
                    ddEngine.postomats = DDeliveryPostomats.response;
                    ddEngine.setData('width', params.x);
                    ddEngine.setData('height', params.y);
                    ddEngine.setData('length', params.z);
                    ddEngine.setData('weight', params.w);
                    ddEngine.showMap(DDeliveryBitrix.options);
                });
            }
        };

        if(typeof(top.DDeliveryBitrix) != 'undefined' && top.DDeliveryBitrix.rand != DDeliveryBitrix.rand){
            setTimeout(function(){top.DDeliveryBitrix.Init(params)}, 100);
        }else{
            jQuery(func);
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

        $('.ddelivery-customer-city').hide();
        $('.ddelivery-filter-additional-block').hide();

        $('[map-wrapper]').hide();
        $('.ddelivery-result').show();
        $('[point-name]').html(point.name + ' #' + point._id);
        $('[city-name]').html(point.city.name);
        $('[price-holder]').html(point.price + ' руб.');
        $('[time-holder]').html(point.time + ' дн.');
        $('.ddelivery-result').find('.ddelivery-item-info2').html( $('#info_container').find('.ddelivery-item-info2').html() );
    }
};
