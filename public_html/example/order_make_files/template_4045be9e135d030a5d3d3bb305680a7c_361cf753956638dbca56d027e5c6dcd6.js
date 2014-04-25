
; /* Start:/js/colorbox/jquery.colorbox-min.js*/
/*!
	Colorbox v1.4.33 - 2013-10-31
	jQuery lightbox and modal window plugin
	(c) 2013 Jack Moore - http://www.jacklmoore.com/colorbox
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function(e,t,i){function o(i,o,n){var r=t.createElement(i);return o&&(r.id=Z+o),n&&(r.style.cssText=n),e(r)}function n(){return i.innerHeight?i.innerHeight:e(i).height()}function r(e){var t=k.length,i=(z+e)%t;return 0>i?t+i:i}function h(e,t){return Math.round((/%/.test(e)?("x"===t?E.width():n())/100:1)*parseInt(e,10))}function l(e,t){return e.photo||e.photoRegex.test(t)}function s(e,t){return e.retinaUrl&&i.devicePixelRatio>1?t.replace(e.photoRegex,e.retinaSuffix):t}function a(e){"contains"in g[0]&&!g[0].contains(e.target)&&(e.stopPropagation(),g.focus())}function d(){var t,i=e.data(N,Y);null==i?(B=e.extend({},X),console&&console.log&&console.log("Error: cboxElement missing settings object")):B=e.extend({},i);for(t in B)e.isFunction(B[t])&&"on"!==t.slice(0,2)&&(B[t]=B[t].call(N));B.rel=B.rel||N.rel||e(N).data("rel")||"nofollow",B.href=B.href||e(N).attr("href"),B.title=B.title||N.title,"string"==typeof B.href&&(B.href=e.trim(B.href))}function c(i,o){e(t).trigger(i),lt.triggerHandler(i),e.isFunction(o)&&o.call(N)}function u(i){q||(N=i,d(),k=e(N),z=0,"nofollow"!==B.rel&&(k=e("."+et).filter(function(){var t,i=e.data(this,Y);return i&&(t=e(this).data("rel")||i.rel||this.rel),t===B.rel}),z=k.index(N),-1===z&&(k=k.add(N),z=k.length-1)),w.css({opacity:parseFloat(B.opacity),cursor:B.overlayClose?"pointer":"auto",visibility:"visible"}).show(),J&&g.add(w).removeClass(J),B.className&&g.add(w).addClass(B.className),J=B.className,B.closeButton?K.html(B.close).appendTo(y):K.appendTo("<div/>"),U||(U=$=!0,g.css({visibility:"hidden",display:"block"}),H=o(st,"LoadedContent","width:0; height:0; overflow:hidden"),y.css({width:"",height:""}).append(H),O=x.height()+C.height()+y.outerHeight(!0)-y.height(),_=b.width()+T.width()+y.outerWidth(!0)-y.width(),D=H.outerHeight(!0),A=H.outerWidth(!0),B.w=h(B.initialWidth,"x"),B.h=h(B.initialHeight,"y"),H.css({width:"",height:B.h}),Q.position(),c(tt,B.onOpen),P.add(L).hide(),g.focus(),B.trapFocus&&t.addEventListener&&(t.addEventListener("focus",a,!0),lt.one(rt,function(){t.removeEventListener("focus",a,!0)})),B.returnFocus&&lt.one(rt,function(){e(N).focus()})),m())}function f(){!g&&t.body&&(V=!1,E=e(i),g=o(st).attr({id:Y,"class":e.support.opacity===!1?Z+"IE":"",role:"dialog",tabindex:"-1"}).hide(),w=o(st,"Overlay").hide(),F=e([o(st,"LoadingOverlay")[0],o(st,"LoadingGraphic")[0]]),v=o(st,"Wrapper"),y=o(st,"Content").append(L=o(st,"Title"),S=o(st,"Current"),I=e('<button type="button"/>').attr({id:Z+"Previous"}),R=e('<button type="button"/>').attr({id:Z+"Next"}),M=o("button","Slideshow"),F),K=e('<button type="button"/>').attr({id:Z+"Close"}),v.append(o(st).append(o(st,"TopLeft"),x=o(st,"TopCenter"),o(st,"TopRight")),o(st,!1,"clear:left").append(b=o(st,"MiddleLeft"),y,T=o(st,"MiddleRight")),o(st,!1,"clear:left").append(o(st,"BottomLeft"),C=o(st,"BottomCenter"),o(st,"BottomRight"))).find("div div").css({"float":"left"}),W=o(st,!1,"position:absolute; width:9999px; visibility:hidden; display:none; max-width:none;"),P=R.add(I).add(S).add(M),e(t.body).append(w,g.append(v,W)))}function p(){function i(e){e.which>1||e.shiftKey||e.altKey||e.metaKey||e.ctrlKey||(e.preventDefault(),u(this))}return g?(V||(V=!0,R.click(function(){Q.next()}),I.click(function(){Q.prev()}),K.click(function(){Q.close()}),w.click(function(){B.overlayClose&&Q.close()}),e(t).bind("keydown."+Z,function(e){var t=e.keyCode;U&&B.escKey&&27===t&&(e.preventDefault(),Q.close()),U&&B.arrowKey&&k[1]&&!e.altKey&&(37===t?(e.preventDefault(),I.click()):39===t&&(e.preventDefault(),R.click()))}),e.isFunction(e.fn.on)?e(t).on("click."+Z,"."+et,i):e("."+et).live("click."+Z,i)),!0):!1}function m(){var n,r,a,u=Q.prep,f=++at;$=!0,j=!1,N=k[z],d(),c(ht),c(it,B.onLoad),B.h=B.height?h(B.height,"y")-D-O:B.innerHeight&&h(B.innerHeight,"y"),B.w=B.width?h(B.width,"x")-A-_:B.innerWidth&&h(B.innerWidth,"x"),B.mw=B.w,B.mh=B.h,B.maxWidth&&(B.mw=h(B.maxWidth,"x")-A-_,B.mw=B.w&&B.w<B.mw?B.w:B.mw),B.maxHeight&&(B.mh=h(B.maxHeight,"y")-D-O,B.mh=B.h&&B.h<B.mh?B.h:B.mh),n=B.href,G=setTimeout(function(){F.show()},100),B.inline?(a=o(st).hide().insertBefore(e(n)[0]),lt.one(ht,function(){a.replaceWith(H.children())}),u(e(n))):B.iframe?u(" "):B.html?u(B.html):l(B,n)?(n=s(B,n),j=t.createElement("img"),e(j).addClass(Z+"Photo").bind("error",function(){B.title=!1,u(o(st,"Error").html(B.imgError))}).one("load",function(){var t;f===at&&(e.each(["alt","longdesc","aria-describedby"],function(t,i){var o=e(N).attr(i)||e(N).attr("data-"+i);o&&j.setAttribute(i,o)}),B.retinaImage&&i.devicePixelRatio>1&&(j.height=j.height/i.devicePixelRatio,j.width=j.width/i.devicePixelRatio),B.scalePhotos&&(r=function(){j.height-=j.height*t,j.width-=j.width*t},B.mw&&j.width>B.mw&&(t=(j.width-B.mw)/j.width,r()),B.mh&&j.height>B.mh&&(t=(j.height-B.mh)/j.height,r())),B.h&&(j.style.marginTop=Math.max(B.mh-j.height,0)/2+"px"),k[1]&&(B.loop||k[z+1])&&(j.style.cursor="pointer",j.onclick=function(){Q.next()}),j.style.width=j.width+"px",j.style.height=j.height+"px",setTimeout(function(){u(j)},1))}),setTimeout(function(){j.src=n},1)):n&&W.load(n,B.data,function(t,i){f===at&&u("error"===i?o(st,"Error").html(B.xhrError):e(this).contents())})}var w,g,v,y,x,b,T,C,k,E,H,W,F,L,S,M,R,I,K,P,B,O,_,D,A,N,z,j,U,$,q,G,Q,J,V,X={html:!1,photo:!1,iframe:!1,inline:!1,transition:"elastic",speed:300,fadeOut:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,href:!1,title:!1,rel:!1,opacity:.9,preloading:!0,className:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:void 0,closeButton:!0,fastIframe:!0,open:!1,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",photoRegex:/\.(gif|png|jp(e|g|eg)|bmp|ico|webp)((#|\?).*)?$/i,retinaImage:!1,retinaUrl:!1,retinaSuffix:"@2x.$1",current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",returnFocus:!0,trapFocus:!0,onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1},Y="colorbox",Z="cbox",et=Z+"Element",tt=Z+"_open",it=Z+"_load",ot=Z+"_complete",nt=Z+"_cleanup",rt=Z+"_closed",ht=Z+"_purge",lt=e("<a/>"),st="div",at=0,dt={},ct=function(){function e(){clearTimeout(h)}function t(){(B.loop||k[z+1])&&(e(),h=setTimeout(Q.next,B.slideshowSpeed))}function i(){M.html(B.slideshowStop).unbind(s).one(s,o),lt.bind(ot,t).bind(it,e),g.removeClass(l+"off").addClass(l+"on")}function o(){e(),lt.unbind(ot,t).unbind(it,e),M.html(B.slideshowStart).unbind(s).one(s,function(){Q.next(),i()}),g.removeClass(l+"on").addClass(l+"off")}function n(){r=!1,M.hide(),e(),lt.unbind(ot,t).unbind(it,e),g.removeClass(l+"off "+l+"on")}var r,h,l=Z+"Slideshow_",s="click."+Z;return function(){r?B.slideshow||(lt.unbind(nt,n),n()):B.slideshow&&k[1]&&(r=!0,lt.one(nt,n),B.slideshowAuto?i():o(),M.show())}}();e.colorbox||(e(f),Q=e.fn[Y]=e[Y]=function(t,i){var o=this;if(t=t||{},f(),p()){if(e.isFunction(o))o=e("<a/>"),t.open=!0;else if(!o[0])return o;i&&(t.onComplete=i),o.each(function(){e.data(this,Y,e.extend({},e.data(this,Y)||X,t))}).addClass(et),(e.isFunction(t.open)&&t.open.call(o)||t.open)&&u(o[0])}return o},Q.position=function(t,i){function o(){x[0].style.width=C[0].style.width=y[0].style.width=parseInt(g[0].style.width,10)-_+"px",y[0].style.height=b[0].style.height=T[0].style.height=parseInt(g[0].style.height,10)-O+"px"}var r,l,s,a=0,d=0,c=g.offset();if(E.unbind("resize."+Z),g.css({top:-9e4,left:-9e4}),l=E.scrollTop(),s=E.scrollLeft(),B.fixed?(c.top-=l,c.left-=s,g.css({position:"fixed"})):(a=l,d=s,g.css({position:"absolute"})),d+=B.right!==!1?Math.max(E.width()-B.w-A-_-h(B.right,"x"),0):B.left!==!1?h(B.left,"x"):Math.round(Math.max(E.width()-B.w-A-_,0)/2),a+=B.bottom!==!1?Math.max(n()-B.h-D-O-h(B.bottom,"y"),0):B.top!==!1?h(B.top,"y"):Math.round(Math.max(n()-B.h-D-O,0)/2),g.css({top:c.top,left:c.left,visibility:"visible"}),v[0].style.width=v[0].style.height="9999px",r={width:B.w+A+_,height:B.h+D+O,top:a,left:d},t){var u=0;e.each(r,function(e){return r[e]!==dt[e]?(u=t,void 0):void 0}),t=u}dt=r,t||g.css(r),g.dequeue().animate(r,{duration:t||0,complete:function(){o(),$=!1,v[0].style.width=B.w+A+_+"px",v[0].style.height=B.h+D+O+"px",B.reposition&&setTimeout(function(){E.bind("resize."+Z,Q.position)},1),i&&i()},step:o})},Q.resize=function(e){var t;U&&(e=e||{},e.width&&(B.w=h(e.width,"x")-A-_),e.innerWidth&&(B.w=h(e.innerWidth,"x")),H.css({width:B.w}),e.height&&(B.h=h(e.height,"y")-D-O),e.innerHeight&&(B.h=h(e.innerHeight,"y")),e.innerHeight||e.height||(t=H.scrollTop(),H.css({height:"auto"}),B.h=H.height()),H.css({height:B.h}),t&&H.scrollTop(t),Q.position("none"===B.transition?0:B.speed))},Q.prep=function(i){function n(){return B.w=B.w||H.width(),B.w=B.mw&&B.mw<B.w?B.mw:B.w,B.w}function h(){return B.h=B.h||H.height(),B.h=B.mh&&B.mh<B.h?B.mh:B.h,B.h}if(U){var a,d="none"===B.transition?0:B.speed;H.empty().remove(),H=o(st,"LoadedContent").append(i),H.hide().appendTo(W.show()).css({width:n(),overflow:B.scrolling?"auto":"hidden"}).css({height:h()}).prependTo(y),W.hide(),e(j).css({"float":"none"}),a=function(){function i(){e.support.opacity===!1&&g[0].style.removeAttribute("filter")}var n,h,a=k.length,u="frameBorder",f="allowTransparency";U&&(h=function(){clearTimeout(G),F.hide(),c(ot,B.onComplete)},L.html(B.title).add(H).show(),a>1?("string"==typeof B.current&&S.html(B.current.replace("{current}",z+1).replace("{total}",a)).show(),R[B.loop||a-1>z?"show":"hide"]().html(B.next),I[B.loop||z?"show":"hide"]().html(B.previous),ct(),B.preloading&&e.each([r(-1),r(1)],function(){var i,o,n=k[this],r=e.data(n,Y);r&&r.href?(i=r.href,e.isFunction(i)&&(i=i.call(n))):i=e(n).attr("href"),i&&l(r,i)&&(i=s(r,i),o=t.createElement("img"),o.src=i)})):P.hide(),B.iframe?(n=o("iframe")[0],u in n&&(n[u]=0),f in n&&(n[f]="true"),B.scrolling||(n.scrolling="no"),e(n).attr({src:B.href,name:(new Date).getTime(),"class":Z+"Iframe",allowFullScreen:!0,webkitAllowFullScreen:!0,mozallowfullscreen:!0}).one("load",h).appendTo(H),lt.one(ht,function(){n.src="//about:blank"}),B.fastIframe&&e(n).trigger("load")):h(),"fade"===B.transition?g.fadeTo(d,1,i):i())},"fade"===B.transition?g.fadeTo(d,0,function(){Q.position(0,a)}):Q.position(d,a)}},Q.next=function(){!$&&k[1]&&(B.loop||k[z+1])&&(z=r(1),u(k[z]))},Q.prev=function(){!$&&k[1]&&(B.loop||z)&&(z=r(-1),u(k[z]))},Q.close=function(){U&&!q&&(q=!0,U=!1,c(nt,B.onCleanup),E.unbind("."+Z),w.fadeTo(B.fadeOut||0,0),g.stop().fadeTo(B.fadeOut||0,0,function(){g.add(w).css({opacity:1,cursor:"auto"}).hide(),c(ht),H.empty().remove(),setTimeout(function(){q=!1,c(rt,B.onClosed)},1)}))},Q.remove=function(){g&&(g.stop(),e.colorbox.close(),g.stop().remove(),w.remove(),q=!1,g=null,e("."+et).removeData(Y).removeClass(et),e(t).unbind("click."+Z))},Q.element=function(){return e(N)},Q.settings=X)})(jQuery,document,window);
/* End */
;
; /* Start:/js/ddelivery/ddelivery.js*/

