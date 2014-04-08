

var DDelivery = {
    delivery: function(objectId, componentUrl, params) {
        var iframe = document.createElement('iframe');
        iframe.params = params;
        iframe.style.width = '1000px';
        iframe.style.height = '650px';
        iframe.style.overflow = 'hidden';
        iframe.scrolling = 'no';
        iframe.frameBorder = 0;
        iframe.style.borderWidth = 0;
        if(componentUrl.indexOf('?') == -1) {
            componentUrl+='?iframe=1';
        }else{
            componentUrl+='&iframe=1';
        }
        iframe.src = componentUrl;
        var object = document.getElementById(objectId);
        object.appendChild( iframe );


    }
};
