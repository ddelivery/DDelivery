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
        iframe.contentWindow.params = params;

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
    }
};

(function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter25661477 = new Ya.Metrika({id:25661477,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
    } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
    s = d.createElement("script"),
    f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
})(document, window, "yandex_metrika_callbacks");