//if( typeof(DDeliveryBitrix) == 'undefined'){
// console.log('DDeliveryBitrix');    
DDeliveryBitrix = {
    rand: Math.random(),
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
        priceURL: '/bitrix/tools/ddelivery.php?action=getPrice',
        mediaURL: '/media/map'
    },
    isLoad: false,
    point_is_set: false,
    point_id: 0,
    cur_point: null,
    enablerefresh_order_form: true,
    params:[],
    PostomatsData:[],
    Init: function(params){
        //if(typeof(console) != 'undefined') console.log('init');
        top.DDeliveryBitrix.params = params; 
        // top.DDeliveryBitrix.params = top.DDeliveryParams;
        //if(typeof(console) != 'undefined') console.log(top.DDeliveryBitrix.params);    
        top.DDeliveryBitrix.options.customCallback = top.DDeliveryBitrix.onChangePoint;
        //if(typeof(console) != 'undefined') console.log('LoadPostomatsData');
        
        top.DDeliveryBitrix.LoadPostomatsData();
    },
    
    InitddEngine: function(){
            // if(typeof(console) != 'undefined') console.log('InitddEngine');
            // if(typeof(console) != 'undefined') console.log(top.DDeliveryBitrix);
            // if(typeof(console) != 'undefined') console.log(top.ddEngine);
            //if(typeof(ddEngine) == "undefined"){ ddEngine = {}; };
            //UpdateControlEvent();
            
            top.DDeliveryBitrix.isLoad = true;
            var radio = $('#ID_DELIVERY_DigitalDelivery_all');
            $('label[for=ID_DELIVERY_DigitalDelivery_all]').removeAttr('for');
            //$('#delivery_info_DigitalDelivery_all').hide();
            top.ddEngine.postomats = top.DDeliveryBitrix.PostomatsData;
            top.ddEngine.postomats = top.DDeliveryBitrix.PostomatsData;
            top.ddEngine.URLS = {
                price : top.DDeliveryBitrix.options.priceURL,
                media: 'http://ddelivery.creatiff.com.ua/media/'
            };


            top.ddEngine.inited = false;
            // $('#ddelivery_main').show();
            
            $('#ddelivery-container').show();    
  
            // if(true){
            // if(radio[0].checked){
                // if(typeof(console) != 'undefined') console.log('radio[0].checked');
                // $('#ddelivery_main').show();
                // $('#ddelivery-container').show();
                top.ddEngine.setData('width', top.DDeliveryBitrix.params.x);
                top.ddEngine.setData('height', top.DDeliveryBitrix.params.y);
                top.ddEngine.setData('length', top.DDeliveryBitrix.params.z);
                top.ddEngine.setData('weight', top.DDeliveryBitrix.params.w);
                top.ddEngine.showMap( top.DDeliveryBitrix.options );
                // top.ddEngine.setData('declared_price', params.p);
            // }else{
            //     radio.bind('change', function(){
            //         $('#ddelivery-container').show();
            //         top.ddEngine.postomats = top.DDeliveryBitrix.PostomatsData;
            //         top.ddEngine.setData('width', top.DDeliveryBitrix.params.x);
            //         top.ddEngine.setData('height', top.DDeliveryBitrix.params.y);
            //         top.ddEngine.setData('length', top.DDeliveryBitrix.params.z);
            //         top.ddEngine.setData('weight', top.DDeliveryBitrix.params.w);
            //         top.ddEngine.showMap( top.DDeliveryBitrix.options );
            //     });
            // }
            
            /*
            if( top.DDeliveryBitrix.point_is_set ){
                top.DDeliveryBitrix.enablerefresh_order_form = false;
                if(typeof(console) != 'undefined') console.log('point_is_set');
                if( typeof( top.DDeliveryBitrix.cur_point) != 'undefined' ){
                    if(typeof(console) != 'undefined') console.log(top.DDeliveryBitrix.cur_point);
                    top.top.ddEngine.executeCusomCallbackForPoint( top.DDeliveryBitrix.cur_point );
                }else{
                    top.DDeliveryBitrix.showPointByID( top.DDeliveryBitrix.point_id );
                }
            }
            */
            
            
    },
    
    showMap: function(){
        $('#'+top.DDeliveryBitrix.options.mainContainer).show();
        $('#'+top.DDeliveryBitrix.options.resultContainer).hide();
    },
    
    onChangePoint: function(point){
        $.getJSON('/bitrix/tools/ddelivery.php', {action: 'setPoint', id: point._id}, function(){
            // if(typeof(console) != 'undefined') console.log('onChangePoint');
            $('#DDELIVERY_OPT_POINT_ID').remove();
            $('<input type="hidden" name="DDELIVERY_OPT[POINT_ID]" id="DDELIVERY_OPT_POINT_ID" value="'+point._id+'">').insertAfter("#sessid");
            top.DDeliveryBitrix.point_is_set = true;
            top.DDeliveryBitrix.cur_point = point;
            top.DDeliveryBitrix.point_id = point._id;
            if(top.DDeliveryBitrix.enablerefresh_order_form){
                submitForm(); // форма битрикса
            } else {
                top.DDeliveryBitrix.enablerefresh_order_form = true;
            }
        });
        
       top.DDeliveryBitrix.showPoint( point );
        
    },
    
    showPoint: function(point){
        $('.ddelivery-customer-city').hide();
        $('.ddelivery-filter-additional-block').hide();

        $('[map-wrapper]').hide();
        $('.ddelivery-result').show();
        $('[point-name]').html(point.name + ' #' + point._id);
        $('[city-name]').html(point.city.name);
        $('[price-holder]').html(point.price + ' руб.');
        $('[time-holder]').html(point.time + ' дн.');
        $('.ddelivery-result').find('.ddelivery-item-info2').html( $('#info_container').find('.ddelivery-item-info2').html() );
    },
    
    LoadPostomatsData:function(){
        if( top.DDeliveryBitrix.PostomatsData.length < 1 ){              
            $.post(
                "/bitrix/tools/ddelivery.php?action=getPoints", 
                {action:"getPoints",x:top.DDeliveryBitrix.params.x,y:top.DDeliveryBitrix.params.y,z:top.DDeliveryBitrix.params.z,w:top.DDeliveryBitrix.params.w,t:Math.random()},
                function(data){ 
                    // if(typeof(console) != 'undefined') console.log('DataLoaded');
                    // if(typeof(console) != 'undefined') console.log(data);
                    top.DDeliveryBitrix.PostomatsData = data.response;
                    if(typeof(top.top.ddEngine) == 'undefined') {
                        top.top.ddEngine = {}
                        top.top.ddEngine.postomats = {}
                    }
                    top.top.ddEngine.postomats = data.response;
                    top.top.ddEngine.postomats = data.response;
                    top.DDeliveryBitrix.InitddEngine();
                },
                "json"
            );
        }else{
             top.DDeliveryBitrix.InitddEngine();
        };
    },
    
    showPointByID: function(id){
        for (var i=0; i<=top.ddEngine.postomats.length-1; i++){
              if (top.ddEngine.postomats[i]._id==id){
                point =  top.ddEngine.postomats[i];
                //if(typeof(console) != 'undefined') console.log( point );
                top.ddEngine.executeCusomCallbackForPoint( point );
                top.DDeliveryBitrix.showPoint( point );
              }
        }
    },
    
    set_point: function(id){
        top.DDeliveryBitrix.point_is_set = true;
        top.DDeliveryBitrix.point_id = id;
    }
};
//};


