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
                var data = {
                    //orderId: 4, // Если у вас есть id заказа который изменяется, то укажите его в этом параметре
                    close: function(){
                        alert('Окно закрыто');
                    },
                    change: function(orderId, aboutDdelivery, amount) {
                        alert('Выбрали '+aboutDdelivery+ ' интернет магазину нужно взять с пользователя '+amount+' руб.');
                    }
                };
                DDelivery.delivery('ddelivery', 'ajax.php', data);
            }
            <?if(!empty($_GET['fast'])):?>
                DDeliveryStart();
            <?endif;?>
        </script>
    </body>
</html>