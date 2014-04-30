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

        <a href="#" class="trigger">Open modal</a>

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

        <script type="text/javascript">
            jQuery(function($){
                // attach modal close handler
                $('.map-popup__head__close').on('click', function(e){
                    e.preventDefault();
                    $.modal().close();
                });
                // bind event handlers to modal triggers
                $('body').on('click', '.trigger', function(e){
                    e.preventDefault();
                    $('#test-modal').modal().open();
                    DDeliveryStart();
                });

                // below isn't important (demo-specific things)
                $('.modal .more-toggle').on('click', function(e){
                    e.stopPropagation();
                    $('.modal .more').toggle();
                });
            });
        </script>

    </body>
</html>