/* End */
;
; /* Start:/js/ddelivery/colorbox.init.js*/
$(function(){
    $(".delivery-map").colorbox({
        "innerWidth"  : "90%",
        "innerHeight" : "90%",
        "onComplete" : function() {
			colorbox = $("#cboxLoadedContent");
			containerDDelivery = $("#ddelivery-container");
			containerMap = $("#map_container");

			width  = colorbox.width();
			height = colorbox.height();

			containerDDelivery.width(width-54);
			containerDDelivery.height(height-15);
			containerMap.height(height - 70);
			$("#info_container").height(height - 70);
			
			DDeliveryBitrix.Init({
				"x":1,
				"y":1,
				"z":1,
				"w":1
			})

        }

    });
})




/* End */
;
; /* Start:/bitrix/templates/eshop_adapt_blue/script.js*/
function eshopOpenNativeMenu()
{
	var native_menu = BX("bx_native_menu");
	var is_menu_active = BX.hasClass(native_menu, "active");

	if (is_menu_active)
	{
		BX.removeClass(native_menu, "active");
		BX.removeClass(BX('bx_menu_bg'), "active");
		BX("bx_eshop_wrap").style.position = "";
		BX("bx_eshop_wrap").style.top = "";
		BX("bx_eshop_wrap").style.overflow = "";
	}
	else
	{
		BX.addClass(native_menu, "active");
		BX.addClass(BX('bx_menu_bg'), "active");
		var topHeight = document.body.scrollTop;
		BX("bx_eshop_wrap").style.position = "fixed";
		BX("bx_eshop_wrap").style.top = -topHeight+"px";
		BX("bx_eshop_wrap").style.overflow = "hidden";
	}

	var easing = new BX.easing({
		duration : 300,
		start : { left : (is_menu_active) ? 0 : -100 },
		finish : { left : (is_menu_active) ? -100 : 0 },
		transition : BX.easing.transitions.quart,
		step : function(state){
			native_menu.style.left = state.left + "%";
		}
	});
	easing.animate();
}

