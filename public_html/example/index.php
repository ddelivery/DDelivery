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

            /**
             * Created by DnAp on 08.05.14.
             */
            var topWindow = parent;

            while(topWindow != topWindow.parent) {
                topWindow = topWindow.parent;
            }

            if(typeof(topWindow.DDeliveryIntegration) == 'undefined')
                topWindow.DDeliveryIntegration = (function(){
                    var th = {};
                    var status = 'Выберите условия доставки';
                    th.getStatus = function(){
                        return status;
                    };

                    function hideCover() {
                        document.body.removeChild(document.getElementById('ddelivery_cover'));
                    }

                    function showPrompt() {
                        var cover = document.createElement('div');
                        cover.id = 'ddelivery_cover';
                        document.body.appendChild(cover);
                        document.getElementById('ddelivery_container').style.display = 'block';
                    }

                    th.openPopup = function(){
                        showPrompt();
                        document.getElementById('ddelivery_popup').innerHTML = '';
                        //jQuery('#ddelivery_popup').html('').modal().open();
                        var params = {
                            formData: {}
                        };
                        $($('#ORDER_FORM').serializeArray()).each(function(){
                            params.formData[this.name] = this.value;
                        });

                        var callback = {
                            close: function(){
                                hideCover();
                                document.getElementById('ddelivery_container').style.display = 'none';
                            },
                            change: function(data) {
                                status = data.comment;
                                console.log(data);
                                document.getElementById('ddelivery').getElementsByTagName('SPAN').innerHTML = data.comment;

                                hideCover();
                                document.getElementById('ddelivery_container').style.display = 'none';

                                $('#ID_DELIVERY_ddelivery_all').click();
                            }
                        };

                        DDelivery.delivery('ddelivery_popup', 'ajax.php', {/*orderId: 4*/}, callback);

                        return void(0);
                    };
                    var style = document.createElement('STYLE');
                    style.innerHTML = // Скрываем ненужную кнопку
                        " #delivery_info_ddelivery_all a{display: none;} " +
                            " #ddelivery_popup { display: inline-block; vertical-align: middle; margin: 10px auto; width: 1000px; height: 650px;} " +
                            " #ddelivery_container { position: fixed; top: 0; left: 0; z-index: 9999;display: none; width: 100%; height: 100%; text-align: center;  } " +
                            " #ddelivery_container:before { display: inline-block; height: 100%; content: ''; vertical-align: middle;} " +
                            " #ddelivery_cover {  position: fixed; top: 0; left: 0; z-index: 9000; width: 100%; height: 100%; background-color: #000; background: rgba(0, 0, 0, 0.5); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = #7F000000, endColorstr = #7F000000); } ";
                    var body = document.getElementsByTagName('body')[0];
                    body.appendChild(style);
                    var div = document.createElement('div');
                    div.innerHTML = '<div id="ddelivery_popup"></div>';
                    div.id = 'ddelivery_container';
                    body.appendChild(div);

                    return th;
                })();
            var DDeliveryIntegration = topWindow.DDeliveryIntegration;
            DDeliveryIntegration.openPopup();

            /*
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

                };

                DDelivery.delivery('ddelivery', 'ajax.php?<?isset($_GET['XDEBUG_SESSION_START']) ? 'XDEBUG_SESSION_START='.(int)$_GET['XDEBUG_SESSION_START'] : ''?> ', params, callback);

            }
            <?if(!empty($_GET['fast'])):?>
                DDeliveryStart();
            <?endif;?>
            */
            /* Хуки на выбор компании или точки
             mapPointChange: function(data) {},
             courierChange: function(data) {}
             */
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