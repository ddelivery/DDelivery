
Ура!1
<div id="ddelivery"></div>

<script>
    var DDelivery = {
        componentUrl: "",
        staticPath: "",
        init: function(componentUrl, staticPath) {
            if(staticPath.length > 0 && staticPath.substr(staticPath.length-2, 1) != '/'){
                staticPath += '/';
            }
            this.staticPath = staticPath;
            this.componentUrl = componentUrl;
            if(typeof($) != 'undefined' && typeof($.fn) != 'undefined' && typeof($.fn.jquery) != 'undefined' && $.fn.jquery) {

            }else{
                var ref$ = typeof($) != 'undefined' ? $ : undefined;
                this.requireScript(
                    '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js',
                    function(){
                        $.noConflict();
                        $ = ref$;
                    }
                );

            }

            this.requireScript(this.staticPath+'js/start.js');
            this.requireStyle(this.staticPath+'css/start.js');
        },
        requireScript: function(url, onload) {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = url;
            if(typeof(onload) == "function"){
                script.onload = onload;
            }
            document.childNodes[0].appendChild(script);
        },
        requireStyle: function(url, onload) {
            var link = document.createElement('link');
            link.type = 'text/css';
            link.rel = 'stylesheet';
            link.href = url;
            if(typeof(onload) == "function"){
                link.onload = onload;
            }
            document.childNodes[0].appendChild(link);
        }
    };
    DDelivery.init('ajax.php', '');
</script>