window.addEventListener('resize',
	function() {
		if (window.innerWidth >= 640 && BX.hasClass(BX("bx_native_menu"), "active"))
			eshopOpenNativeMenu();
	},
	false
);

/* End */
;
; /* Start:/bitrix/templates/eshop_adapt_blue/components/bitrix/sale.basket.basket.line/eshop_adapt/script.js*/
function JSEshopBasket(ajaxPath, site_id)
{
	this.ajaxPath = ajaxPath;
	this.site_id = site_id;

	var curObj = this;
	BX.addCustomEvent(window, "OnBasketChange", function() {
		curObj.OnBasketChangeHandler();
	});
}

JSEshopBasket.prototype.OnBasketChangeHandler = function()
{
	BX.ajax.post(
		this.ajaxPath,
		{
			sessid: BX.bitrix_sessid(),
			basketChange: "Y",
			site_id: this.site_id
		},
		function(num_products)
		{
			if (document.getElementById('bx_cart_num'))
				document.getElementById('bx_cart_num').innerHTML = (num_products > 0) ? " ("+num_products+")" : "";
		}
	);
}
/* End */
;
; /* Start:/bitrix/components/bitrix/search.title/script.js*/
function JCTitleSearch(arParams)
{
	var _this = this;

	this.arParams = {
		'AJAX_PAGE': arParams.AJAX_PAGE,
		'CONTAINER_ID': arParams.CONTAINER_ID,
		'INPUT_ID': arParams.INPUT_ID,
		'MIN_QUERY_LEN': parseInt(arParams.MIN_QUERY_LEN)
	};
	if(arParams.WAIT_IMAGE)
		this.arParams.WAIT_IMAGE = arParams.WAIT_IMAGE;
	if(arParams.MIN_QUERY_LEN <= 0)
		arParams.MIN_QUERY_LEN = 1;

	this.cache = [];
	this.cache_key = null;

	this.startText = '';
	this.currentRow = -1;
	this.RESULT = null;
	this.CONTAINER = null;
	this.INPUT = null;
	this.WAIT = null;

	this.ShowResult = function(result)
	{
		var pos = BX.pos(_this.CONTAINER);
		pos.width = pos.right - pos.left;
		_this.RESULT.style.position = 'absolute';
		_this.RESULT.style.top = (pos.bottom + 2) + 'px';
		_this.RESULT.style.left = pos.left + 'px';
		_this.RESULT.style.width = pos.width + 'px';
		if(result != null)
			_this.RESULT.innerHTML = result;

		if(_this.RESULT.innerHTML.length > 0)
			_this.RESULT.style.display = 'block';
		else
			_this.RESULT.style.display = 'none';

		//ajust left column to be an outline
		var th;
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl) th = BX.findChild(tbl, {'tag':'th'}, true);
		if(th)
		{
			var tbl_pos = BX.pos(tbl);
			tbl_pos.width = tbl_pos.right - tbl_pos.left;

			var th_pos = BX.pos(th);
			th_pos.width = th_pos.right - th_pos.left;
			th.style.width = th_pos.width + 'px';

			_this.RESULT.style.width = (pos.width + th_pos.width) + 'px';

			//Move table to left by width of the first column
			_this.RESULT.style.left = (pos.left - th_pos.width - 1)+ 'px';

			//Shrink table when it's too wide
			if((tbl_pos.width - th_pos.width) > pos.width)
				_this.RESULT.style.width = (pos.width + th_pos.width -1) + 'px';

			//Check if table is too wide and shrink result div to it's width
			tbl_pos = BX.pos(tbl);
			var res_pos = BX.pos(_this.RESULT);
			if(res_pos.right > tbl_pos.right)
			{
				_this.RESULT.style.width = (tbl_pos.right - tbl_pos.left) + 'px';
			}
		}

		var fade;
		if(tbl) fade = BX.findChild(_this.RESULT, {'class':'title-search-fader'}, true);
		if(fade && th)
		{
			res_pos = BX.pos(_this.RESULT);
			fade.style.left = (res_pos.right - res_pos.left - 18) + 'px';
			fade.style.width = 18 + 'px';
			fade.style.top = 0 + 'px';
			fade.style.height = (res_pos.bottom - res_pos.top) + 'px';
			fade.style.display = 'block';
		}
	}

	this.onKeyPress = function(keyCode)
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(!tbl)
			return false;

		var cnt = tbl.rows.length;

		switch (keyCode)
		{
		case 27: // escape key - close search div
			_this.RESULT.style.display = 'none';
			_this.currentRow = -1;
			_this.UnSelectAll();
		return true;

		case 40: // down key - navigate down on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var first = -1;
			for(var i = 0; i < cnt; i++)
			{
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					if(first == -1)
						first = i;

					if(_this.currentRow < i)
					{
						_this.currentRow = i;
						break;
					}
					else if(tbl.rows[i].className == 'title-search-selected')
					{
						tbl.rows[i].className = '';
					}
				}
			}

			if(i == cnt && _this.currentRow != i)
				_this.currentRow = first;

			tbl.rows[_this.currentRow].className = 'title-search-selected';
		return true;

		case 38: // up key - navigate up on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var last = -1;
			for(var i = cnt-1; i >= 0; i--)
			{
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					if(last == -1)
						last = i;

					if(_this.currentRow > i)
					{
						_this.currentRow = i;
						break;
					}
					else if(tbl.rows[i].className == 'title-search-selected')
					{
						tbl.rows[i].className = '';
					}
				}
			}

			if(i < 0 && _this.currentRow != i)
				_this.currentRow = last;

			tbl.rows[_this.currentRow].className = 'title-search-selected';
		return true;

		case 13: // enter key - choose current search result
			if(_this.RESULT.style.display == 'block')
			{
				for(var i = 0; i < cnt; i++)
				{
					if(_this.currentRow == i)
					{
						if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
						{
							var a = BX.findChild(tbl.rows[i], {'tag':'a'}, true);
							if(a)
							{
								window.location = a.href;
								return true;
							}
						}
					}
				}
			}
		return false;
		}

		return false;
	}

	this.onTimeout = function()
	{
		if(_this.INPUT.value != _this.oldValue && _this.INPUT.value != _this.startText)
		{
			if(_this.INPUT.value.length >= _this.arParams.MIN_QUERY_LEN)
			{
				_this.oldValue = _this.INPUT.value;
				_this.cache_key = _this.arParams.INPUT_ID + '|' + _this.INPUT.value;
				if(_this.cache[_this.cache_key] == null)
				{
					if(_this.WAIT)
					{
						var pos = BX.pos(_this.INPUT);
						var height = (pos.bottom - pos.top)-2;
						_this.WAIT.style.top = (pos.top+1) + 'px';
						_this.WAIT.style.height = height + 'px';
						_this.WAIT.style.width = height + 'px';
						_this.WAIT.style.left = (pos.right - height + 2) + 'px';
						_this.WAIT.style.display = 'block';
					}

					BX.ajax.post(
						_this.arParams.AJAX_PAGE,
						{
							'ajax_call':'y',
							'INPUT_ID':_this.arParams.INPUT_ID,
							'q':_this.INPUT.value,
							'l':_this.arParams.MIN_QUERY_LEN
						},
						function(result)
						{
							_this.cache[_this.cache_key] = result;
							_this.ShowResult(result);
							_this.currentRow = -1;
							_this.EnableMouseEvents();
							if(_this.WAIT)
								_this.WAIT.style.display = 'none';
							setTimeout(_this.onTimeout, 500);
						}
					);
				}
				else
				{
					_this.ShowResult(_this.cache[_this.cache_key]);
					_this.currentRow = -1;
					_this.EnableMouseEvents();
					setTimeout(_this.onTimeout, 500);
				}
			}
			else
			{
				_this.RESULT.style.display = 'none';
				_this.currentRow = -1;
				_this.UnSelectAll();
				setTimeout(_this.onTimeout, 500);
			}
		}
		else
		{
			setTimeout(_this.onTimeout, 500);
		}
	}

	this.UnSelectAll = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				tbl.rows[i].className = '';
		}
	}

	this.EnableMouseEvents = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					tbl.rows[i].id = 'row_' + i;
					tbl.rows[i].onmouseover = function (e) {
						if(_this.currentRow != this.id.substr(4))
						{
							_this.UnSelectAll();
							this.className = 'title-search-selected';
							_this.currentRow = this.id.substr(4);
						}
					};
					tbl.rows[i].onmouseout = function (e) {
						this.className = '';
						_this.currentRow = -1;
					};
				}
		}
	}

	this.onFocusLost = function(hide)
	{
		setTimeout(function(){_this.RESULT.style.display = 'none';}, 250);
	}

	this.onFocusGain = function()
	{
		if(_this.RESULT.innerHTML.length)
			_this.ShowResult();
	}

	this.onKeyDown = function(e)
	{
		if(!e)
			e = window.event;

		if (_this.RESULT.style.display == 'block')
		{
			if(_this.onKeyPress(e.keyCode))
				return BX.PreventDefault(e);
		}
	}

	this.Init = function()
	{
		this.CONTAINER = document.getElementById(this.arParams.CONTAINER_ID);
		this.RESULT = document.body.appendChild(document.createElement("DIV"));
		this.RESULT.className = 'title-search-result';
		this.INPUT = document.getElementById(this.arParams.INPUT_ID);
		this.startText = this.oldValue = this.INPUT.value;
		BX.bind(this.INPUT, 'focus', function() {_this.onFocusGain()});
		BX.bind(this.INPUT, 'blur', function() {_this.onFocusLost()});

		if(BX.browser.IsSafari() || BX.browser.IsIE())
			this.INPUT.onkeydown = this.onKeyDown;
		else
			this.INPUT.onkeypress = this.onKeyDown;

		if(this.arParams.WAIT_IMAGE)
		{
			this.WAIT = document.body.appendChild(document.createElement("DIV"));
			this.WAIT.style.backgroundImage = "url('" + this.arParams.WAIT_IMAGE + "')";
			if(!BX.browser.IsIE())
				this.WAIT.style.backgroundRepeat = 'none';
			this.WAIT.style.display = 'none';
			this.WAIT.style.position = 'absolute';
			this.WAIT.style.zIndex = '1100';
		}

		setTimeout(this.onTimeout, 500);
	}

	BX.ready(function (){_this.Init(arParams)});
}

