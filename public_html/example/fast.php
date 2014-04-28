<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <script src="../html/js/ddelivery.js"></script>
        <div id="ddelivery"></div>
        <div style="width: 100px; height: 300px"></div>

        <script>
            DDelivery.delivery('ddelivery', 'ajax.php<?isset($_GET['XDEBUG_SESSION_START'])?'XDEBUG_SESSION_START='.(int)$_GET['XDEBUG_SESSION_START']:''?>', {});
        </script>
    </body>
</html>