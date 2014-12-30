/**
 * Created by mrozk on 06.12.14.
 */

var DDeliveryWidget = (function(w,doc) {

    var componentUrl = w.__ddWdgtEnterPoint;
    if (componentUrl.indexOf('?') == -1) {
        componentUrl += '?dd_widget=1';
    } else {
        componentUrl += '&dd_widget=1';
    }
    var productId = w.__ddWdgtProductId;
    var staticUrl = w.__ddWdgtStatic;
    // Контейнеры виджетов
    var ddelivery_widget, ddelivery_widget_product, ddelivery_traking;

    // iframe виджетов
    var product_iframe, ddelivery_traking_iframe, ddelivery_widget_iframe;


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

    function enableIframeListener(iframe, callbacks){
        var message = function (event) {
            // Не наше окно, мы его не слушаем
            if(iframe.contentWindow != event.source) {
                return;
            }
            var data = event.data;
            if (typeof(callbacks[data.action]) == 'function') {
                result = callbacks[data.action](data.data, iframe);
            }
        };
        if (typeof (window.addEventListener) != 'undefined') { //код для всех браузеров
            window.addEventListener("message", message, false);
        } else { //код для IE
            window.attachEvent("onmessage", message);
        }
    }


    var callbacksCityWidget = {
        resize:function(data,iframe){

            iframe.style.height = data.size;
            var evt = document.createEvent("Event");
            evt.initEvent("dd-color-triangle", true, false);
            evt.detail = data.color;
            iframe.dispatchEvent(evt);

        },
        close:function(data, iframe){

            var evt = document.createEvent("Event");
            evt.initEvent("dd-close-iframe", true, false);
            evt.detail = iframe;
            iframe.dispatchEvent(evt);
        },
        geo:function(data, iframe){
            //console.log( ifr  ame.parentNode.parentNode.parentNode.parentNode );
            if( ddelivery_widget ){
                ddelivery_widget.getElementsByClassName('dd_caption_container')[0].innerHTML = decodeURI(data.name);
            }
            //console.log( iframe.parentNode.parentNode.parentNode.parentNode);
            //cityNameContainer.innerHTML = decodeURI(data.name);
        },
        product_widget:function(data, iframe){
            if( ddelivery_widget_product ){
                product_iframe.src = componentUrl + '&start_action=target_product' + '&product=' + productId;
            }
        }
    }

    function addEvent(elem, type, handler){
        if (elem.addEventListener){
            elem.addEventListener(type, handler, false)
        } else {
            elem.attachEvent("on"+type, handler)
        }
    }

    function wrapIframe( iframe, clickUrl ){
        var content = doc.createElement('div');
        content.className = 'dd_wrapper';
        content.position = 'relative';

        var iframeWrapper = doc.createElement('div');
        iframeWrapper.className = 'dd_wrapper';
        iframeWrapper.style.position = 'relative';
        iframeWrapper.style.display = 'none';
        iframeWrapper.style.zIndex = '30000';

        var iContainer = doc.createElement('div');
        iContainer.style.mozBoxShadow = '0 0 8px 0 rgba(0, 0, 0, 0.5)';
        iContainer.style.webkitBoxShadow = '0 0 8px 0 rgba(0, 0, 0, 0.5)';
        iContainer.style.boxShadow = '0 0 8px 0 rgba(0, 0, 0, 0.5)';
        iContainer.style.position = 'absolute';
        iContainer.style.top = '7px';
        iContainer.className = 'dd_fade';
        iContainer.style.display = 'none';

        iframe.style.width = '400px';
        iframe.style.height = '0px';
        iframe.style.overflow = 'hidden';
        iframe.scrolling = 'no';
        iframe.frameBorder = 0;
        iframe.style.borderWidth = 0;
        iframe.style.display = 'block';

        var triangle = doc.createElement('div');
        triangle.style.position = 'absolute';
        triangle.style.left = '30px';
        triangle.style.top = '3px';
        triangle.className = 'dd_triangle';
        triangle.style.width = '16px';
        triangle.style.height = '7px';
        triangle.style.zIndex = '9000';
        triangle.style.top = '0px';
        triangle.style.left = '20px';

        var cityNameContainer = doc.createElement('div');
        cityNameContainer.style.fontSize = '16px';
        cityNameContainer.className = 'dd_caption_container';
        cityNameContainer.style.borderBottom = '1px dashed #000000';
        cityNameContainer.style.color = '#000000';
        cityNameContainer.style.display = 'inline-block';
        cityNameContainer.style.marginLeft = '15px';
        cityNameContainer.style.paddingTop = '10px';
        cityNameContainer.onmouseover = function(){
            cityNameContainer.style.cursor = 'pointer';
        }

        iContainer.appendChild(iframe);
        iframeWrapper.appendChild(triangle);
        iframeWrapper.appendChild(iContainer);
        content.appendChild(cityNameContainer);
        content.appendChild(iframeWrapper);


        addEvent(cityNameContainer,'click',function(){

            iContainer.style.display = 'block';
            iframeWrapper.style.display = 'block';
            if( iframe.style.height == '0px' ){
                iframe.src = componentUrl + clickUrl;
            }

        });

        addEvent( iframe, "dd-close-iframe", function(e) {
            //console.log(e.detail);
            e.detail.parentNode.parentNode.style.display = 'none';
            //console.log(e.detail); // Prints "Example of an event"
        });
        addEvent( iframe, "dd-color-triangle", function(e) {
            iframeWrapper.style.display = 'none';
            setTimeout(function () {iframeWrapper.style.display = 'block'; iContainer.style.display = 'block';}, 5);

            if( e.detail != '' ){
                if( e.detail == 'red'){
                    triangle.style.background = 'url("' + staticUrl + 'img/icons-sb02245f0c0.png")  0 -60px no-repeat';
                }else if(e.detail == 'white'){
                    triangle.style.background = 'url("' + staticUrl + 'img/icons-sb02245f0c0.png")  0 -72px no-repeat';
                }
            }
        });

        addEvent(doc.getElementsByTagName('body')[0], 'click', function(event){
            if (!event.target) {
                event.target = event.srcElement
            }
            if( event.target != iframeWrapper && event.target != cityNameContainer ){
                iframeWrapper.style.display = 'none';
            }
        });

        return content;

    }

    return {

        init:function(){
            if( doc.getElementById('ddelivery_traking') ) {
                ddelivery_traking = doc.getElementById('ddelivery_traking');
                ddelivery_traking_iframe = doc.createElement('iframe');
                ddelivery_traking.appendChild( wrapIframe(ddelivery_traking_iframe, '&start_action=tracking'));
                if((ddelivery_traking.getAttribute('data'))){
                    ddelivery_traking.getElementsByClassName('dd_caption_container')[0].innerHTML = ddelivery_traking.getAttribute('data');
                }else{
                    ddelivery_traking.getElementsByClassName('dd_caption_container')[0].innerHTML = "Трекинг";
                }
                enableIframeListener(ddelivery_traking_iframe, callbacksCityWidget);
            }
            if( doc.getElementById('ddelivery_widget') ) {
                ddelivery_widget = doc.getElementById('ddelivery_widget');
                ddelivery_widget_iframe = doc.createElement('iframe');
                ddelivery_widget.appendChild(wrapIframe(ddelivery_widget_iframe, '&start_action=demo_stand'));
                var date = new Date();
                var dateHash = date.getDate() + '_' + date.getFullYear();
                var ddLastWisit = getCookie('dd_last_wisit');
                if( (ddLastWisit == "") || ( ddLastWisit != dateHash) ){
                    ddelivery_widget_iframe.src = componentUrl + '&start_action=default';
                }else{
                    ddelivery_widget_iframe.src = componentUrl + '&start_action=geo';
                }
                setCookie('dd_last_wisit', dateHash ,100);
                enableIframeListener(ddelivery_widget_iframe, callbacksCityWidget);
            }

            if( doc.getElementById('ddelivery_widget_product') ){
                ddelivery_widget_product = doc.getElementById('ddelivery_widget_product');
                productId = ddelivery_widget_product.getAttribute("data");
                product_iframe = doc.createElement('iframe');
                if( ddelivery_widget_product.style.width !='' ){
                    product_iframe.style.width = ddelivery_widget_product.style.width;
                }else{
                    product_iframe.style.width = '250px';
                }
                product_iframe.style.height = '0px';
                product_iframe.style.overflow = 'hidden';
                product_iframe.scrolling = 'no';
                product_iframe.frameBorder = 0;
                product_iframe.style.borderWidth = 0;
                product_iframe.src = componentUrl + '&start_action=target_product' + '&product=' + productId; //+
                ddelivery_widget_product.appendChild(product_iframe);
                enableIframeListener(product_iframe, callbacksCityWidget);
            }


        }
    }

})(window,document);
DDeliveryWidget.init();

