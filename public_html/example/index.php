<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript" src="assets/jquery.the-modal.js"></script>
        <link rel="stylesheet" type="text/css" href="assets/the-modal.css" media="all">
        <link rel="stylesheet" type="text/css" href="assets/demo-modals.css" media="all">
    </head>
    <body>
        <script src="../html/js/ddelivery.js"></script>

        <a href="javascript:void(0)" class="trigger">Выбрать способ доставки</a>

        <div class="modal" id="test-modal" style="display: none">
            <div id="ddelivery"></div>
            <?php
            /*
            ?>
            <a href="javascript:DDeliveryStart()">Выбрать способ доставки</a>
            <?php
            */
            ?>
        </div>





        <script>
            // attach modal close handler
            function closePopup()
            {
                jQuery(function($){
                    $.modal().close();
                })
            }
            function DDeliveryStart(){

                jQuery('#test-modal').modal().open();

                var params = {
                    //orderId: 4 // Если у вас есть id заказа который изменяется, то укажите его в этом параметре
                    //displayContactForm: false
                };
                var callback = {
                    close: function(){
                        closePopup();
                        alert('Окно закрыто');
                    },
                    change: function(data) {
                        closePopup();
                        alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
                    }
                    /* Хуки на выбор компании или точки
                    mapPointChange: function(data) {},
                    courierChange: function(data) {}
                    */
                };

                DDelivery.delivery('ddelivery', 'ajax.php?<?isset($_GET['XDEBUG_SESSION_START']) ? 'XDEBUG_SESSION_START='.(int)$_GET['XDEBUG_SESSION_START'] : ''?> ', params, callback);
            }
            <?if(!empty($_GET['fast'])):?>
                DDeliveryStart();
            <?endif;?>
        </script>

        <script type="text/javascript">
            jQuery(function($) {
                // bind event handlers to modal triggers
                $('body').on('click', '.trigger', function(e){
                    e.preventDefault();
                    DDeliveryStart();
                });
            });
        </script>

    </body>
</html>