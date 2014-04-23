var DDelivery = {
    delivery: function (objectId, componentUrl, params, callbacks) {
        var iframe = document.createElement('iframe');
        iframe.params = params;
        iframe.style.width = '1000px';
        iframe.style.height = '650px';
        iframe.style.overflow = 'hidden';
        iframe.scrolling = 'no';
        iframe.frameBorder = 0;
        iframe.style.borderWidth = 0;
        if (componentUrl.indexOf('?') == -1) {
            componentUrl += '?iframe=1';
        } else {
            componentUrl += '&iframe=1';
        }
        iframe.src = componentUrl;
        var object = document.getElementById(objectId);
        object.appendChild(iframe);

        if(typeof(callbacks)!='object'){
            callbacks = false;
        }

        var message = function (event) {
            var result;

            if (typeof(callbacks[event.data.action]) == 'function') {
                result = callbacks[event.data.action](event.data);
            }
            if( result !== false ) {
                if (event.data.action == 'close') {
                    iframe.remove();
                }
            }
        };
        if (typeof (window.addEventListener) != 'undefined') { //код для всех браузеров
            window.addEventListener("message", message, false);
        } else { //код для IE
            window.attachEvent("onmessage", message);
        }
    }
};