/* End */
;
; /* Start:/bitrix/components/bitrix/menu/templates/catalog_horizontal/script.js*/
(function(window) {

	if (!window.BX || BX.CatalogMenu)
		return;

	BX.CatalogMenu = {
		items : {},
		idCnt : 1,
		currentItem : null,
		overItem : null,
		outItem : null,
		timeoutOver : null,
		timeoutOut : null,

		getItem : function(item)
		{
			if (!BX.type.isDomNode(item))
				return null;

			var id = !item.id || !BX.type.isNotEmptyString(item.id) ? (item.id = "menu-item-" + this.idCnt++) : item.id;

			if (!this.items[id])
				this.items[id] = new CatalogMenuItem(item);

			return this.items[id];
		},

		itemOver : function(item)
		{
			var menuItem = this.getItem(item);
			if (!menuItem)
				return;

			if (this.outItem == menuItem)
			{
				clearTimeout(menuItem.timeoutOut);
			}

			this.overItem = menuItem;

			if (menuItem.timeoutOver)
			{
				clearTimeout(menuItem.timeoutOver);
			}

			menuItem.timeoutOver = setTimeout(function() {
				if (BX.CatalogMenu.overItem == menuItem)
				{
					menuItem.itemOver();
				}

			}, 100);
		},

		itemOut : function(item)
		{
			var menuItem = this.getItem(item);
			if (!menuItem)
				return;

			this.outItem = menuItem;

			if (menuItem.timeoutOut)
			{
				clearTimeout(menuItem.timeoutOut);
			}

			menuItem.timeoutOut = setTimeout(function() {

				if (menuItem != BX.CatalogMenu.overItem)
				{
					menuItem.itemOut();
				}
				if (menuItem == BX.CatalogMenu.outItem)
				{
					menuItem.itemOut();
				}

			}, 100);
		}
	};

	var CatalogMenuItem = function(item)
	{
		this.element = item;
		this.popup = BX.findChild(item, { className: "bx_children_container" }, false, false);
		this.isLastItem = BX.lastChild(this.element.parentNode) == this.element;
	};

	CatalogMenuItem.prototype.itemOver = function()
	{
		if (!BX.hasClass(this.element, "hover"))
		{
			BX.addClass(this.element, "hover");
			this.alignPopup();
		}
	};

	CatalogMenuItem.prototype.itemOut = function()
	{
		BX.removeClass(this.element, "hover");
	};

	CatalogMenuItem.prototype.alignPopup = function()
	{
		if (!this.popup)
			return;

		this.popup.style.cssText = "";

		var ulContainer = this.element.parentNode;
		var offsetRightPopup = this.popup.offsetLeft + this.popup.offsetWidth;
		var offsetRightMenu = ulContainer.offsetLeft + ulContainer.offsetWidth;

		if (offsetRightPopup >= offsetRightMenu)
		{
			this.popup.style.right = /*this.isLastItem ? "0px" :*/ "0";
		}
	};
})(window);


