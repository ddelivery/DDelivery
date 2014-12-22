<htm>
<body style="background-color: red">

<div id="ddelivery_traking"></div>
<div id="ddelivery_widget"></div>
<div style="margin-top: 400px;" id="ddelivery_widget_product"></div>

<script type="text/javascript">
    (function(w, doc){
        if (!w.__ddWdgt ) {
            w.__ddWdgt = true;
            w.__ddWdgtStatic = '/ddelivery/html/widget/';
            w.__ddWdgtEnterPoint = 'ajax.php';
            w.__ddWdgtProductId = encodeURI( w.location.href );  // для идентификации продукта, это для виджета из карточки товара
            var d = doc, s = d.createElement('script'), g = 'getElementsByTagName';
            s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
            s.src = w.__ddWdgtStatic + 'widget.js';
            var h=d[g]('body')[0];
            h.appendChild(s);
        }
    } )(window,document);
</script>
</body>
</htm>