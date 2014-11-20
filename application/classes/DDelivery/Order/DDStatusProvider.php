<?php
/**
 * User: mrozk
 * Date: 19.04.14
 * Time: 13:20
 */
namespace DDelivery\Order;

class DDStatusProvider
{
    const ORDER_IN_PROGRESS = 10;

    const ORDER_CONFIRMED = 20;

    const ORDER_IN_STOCK = 30;

    const ORDER_IN_WAY = 40;

    const ORDER_DELIVERED = 50;

    const ORDER_RECEIVED = 60;

    const ORDER_RETURN = 70;

    const ORDER_CUSTOMER_RETURNED = 80;

    const ORDER_PARTIAL_REFUND = 90;

    const ORDER_RETURNED_MI = 100;

    const ORDER_WAITING = 110;

    const ORDER_CANCEL = 120;

    public $ddeliveryOrderStatus = array(  self::ORDER_IN_PROGRESS => 'В обработке', self::ORDER_CONFIRMED => 'Подтверждена',
                                           self::ORDER_IN_STOCK => 'На складе ИМ', self::ORDER_IN_WAY => 'Заказ в пути',
                                           self::ORDER_DELIVERED => 'Заказ доставлен', self::ORDER_RECEIVED => 'Заказ получен',
                                           self::ORDER_RETURN => 'Возврат заказа', self::ORDER_CUSTOMER_RETURNED => 'Клиент вернул заказ',
                                           self::ORDER_PARTIAL_REFUND => 'Частичный возврат заказа',
                                           self::ORDER_RETURNED_MI => 'Возвращен в ИМ', self::ORDER_WAITING => 'Ожидание',
                                           self::ORDER_CANCEL => 'Отмена');


    public function  getOrderDescription( $orderCode )
    {
        if(!empty( $this->ddeliveryOrderStatus[$orderCode] ) )
        {
            return $this->ddeliveryOrderStatus[$orderCode];
        }
        return 'Статус с таким кодом не найден';
    }


}