function menuCatalogResize(menuID, menuFirstWidth)
{
	var wpasum = 0; // sum of width for all li

	var firstLevelLi = BX.findChildren(BX(menuID), {className : "bx_hma_one_lvl"}, true);

	if (firstLevelLi)
	{
		for(var i = 0; i < firstLevelLi.length; i++)
		{
			var wpa = BX.firstChild(firstLevelLi[i]).clientWidth;
			wpasum += wpa;
		}

		if(menuFirstWidth && (wpasum+20) <= menuFirstWidth)
			BX.addClass(BX(menuID), "small");   //adaptive
		else
			BX.removeClass(BX(menuID), "small");
	}

	return wpasum;
}

function menuCatalogAlign(menuID)
{
	var firstLevelLi = BX.findChildren(BX(menuID), {className : "bx_hma_one_lvl"}, true);
	var wpsum = 0;

	if (firstLevelLi)
	{
		for(var i = 0; i < firstLevelLi.length; i++)
		{
			firstLevelLi[i].removeAttribute("style");
			var wp = firstLevelLi[i].clientWidth;
			wpsum = wpsum+wp;
		}

		var cof_width = wpsum/100;

		for(var i = 0; i < firstLevelLi.length; i++)
		{
			wp = firstLevelLi[i].clientWidth;
			firstLevelLi[i].style.width = (wp/cof_width)+"%";
		}
	}
}

