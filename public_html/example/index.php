<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <script src="../html/js/ddelivery.js"></script>

        <a href="javascript:void(0)" id="select_way" class="trigger">Выбрать точку доставки</a>

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
                        /*
                        $($('#ORDER_FORM').serializeArray()).each(function(){
                            params.formData[this.name] = this.value;
                        });
                        */

                        var callback = {
                            close: function(){
                                hideCover();
                                document.getElementById('ddelivery_container').style.display = 'none';
                            },
                            change: function(data) {
                                status = data.comment;
                                console.log(data);
                                hideCover();
                                document.getElementById('ddelivery_container').style.display = 'none';

                                //$('#ID_DELIVERY_ddelivery_all').click();
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
                    div.innerHTML = '<div id="ddelivery_popup">xxxx</div>';
                    div.id = 'ddelivery_container';
                    body.appendChild(div);

                    return th;
                })();
            var DDeliveryIntegration = topWindow.DDeliveryIntegration;
            select_way = document.getElementById('select_way');
            select_way.onclick = function(){DDeliveryIntegration.openPopup()};

            /* Хуки на выбор компании или точки
             mapPointChange: function(data) {},
             courierChange: function(data) {}
             */
        </script>

    </body>
</html>