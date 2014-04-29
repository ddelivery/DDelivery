<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <script src="../html/js/ddelivery.js"></script>
        <div id="ddelivery"></div>
        <a href="javascript:DDeliveryStart()">Выбрать способ доставки</a>

        <script>
            function DDeliveryStart(){
                var params = {
                    //orderId: 4 // Если у вас есть id заказа который изменяется, то укажите его в этом параметре
                };
                var callback = {
                    close: function(){
                        alert('Окно закрыто');
                    },
                    change: function(data) {
                        
                        alert(data.comment+ ' интернет магазину нужно взять с пользователя '+' руб. OrderId: '+data.orderId);
                    }
                };
                DDelivery.delivery('ddelivery', 'ajax.php', params, callback);
            }
            <?if(!empty($_GET['fast'])):?>
                DDeliveryStart();
            <?endif;?>
        </script>
    </body>
</html>