var DDeliveryIframe = (function () {
    //Тут можно определить приватные переменные и методы

    var componentUrl, staticUrl;

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
        },
        ajaxPage: function (data) {
            var th = this;
            if (this.orderId)
                data.order_id = this.orderId;
            $('#ddelivery').html('<img class="loader" src="' + staticUrl + '/img/ajax_loader_horizont.gif"/>');

            $.post(componentUrl, data, function (dataHtml) {
                $('#ddelivery').html(dataHtml.html);

                if (typeof(dataHtml.orderId) != 'undefined' && dataHtml.orderId) {
                    th.orderId = dataHtml.orderId;
                }

                $(window).trigger('ajaxPageRender', {params: data, result: dataHtml});

                th.render(dataHtml);

            }, 'json');
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
            window.parent.postMessage({action:action, data: data}, '*');
        },
        close: function(){
            DDeliveryIframe.postMessage('close', {});
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

