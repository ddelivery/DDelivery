var DDeliveryIframe = (function () {
    //Тут можно определить приватные переменные и методы

    var componentUrl, staticUrl, lastData;

    function repeatLastQuery() {
        DDeliveryIframe.ajaxPage(lastData);
    }
    //Объект, содержащий публичное API
    return {
        componentUrl: null,
        staticUrl: null,
        orderId: null,
        init: function (_componentUrl, _staticUrl) {
            if(!window.parent || window.parent == window) {
                //document.location.href='http://www.ddelivery.ru/';
            }
            // Инициализация модуля. В ней мы инициализируем все остальные модули на странице
            this.componentUrl = componentUrl = _componentUrl;
            this.staticUrl = staticUrl = _staticUrl;
            // Да, нужно его подрубить тут
            Header.init();
            this.ajaxPage({});

            $('#ddelivery_loader .load_error a').click(repeatLastQuery);
        },
        ajaxPage: function (data) {
            lastData = data;
            var th = this;
            if (this.orderId)
                data.order_id = this.orderId;
            $('#ddelivery').hide();
            $('#ddelivery_loader').show();

            $('#ddelivery_loader .loader').show();
            $('#ddelivery_loader .load_error').hide();

            $.post(componentUrl, data, function (dataHtml) {
                $('#ddelivery_loader').hide();
                dataHtml.html = dataHtml.html.replace(/!KasperskyHack!/g,'');
                $('#ddelivery').html(dataHtml.html).show();
                //$('#ddelivery').html(dataHtml.html.replace(/!kasperskyhack!/g, '')).show();
                if (typeof(dataHtml.orderId) != 'undefined' && dataHtml.orderId) {
                    th.orderId = dataHtml.orderId;
                }

                $(window).trigger('ajaxPageRender', {params: data, result: dataHtml});

                th.render(dataHtml);

            }, 'json').fail(function(responce, errorType) {
                if(typeof(console.log) != 'undefined')

                $('#ddelivery_loader .loader').hide();
                $('#ddelivery_loader .load_error').show();
            });
            $(window).trigger('ajaxPageRequest', {params: data});
        },
        ajaxData: function (data, callBack) {
            if (this.orderId)
                data.order_id = this.orderId;
            $.post(componentUrl, data, function(result){
                $(window).trigger('ajaxDataResult', {params: data, result: result});
                callBack(result);
            }, 'json');
        },
        render: function (data) {
            // У всех
            Header.init();

            if (typeof(data.js) != 'undefined' && data.js.length > 0) {
                var js = data.js.split(',');
                for (var k = 0; k < js.length; k++) {
                    switch (js[k]) {
                        case 'courier':
                            Courier.init();
                            break;
                        case 'map':
                            Map.init(data);
                            break;
                        case 'contactForm':
                            ContactForm.init();
                            break;
                        case 'typeForm':
                            TypeForm.init();
                            break;
                        case 'close':
                            DDeliveryIframe.close();
                            break;
                        case 'change':
                            DDeliveryIframe.postMessage('change', data);
                            break;
                    }
                }
            }
        },
        postMessage: function(action, data) {
            // Отправляем сообщение родительскому окну
            var dataJSON = $.toJSON({action:action, data: data});
            window.parent.postMessage(dataJSON, '*');
        },
        close: function(){
            var th = this;
            DDeliveryIframe.postMessage('close', {orderId:th.orderId});
        }
    }
})();

// IE 7 not support Array.indexOf
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement, fromIndex) {
        if (this === undefined || this === null) {
            throw new TypeError('"this" is null or not defined');
        }

        var length = this.length >>> 0; // Hack to convert object.length to a UInt32

        fromIndex = +fromIndex || 0;

        if (Math.abs(fromIndex) === Infinity) {
            fromIndex = 0;
        }

        if (fromIndex < 0) {
            fromIndex += length;
            if (fromIndex < 0) {
                fromIndex = 0;
            }
        }

        for (; fromIndex < length; fromIndex++) {
            if (this[fromIndex] === searchElement) {
                return fromIndex;
            }
        }

        return -1;
    };
}