function menuCatalogPadding(menuID)
{
	var firstLevelLi = BX.findChildren(BX(menuID), {className : "bx_hma_one_lvl"}, true);
	var wpsum = 0;

	if (firstLevelLi)
	{
		for(var i = 0; i < firstLevelLi.length; i++)
		{
			BX.firstChild(firstLevelLi[i]).style.padding = "19px 10px";
		}
	}
}

function menuCatalogChangeSectionPicure(element)
{
	var curImgWrapObj = BX.nextSibling(element);
	var curImgObj = BX.clone(BX.firstChild(curImgWrapObj));
	var curDescr = element.getAttribute("data-description");
	var parentObj = BX.hasClass(element, 'bx_hma_one_lvl') ? element : BX.findParent(element, {className:'bx_hma_one_lvl'});
	var sectionImgObj = BX.findChild(parentObj, {className:'bx_section_picture'}, true, false);
	sectionImgObj.innerHTML = "";
	sectionImgObj.appendChild(curImgObj);
	var sectionDescrObj = BX.findChild(parentObj, {className:'bx_section_description'}, true, false);
	sectionDescrObj.innerHTML = curDescr;
	BX.previousSibling(sectionDescrObj).innerHTML = element.innerHTML;
	sectionImgObj.parentNode.href = element.href;
}
/* End */
;
; /* Start:/bitrix/js/main/cphttprequest.js*/
function PShowWaitMessage(container_id, bHide)
{
	if (bHide == null) bHide = false;
	PCloseWaitMessage(container_id, bHide);

	var obContainer = document.getElementById(container_id);

	if (obContainer)
	{
		if (window.ajaxMessages == null) window.ajaxMessages = {};
		if (!window.ajaxMessages.wait) window.ajaxMessages.wait = 'Wait...';

		obContainer.innerHTML = window.ajaxMessages.wait;

		if (bHide) obContainer.style.display = 'inline';
	}
}

function PCloseWaitMessage(container_id, bHide)
{
	if (bHide == null) bHide = false;

	var obContainer = document.getElementById(container_id);

	if (obContainer)
	{
		obContainer.innerHTML = '';

		if (bHide) obContainer.style.display = 'none';
	}

}

