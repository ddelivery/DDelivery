    if(typeof(DDelivery) == 'undefined')
var DDelivery = {
    delivery: function (objectId, componentUrl, params, callbacks) {
        var iframe = document.createElement('iframe');

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
        object.style.height = '650px';
        object.innerHTML = '';
        object.appendChild(iframe);


        if(typeof(callbacks)!='object'){
            callbacks = false;
        }
        var message = function (event) {

            // Не наше окно, мы его не слушаем
            if(iframe.contentWindow != event.source) {
                return;
            }
            var data;
            eval('data = '+event.data);
            var result;
            if (typeof(callbacks[data.action]) == 'function') {
                result = callbacks[data.action](data.data);
            }
            if( result !== false ) {
                if (data.action == 'close') {
                    //iframe.parentNode.removeChild(iframe);
                }
            }
        };
        if (typeof (window.addEventListener) != 'undefined') { //код для всех браузеров
            window.addEventListener("message", message, false);
        } else { //код для IE
            window.attachEvent("onmessage", message);
        }

        iframe.contentWindow.params = params;

    }
};


