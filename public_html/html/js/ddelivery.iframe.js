var DDeliveryIframe = (function () {
    //Тут можно определить приватные переменные и методы

    var componentUrl, staticUrl;

    //Объект, содержащий публичное API
    return {
        componentUrl: null,
        staticUrl: null,
        orderId: null,
        init: function (_componentUrl, _staticUrl) {
            // Инициализация модуля. В ней мы инициализируем все остальные модули на странице
            this.componentUrl = componentUrl = _componentUrl;
            this.staticUrl = staticUrl = _staticUrl;
            this.ajaxPage({});
        },
        ajaxPage: function (data) {
            var th = this;
            if (this.orderId)
                data.order_id = this.orderId;
            $('#ddelivery').html('<img class="loader" src="' + staticUrl + '/img/ajax_loader.gif"/>');
            $.post(componentUrl, data, function (dataHtml) {
                $('#ddelivery').html(dataHtml.html);

                if (typeof(dataHtml.orderId) != 'undefined' && dataHtml.orderId) {
                    th.orderId = dataHtml.orderId;
                }

                th.render(dataHtml);
            }, 'json');
            $(window).trigger('ajaxPage');
        },
        ajaxData: function (data, callBack) {
            if (this.orderId)
                data.order_id = this.orderId;
            $.post(componentUrl, data, callBack, 'json');
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
                    }
                }
            }

            /*$(window).on('ddeliveryCityPlace', function(e, data){
             $this.getData(data.id)
             });*/

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