function JCPHttpRequest()
{
	this.Action = {}; //{TID:function(result){}}

	this.InitThread = function()
	{
		while (true)
		{
			var TID = 'TID' + Math.floor(Math.random() * 1000000);
			if (!this.Action[TID]) break;
		}

		return TID;
	}

	this.SetAction = function(TID, actionHandler)
	{
		this.Action[TID] = actionHandler;
	}

	this._Close = function(TID, httpRequest)
	{
		if (this.Action[TID]) this.Action[TID] = null;
//		httpRequest.onreadystatechange = null;
		httpRequest = null;
	}

	this._OnDataReady = function(TID, result)
	{
		if(this.Action[TID])
		{
			this.Action[TID](result);
		}
	}

	this._CreateHttpObject = function()
	{
		var obj = null;
		if(window.XMLHttpRequest)
		{
			try {obj = new XMLHttpRequest();} catch(e){}
		}
        else if(window.ActiveXObject)
        {
            try {obj = new ActiveXObject("Microsoft.XMLHTTP");} catch(e){}
            if(!obj)
            	try {obj = new ActiveXObject("Msxml2.XMLHTTP");} catch (e){}
        }
        return obj;
	}

	this._SetHandler = function(TID, httpRequest)
	{
		var _this = this;

		function __handlerReadyStateChange()
		{
			//alert(httpRequest.readyState);
			if(httpRequest.readyState == 4)
			{
//				try
//				{
					var s = httpRequest.responseText;
					var code = [];
					var start;
					
					while((start = s.indexOf('<script>')) != -1)
					{
						var end = s.indexOf('</script>', start);
						if(end != -1)
						{
							code[code.length] = s.substr(start+8, end-start-8);
							s = s.substr(0, start) + s.substr(end+9);
						}
						else
						{
							s = s.substr(0, start) + s.substr(start+8);
						}
					}
					
					_this._OnDataReady(TID, s);

					for(var i in code)
						if(code[i] != '')
							eval(code[i]);
//				}
//				catch (e)
//				{
//					var w = window.open("about:blank");
//					w.document.write(httpRequest.responseText);
//					//w.document.close();
//				}

				_this._Close(TID, httpRequest);
			}
			//alert('done');
		}

		httpRequest.onreadystatechange = __handlerReadyStateChange;
	}

	this._MyEscape = function(str)
	{
		return escape(str).replace(/\+/g, '%2B');
	}

	this._PrepareData = function(arData, prefix)
	{
		var data = '';
		if (arData != null)
		{
			for(var i in arData)
			{
				if (data.length > 0) data += '&';
				var name = this._MyEscape(i);
				if(prefix)
					name = prefix + '[' + name + ']';
				if(typeof arData[i] == 'object')
					data += this._PrepareData(arData[i], name)
				else
					data += name + '=' + this._MyEscape(arData[i])
			}
		}
		return data;
	}

	this.Send = function(TID, url, arData)
	{
		if (arData != null)
			var data = this._PrepareData(arData);

		if (data.length > 0)
		{
			if (url.indexOf('?') == -1)
		 		url += '?' + data;
		 	else
				url += '&' + data;	
		}

		var httpRequest = this._CreateHttpObject();
		if(httpRequest)
		{
			httpRequest.open("GET", url, true);
			this._SetHandler(TID, httpRequest);
			return httpRequest.send("");
  		}
  		return false;
	}

	this.Post = function(TID, url, arData)
	{
		var data = '';

		if (arData != null)
			data = this._PrepareData(arData);

		var httpRequest = this._CreateHttpObject();
		if(httpRequest)
		{
			httpRequest.open("POST", url, true);
			this._SetHandler(TID, httpRequest);
			httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			return httpRequest.send(data);
  		}
  		return false;
	}

	this.__migrateSetHandler = function(obForm, obFrame, handler)
	{
		function __formResultHandler()
		{
			if (!obFrame.contentWindow.document || obFrame.contentWindow.document.body.innerHTML.length == 0) return;
			if (null != handler) 
				handler(obFrame.contentWindow.document.body.innerHTML);
			
			// uncomment next to return form back after first query
			
			/*
			obForm.target = '';
			obForm.removeChild(obForm.lastChild);
			document.body.removeChild(obFrame);
			*/
		}
		
		if (obFrame.addEventListener) 
		{
			obFrame.addEventListener("load", __formResultHandler, false);
		}
		else if (obFrame.attachEvent) 
		{
			obFrame.attachEvent("onload", __formResultHandler);
		}
	}
	
	this.MigrateFormToAjax = function(obForm, handler)
	{
		if (!obForm) 
			return;
		if (obForm.target && obForm.target.substring(0, 5) == 'AJAX')
			return;
		
		var obAJAXIndicator = document.createElement('INPUT');
		obAJAXIndicator.type = 'hidden';
		obAJAXIndicator.name = 'AJAX_CALL';
		obAJAXIndicator.value = 'Y';
		
		obForm.appendChild(obAJAXIndicator);
		
		var frameName = 'AJAX_' + Math.round(Math.random() * 100000);
		
		if (document.getElementById('frameName'))
			var obFrame = document.getElementById('frameName');
		else
		{
			if (currentBrowserDetected == 'IE')
				var obFrame = document.createElement('<iframe name="' + frameName + '"></iframe>');
			else
				var obFrame = document.createElement('IFRAME');
			
			obFrame.style.display = 'none';
			obFrame.src = '';
			obFrame.id = frameName;
			obFrame.name = frameName;
			
			document.body.appendChild(obFrame);
		}
		
		obForm.target = frameName;
		
		this.__migrateSetHandler(obForm, obFrame, handler);
	}
}

var CPHttpRequest = new JCPHttpRequest();

var currentBrowserDetected = "";
if (window.opera)
	currentBrowserDetected = "Opera";
else if (navigator.userAgent)
{
	if (navigator.userAgent.indexOf("MSIE") != -1)
		currentBrowserDetected = "IE";
	else if (navigator.userAgent.indexOf("Firefox") != -1)
		currentBrowserDetected = "Firefox";
}
		

/* End */
;; /* /js/colorbox/jquery.colorbox-min.js*/
; /* /js/ddelivery/ddelivery.js*/
; /* /js/ddelivery/colorbox.init.js*/
; /* /bitrix/templates/eshop_adapt_blue/script.js*/
; /* /bitrix/templates/eshop_adapt_blue/components/bitrix/sale.basket.basket.line/eshop_adapt/script.js*/
; /* /bitrix/components/bitrix/search.title/script.js*/
; /* /bitrix/components/bitrix/menu/templates/catalog_horizontal/script.js*/
; /* /bitrix/js/main/cphttprequest.js*/
