/**
 * Created by mrozk on 06.12.14.
 */

var DDeliveryWidget = (function(w,doc) {
    var ddelivery_widget, ddelivery_widget_product;
    var componentUrl = w.__ddWdgtEnterPoint;
    if (componentUrl.indexOf('?') == -1) {
        componentUrl += '?dd_widget=1';
    } else {
        componentUrl += '&dd_widget=1';
    }
    var productId = w.__ddWdgtProductId;
    var cityNameContainer;

    var wrapperIframe, triangle;
    var staticUrl = w.__ddWdgtStatic;
    var product_iframe;


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
            console.log(event.data);
            var data = event.data;
           // eval('data = '+event.data);
            //var result;
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
            if( data.color != '' ){
                if( data.color == 'red'){
                    triangle.style.background = 'url("' + staticUrl + 'img/icons-sb02245f0c0.png")  0 -60px no-repeat';
                }else if(data.color == 'white'){
                    triangle.style.background = 'url("' + staticUrl + 'img/icons-sb02245f0c0.png")  0 -72px no-repeat';
                }
            }
        },
        close:function(data, iframe){
            wrapperIframe.style.display = 'none';
            //iframe.style.height = '0px';
        },
        geo:function(data, iframe){
            //console.log('client side');
            //console.log( data);
            cityNameContainer.innerHTML = decodeURI(data.name);
        },
        product_widget:function(data, iframe){
            if( ddelivery_widget_product ){
                //product_iframe.style.height = '0px';
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


    return {
        init:function(){
            if( doc.getElementById('ddelivery_widget') ) {
                ddelivery_widget = doc.getElementById('ddelivery_widget');
                var ddelivery_widget_iframe = doc.createElement('iframe');
                ddelivery_widget_iframe.style.width = '400px';
                ddelivery_widget_iframe.style.height = '0px';
                ddelivery_widget_iframe.style.overflow = 'hidden';
                ddelivery_widget_iframe.scrolling = 'no';
                ddelivery_widget_iframe.frameBorder = 0;
                ddelivery_widget_iframe.style.borderWidth = 0;
                ddelivery_widget_iframe.style.display = 'block';

                ddelivery_widget_iframe.style.mozBoxShadow = '0 0 8px 0 rgba(0, 0, 0, 0.5)';
                ddelivery_widget_iframe.style.webkitBoxShadow = '0 0 8px 0 rgba(0, 0, 0, 0.5)';
                ddelivery_widget_iframe.style.boxShadow = '0 0 8px 0 rgba(0, 0, 0, 0.5)';

                var iframeDiv = doc.createElement('div');
                iframeDiv.style.padding = '10px 8px 0px 8px';
                iframeDiv.style.position = 'absolute';
                iframeDiv.appendChild(ddelivery_widget_iframe);

                wrapperIframe = doc.createElement('div');
                wrapperIframe.style.display = 'block';
                wrapperIframe.style.position = 'relative';
                wrapperIframe.style.zIndex = '10000';
                wrapperIframe.appendChild(iframeDiv);

                triangle = doc.createElement('div');
                triangle.style.position = 'absolute';
                triangle.style.left = '30px';
                triangle.style.top = '3px';
                triangle.style.width = '16px';
                triangle.style.height = '7px';
                wrapperIframe.appendChild(triangle);

                cityNameContainer = doc.createElement('div');
                cityNameContainer.id = 'dd_city_name_container';
                cityNameContainer.style.fontSize = '16px';
                cityNameContainer.style.borderBottom = '1px dashed #000000';
                cityNameContainer.style.color = '#000000';
                cityNameContainer.style.display = 'inline-block';
                cityNameContainer.style.marginLeft = '15px';
                cityNameContainer.style.paddingTop = '10px';

                addEvent(cityNameContainer,'click',function(){
                    wrapperIframe.style.display = 'block';
                    if(ddelivery_widget_iframe.style.height == '0px'){
                        ddelivery_widget_iframe.src = componentUrl + '&start_action=demo_stand';
                    }
                });
                var date = new Date();
                var dateHash = date.getDate() + '_' + date.getFullYear();
                var ddLastWisit = getCookie('dd_last_wisit');
                if( (ddLastWisit == "") || ( ddLastWisit != dateHash) ){
                    ddelivery_widget_iframe.src = componentUrl + '&start_action=default';
                }else{
                    ddelivery_widget_iframe.src = componentUrl + '&start_action=geo';
                }
                setCookie('dd_last_wisit', dateHash ,100);

                ddelivery_widget.appendChild(cityNameContainer);
                ddelivery_widget.appendChild(wrapperIframe);
                enableIframeListener(ddelivery_widget_iframe, callbacksCityWidget);


                addEvent(doc.getElementsByTagName('body')[0], 'click', function(event){
                    if (!event.target) {
                        event.target = event.srcElement
                    }
                    if( event.target.id != 'dd_city_name_container' ){
                        wrapperIframe.style.display = 'none';
                    }
                });

            }

            if( doc.getElementById('ddelivery_widget_product') ){
                ddelivery_widget_product = doc.getElementById('ddelivery_widget_product');
                product_iframe = doc.createElement('iframe');
                if( ddelivery_widget_product.style.width !='' ){
                    product_iframe.style.width = ddelivery_widget_product.style.width;
                }
                product_iframe.style.height = '0px';
                product_iframe.style.overflow = 'hidden';
                product_iframe.scrolling = 'no';
                product_iframe.frameBorder = 0;
                product_iframe.style.borderWidth = 0;
                product_iframe.src = componentUrl + '&start_action=target_product' + '&product=' + productId; //+
                                    //'city=' + getCookie('dd_city') + '&product=' + productId;
                ddelivery_widget_product.appendChild(product_iframe);
                enableIframeListener(product_iframe, callbacksCityWidget);
            }

            /*
            doc.getElementsByTagName('body')[0].onclick = function(e){
                alert('xxx');
            };
            doc.getElementsByTagName('body')[0].onclick = function(e){
                alert('yyyy');
                e.preventDefault();
            };
            */
        }
    }

})(window,document);
DDeliveryWidget.init();