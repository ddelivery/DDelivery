<?php
/**
 * @package    DDelivery.DataBase
 *
 * @author  mrozk 
 */

namespace DDelivery\DataBase;

use DDelivery\Adapter\DShopAdapter;
use DDelivery\Order\DDStatusProvider;
use DDelivery\Order\DDeliveryOrder;
use PDO;

/**
 * Class Order
 * @package DDelivery\DataBase
 */
class Order {

	/**
	 * @var PDO
	 */
	public $pdo;


	public function __construct(\PDO $pdo, $prefix = '')
	{
        $this->pdo = $pdo;
        $this->prefix = $prefix;
        if($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) == 'sqlite') {
            $this->pdoType = DShopAdapter::DB_SQLITE;
        }else{
            $this->pdoType = DShopAdapter::DB_MYSQL;
        }
	}

	/**
	 * Создаем таблицу orders
	 *
	 * Описание к полям таблицы:
	 *
	 * paymen_variant способ оплаты
	 * shop_refnum id заказа на стороне CMS
	 * local_status статус заказа на стороне CMS
	 * dd_status статус заказа на стороне ddelivery
	 * type тип заказа
	 * amount сума заказа
	 * products сериализированный массив с продуктами
	 * to_city id города клиента
	 * date дата добавления заявки
	 * ddeliveryorder_id id заявки на стороне ddelivery
	 * point_id id точки
	 * delivery_company id компании
	 * dimension_side1 сторона заказа 1
	 * dimension_side2 строна заказа 2
	 * dimension_side3 сторона заказа 3
	 * confirmed подтвержден заказ
	 * weight вес
	 * declared_price свойство заказа dd
	 * payment_price  свойство заказа dd
	 * to_name    ФИО клиента
	 * to_phone телефон клиента
	 * goods_description описание товаров
	 * to_street  улица
	 * to_house дом
	 * to_flat квартира
	 * to_email емейл
	 * firstName имя
	 * secondName Фамилия
	 * serilize упакованый order
	 * point сериализированный объект точки
	 *
	 */
	public function createTable()
	{
        if($this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = "CREATE TABLE `{$this->prefix}orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `payment_variant` varchar(255) DEFAULT NULL,
                `shop_refnum` int(11) DEFAULT NULL,
                `local_status` int(11) DEFAULT NULL,
                `dd_status` int(11) DEFAULT NULL,
                `type` int(11) DEFAULT NULL,
                `amount` float(11,2) DEFAULT NULL,
                `products` text DEFAULT NULL,
                `to_city` int(11) DEFAULT NULL,
                `date` datetime DEFAULT NULL,
                `ddeliveryorder_id` int(11) DEFAULT NULL,
                `point_id` int(11) DEFAULT NULL,
                `delivery_company` int(11) DEFAULT NULL,
                `dimension_side1` int(11) DEFAULT NULL,
                `dimension_side2` int(11) DEFAULT NULL,
                `dimension_side3` int(11) DEFAULT NULL,
                `confirmed` int(11) DEFAULT NULL,
                `weight` int(11) DEFAULT NULL,
                `declared_price` int(11) DEFAULT NULL,
                `payment_price` int(11) DEFAULT NULL,
                `to_name` varchar(255) DEFAULT NULL,
                `to_phone` varchar(255) DEFAULT NULL,
                `goods_description` text DEFAULT NULL,
                `to_street` varchar(255) DEFAULT NULL,
                `to_house` varchar(255) DEFAULT NULL,
                `to_flat` varchar(255) DEFAULT NULL,
                `to_email` varchar(255) DEFAULT NULL,
                `first_name` varchar(255) DEFAULT NULL,
                `second_name` varchar(255) DEFAULT NULL,
                `serilize` text DEFAULT NULL,
                `point` text DEFAULT NULL,
                `comment` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $this->pdo->exec($query);
        }elseif($this->pdoType == DShopAdapter::DB_SQLITE){
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                payment_variant TEXT,
                shop_refnum INTEGER,
                local_status INTEGER,
                dd_status INTEGER,
                type INTEGER,
                amount REAL,
                products TEXT,
                to_city INTEGER,
                date TEXT,
                ddeliveryorder_id INTEGER,
                point_id INTEGER,
                delivery_company INTEGER,
                dimension_side1 INTEGER,
                dimension_side2 INTEGER,
                dimension_side3 INTEGER,
                confirmed INTEGER,
                weight REAL,
                declared_price REAL,
                payment_price REAL,
                to_name TEXT,
                to_phone TEXT,
                goods_description TEXT,
                to_street  TEXT,
                to_house TEXT,
                to_flat TEXT,
                to_email TEXT,
                first_name TEXT,
                second_name TEXT,
                serilize TEXT,
                point TEXT,
                comment TEXT
              )");
        }
	}

	/**
	 * Получить заказ по его cms ID
	 *
	 * @param int[] $cmsOrderID - id заказа внутри cms
	 *
     * @return array
	 */
	public function getOrderByCmsOrderID( $cmsOrderID )
	{
        if($this->pdoType == DShopAdapter::DB_SQLITE || $this->pdoType == DShopAdapter::DB_SQLITE) {
		    $query = "SELECT id FROM {$this->prefix}orders WHERE shop_refnum = :cmsOrderId";
        }
        $sth = $this->pdo->prepare( $query );
        $sth->bindParam( ':cmsOrderId', $cmsOrderID );
        $sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

    public function getNotFinishedOrders()
    {
        if($this->pdoType == DShopAdapter::DB_SQLITE || $this->pdoType == DShopAdapter::DB_MYSQL) {
            $query = "SELECT id FROM {$this->prefix}orders WHERE  dd_status <> :dd_status AND dd_status <> :dd_status2
                      AND shop_refnum <> :shop_refnum AND ddeliveryorder_id <> :ddeliveryorder_id";
        }
        $sth = $this->pdo->prepare( $query );
        $dd_status = DDStatusProvider::ORDER_RECEIVED;
        $dd_status2 = DDStatusProvider::ORDER_RETURNED_MI;
        $shop_refnum = 0;
        $ddeliveryorder_id = 0;
        $sth->bindParam( ':ddeliveryorder_id', $ddeliveryorder_id );
        $sth->bindParam( ':dd_status', $dd_status );
        $sth->bindParam( ':dd_status2', $dd_status2 );
        $sth->bindParam( ':shop_refnum', $shop_refnum );
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_OBJ);

        return $data;

    }


	/**
	 * Получить список заказов
	 *
	 * @param int[] $ids массив с  заказов
	 *
	 * @return array
	 */
	public function getOrderList( $ids )
	{
        if(empty($ids))
            return array();

        foreach($ids as &$id){
            $id = (int)$id;
        }
		$idWhere = implode(',', $ids);
        $query = "SELECT * FROM {$this->prefix}orders WHERE id IN({$idWhere})";
        $sth = $this->pdo->query( $query );
        $result = $sth->fetchAll(PDO::FETCH_OBJ);

        return $result;
	}

	/**
	 * Устанавливаем для заказа в БД SQLLite id заказа в CMS
	 *
	 * @param int $id id локальной БД SQLLite
	 * @param int $shopOrderID id заказа в CMS
	 *
	 * @return bool
	 */
	public function setShopOrderID( $id, $shopOrderID )
	{
		$this->pdo->beginTransaction();
		if( $this->isRecordExist( $id ) )
		{
			$query = "UPDATE {$this->prefix}orders SET order_id = :order_id WHERE id=:id";
			$sth = $this->pdo->prepare( $query );
			$sth->bindParam( ':id', $id );
			$sth->bindParam( ':order_id', $shopOrderID );
			if( $sth->execute() )
			{
				return true;
			}
		}
		$this->pdo->commit();
		return false;
	}

    /**
     * Проверяем на существование запись
     *
     * @param int $id
     * @return int
     */
	public function isRecordExist( $id )
	{
        $id = (int)$id;
        if(!$id) return 0;

		$sth = $this->pdo->prepare("SELECT id FROM {$this->prefix}orders WHERE id = :id");
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		$result = (count($data))?1:0;
		return $result;
	}

    /**
     * @param DDeliveryOrder $order
     * @return int
     */
	public function saveFullOrder( DDeliveryOrder $order )
	{
	    $wasUpdate = 0;
	    
	    $point = $order->getPoint();
        $pointDB = '';
        $delivery_company = 0;
        $deliveryPrice = 0;
        if( $point !== null )
        {
            $delivery_company = $point->getDeliveryInfo()->get('delivery_company');
            $deliveryPrice = $point->getDeliveryInfo()->get('total_price');
            $pointDB = serialize($point);
        }
	    $dimensionSide1 = $order->getDimensionSide1();
	    $dimensionSide2 = $order->getDimensionSide2();
	    $dimensionSide3 = $order->getDimensionSide3();
	    $goods_description = $order->getGoodsDescription();
	    $weight = $order->getWeight();
	    $to_city = $order->city;

	    $confirmed = $order->getConfirmed();
	    $to_name = $order->getToName();
	    $to_phone = $order->getToPhone();
	    $declaredPrice = $order->declaredPrice;
	    $paymentPrice = $order->paymentPrice;
	    $ddeliveryID = $order->ddeliveryID;
	    $localId = $order->localId;

	    $payment_variant = $order->paymentVariant;
	    $localStatus = $order->localStatus;
	    $ddStatus = $order->ddStatus;
	    $shop_refnum = $order->shopRefnum;
	    $firstName = $order->firstName;
	    $secondName = $order->secondName;
	    $amount = $order->amount;

	    $productString = $order->getSerializedProducts();
	    $toStreet = $order->toStreet;
	    $toHouse = $order->toHouse;
	    $toFlat = $order->toFlat;
	    $type = $order->type;
        $comment = $order->comment;
	    //$this->pdo->beginTransaction();
	    if( $this->isRecordExist($localId) )
	    {
	    	$query = "UPDATE {$this->prefix}orders SET comment = :comment, payment_variant = :payment_variant, type = :type, amount =:amount,
	    			  to_city = :to_city,
	    			  ddeliveryorder_id = :ddeliveryorder_id, delivery_company = :delivery_company,
	    			  dimension_side1 = :dimension_side1, dimension_side2 = :dimension_side2,
	    			  dimension_side3 = :dimension_side3, confirmed = :confirmed,
			          weight = :weight, declared_price = :declared_price, payment_price = :payment_price,
	    			  to_name = :to_name, to_phone = :to_phone, goods_description = :goods_description,
	    			  to_street= :to_street, to_house = :to_house, to_flat = :to_flat, date = :date,
			          shop_refnum =:shop_refnum, products = :products, local_status = :local_status,
			          dd_status = :dd_status, first_name = :first_name, second_name =:second_name,
	    			  point = :point  WHERE id=:id";
	    	$stmt = $this->pdo->prepare($query);
	    	$stmt->bindParam( ':id', $localId );
            $wasUpdate = 1;
	    }
	    else 
	    {
	    	$query = "INSERT INTO {$this->prefix}orders ( comment, payment_variant, type, amount, to_city, ddeliveryorder_id,
	    			  delivery_company, dimension_side1,
                      dimension_side2, dimension_side3, confirmed, weight, declared_price,
	    			  payment_price, to_name, to_phone, goods_description, to_flat, to_house,
	    			  to_street, date, shop_refnum, products, local_status, dd_status,
	    			  first_name, second_name, point)
	                  VALUES( :comment, :payment_variant, :type, :amount, :to_city, :ddeliveryorder_id, :delivery_company,
	    			  :dimension_side1, :dimension_side2, :dimension_side3, :confirmed, :weight,
	    			  :declared_price, :payment_price, :to_name, :to_phone, :goods_description,
	    			  :to_flat, :to_house, :to_street,  :date, :shop_refnum, :products,
	    			  :local_status, :dd_status, :first_name, :second_name, :point )";
	    	$stmt = $this->pdo->prepare($query);
	    }
        $stmt->bindParam( ':comment', $comment  );
	    $stmt->bindParam( ':payment_variant', $payment_variant  );
	    $stmt->bindParam( ':type', $type );
	    $stmt->bindParam( ':amount', $amount );
	    $dateTime = date( "Y-m-d H:i:s" );
	    $stmt->bindParam( ':to_city', $to_city );
	    $stmt->bindParam( ':ddeliveryorder_id', $ddeliveryID );
	    $stmt->bindParam( ':delivery_company', $delivery_company );
	    $stmt->bindParam( ':dimension_side1', $dimensionSide1 );
	    $stmt->bindParam( ':dimension_side2', $dimensionSide2 );
	    $stmt->bindParam( ':dimension_side3', $dimensionSide3 );
	    $stmt->bindParam( ':confirmed', $confirmed );
	    $stmt->bindParam( ':weight', $weight );
	    $stmt->bindParam( ':declared_price', $declaredPrice );
	    $stmt->bindParam( ':payment_price', $paymentPrice );
	    $stmt->bindParam( ':to_name', $to_name );
	    $stmt->bindParam( ':to_phone', $to_phone );
	    $stmt->bindParam( ':goods_description', $goods_description );
	    $stmt->bindParam( ':to_house', $toHouse );
	    $stmt->bindParam( ':to_street', $toStreet );
	    $stmt->bindParam( ':to_flat', $toFlat );
	    $stmt->bindParam( ':date', $dateTime );
	    $stmt->bindParam( ':shop_refnum', $shop_refnum );
	    $stmt->bindParam( ':products', $productString );
	    $stmt->bindParam( ':local_status', $localStatus );
	    $stmt->bindParam( ':dd_status', $ddStatus );
	    $stmt->bindParam( ':first_name', $firstName );
	    $stmt->bindParam( ':second_name', $secondName );
	    $stmt->bindParam( ':point', $pointDB );
	    $stmt->execute();
	    //$this->pdo->commit();

	    if( $wasUpdate )
	    {
	    	return $localId;
	    }
	    else
	    {
	    	return $this->pdo->lastInsertId();
	    }
	    
	    
	    
	}
	/**
	 * 
	 * Сохраняем значения курьерского заказа
	 * 
	 * @deprecated
	 * 
	 * @param int $intermediateID id существующего заказа
	 * @param int $to_city
	 * @param int $delivery_company
	 * @param int $dimensionSide1
	 * @param int $dimensionSide2
	 * @param int $dimensionSide3
	 * @param int $shop_refnum
	 * @param int $confirmed
	 * @param float $weight
	 * @param string $to_name
	 * @param string $to_phone
	 * @param string $goods_description
	 * @param string $declaredPrice
	 * @param string $paymentPrice
	 * @param string $to_street
	 * @param string $to_house
	 * @param string $to_flat
	 * @param $ddeliveryOrderID - id заказа на стороне сервера ddelivery
	 *    
	 */
	public function saveFullCourierOrder( $intermediateID, $to_city, $delivery_company, $dimensionSide1, 
			                              $dimensionSide2, $dimensionSide3, $shop_refnum, $confirmed, 
    			                          $weight, $to_name, $to_phone, $goods_description, $declaredPrice, 
			                              $paymentPrice, $to_street, $to_house, $to_flat, $ddeliveryOrderID, 
			                              $productString,$localStatus, $ddStatus, $firstName, $secondName,
			                              $pointDB   ) 
	{
		$wasUpdate = 0;
 		$this->pdo->beginTransaction();
 		if( $this->isRecordExist( $intermediateID ) )
 		{   
			
			$query = "UPDATE {$this->prefix}orders SET type = :type, to_city = :to_city, ddeliveryorder_id = :ddeliveryorder_id,
					  delivery_company = :delivery_company, dimension_side1 = :dimension_side1,
					  dimension_side2 = :dimension_side2, dimension_side3 = :dimension_side3, confirmed = :confirmed,
					  weight = :weight, declared_price = :declared_price, payment_price = :payment_price, to_name = :to_name,
					  to_phone = :to_phone, goods_description = :goods_description, to_street= :to_street,
					  to_house = :to_house, to_flat = :to_flat, date = :date,
					  shop_refnum =:shop_refnum, products = :products, local_status = :local_status,
				      dd_status = :dd_status, first_name = :first_name, second_name =:second_name, point = :point  WHERE id=:id";
				
			$stmt = $this->pdo->prepare($query);
			$stmt->bindParam( ':id', $intermediateID );
			$wasUpdate = 1;
		}
		else
		{
			$query = "INSERT INTO {$this->prefix}orders (type, to_city, ddeliveryorder_id, delivery_company, dimension_side1,
                      dimension_side2, dimension_side3, confirmed, weight, declared_price, payment_price, to_name,
                      to_phone, goods_description, to_flat, to_house, to_street, to_phone, date, shop_refnum,
					  products, local_status, dd_status, first_name, second_name, point)
	                  VALUES
					  (:type, :to_city, :ddeliveryorder_id, :delivery_company, :dimension_side1,
                      :dimension_side2, :dimension_side3, :confirmed, :weight, :declared_price,
					  :payment_price, :to_name, :to_phone, :goods_description, :to_flat, :to_house,
					  :to_street, :to_phone, :date, :shop_refnum, :products, :local_status, :dd_status, :first_name, :second_name, :point )";
			$stmt = $this->pdo->prepare($query);
		}
		
		$dateTime = date( "Y-m-d H:i:s" );
		$type = 2;
		$stmt->bindParam( ':type', $type );
		$stmt->bindParam( ':to_city', $to_city );
		$stmt->bindParam( ':ddeliveryorder_id', $ddeliveryOrderID );
		$stmt->bindParam( ':delivery_company', $delivery_company );
		$stmt->bindParam( ':dimension_side1', $dimensionSide1 );
		$stmt->bindParam( ':dimension_side2', $dimensionSide2 );
		$stmt->bindParam( ':dimension_side3', $dimensionSide3 );
		$stmt->bindParam( ':confirmed', $confirmed );
		$stmt->bindParam( ':weight', $weight );
		$stmt->bindParam( ':declared_price', $declaredPrice );
		$stmt->bindParam( ':payment_price', $paymentPrice );
		$stmt->bindParam( ':to_name', $to_name );
		$stmt->bindParam( ':to_phone', $to_phone );
		$stmt->bindParam( ':goods_description', $goods_description );
		$stmt->bindParam( ':to_house', $to_house );
		$stmt->bindParam( ':to_street', $to_street );
		$stmt->bindParam( ':to_phone', $to_phone );
		$stmt->bindParam( ':date', $dateTime );
		$stmt->bindParam( ':shop_refnum', $shop_refnum );
		$stmt->bindParam( ':to_flat', $to_flat );
		$stmt->bindParam( ':products', $productString );
		$stmt->bindParam( ':local_status', $localStatus );
		$stmt->bindParam( ':dd_status', $ddStatus );
		$stmt->bindParam( ':first_name', $firstName );
		$stmt->bindParam( ':second_name', $secondName );
		$stmt->bindParam( ':point', $pointDB );
		$stmt->execute();
		$this->pdo->commit();
		if( $wasUpdate )
		{
			return $intermediateID;
		}
		else 
		{
		    return $this->pdo->lastInsertId();
		}
	}

	/**
	 *
	 * Обновляем промежуточное значение заказа
	 *
	 * @param int $id id заказа
	 * @param json $jsonOrder упакованые параметры промежуточного заказа
	 * 
	 */
	public function updateOrder( $id, $jsonOrder )
	{
		$update = "UPDATE {$this->prefix}orders SET type = :type, serilize = :serialise
		           WHERE id=:id";
		$stmt = $this->pdo->prepare($update);
		$point = $jsonOrder['point'];
		$order = json_encode( $jsonOrder);
		// Bind parameters to statement variables
		$stmt->bindParam( ':type', $jsonOrder['type'] );
		$stmt->bindParam( ':serialise', $order );
		$stmt->bindParam( ':id', $id );
		$stmt->execute();
	}
	/**
	 *
	 * Создаем промежуточное значение заказа
	 *
	 * @param json упакованые параметры промежуточного заказа
	 *
     * @return int
	 */
	public function insertOrder( $jsonOrder )
	{
		$insert = "INSERT INTO {$this->prefix}orders (type, serilize)
	                VALUES (:type, :serilize )";
		$stmt = $this->pdo->prepare($insert);
		$order = json_encode( $jsonOrder);
		// Bind parameters to statement variables
		$stmt->bindParam( ':type', $jsonOrder['type'] );
		$stmt->bindParam( ':serilize', $order );
		$stmt->execute();
			
		return  $this->pdo->lastInsertId();
	}

    /**
     * Удалить все заказы
     * @return bool
     */
    public function cleanOrders( )
    {
        $delete = "DELETE FROM orders ";
        $stmt = $this->pdo->prepare($delete);
        return $stmt->execute();
    }
	
	public function selectByID( $id )
	{
		$sth = $this->pdo->prepare("SELECT * FROM {$this->prefix}orders WHERE id = :id");
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function selectSerializeByID( $id )
	{
		$sth = $this->pdo->prepare("SELECT serilize FROM {$this->prefix}orders WHERE id = :id");
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_COLUMN);
		return $data;
	}
	
	public function selectAll()
	{   
		$this->pdo->beginTransaction();
		$sth = $this->pdo->query("SELECT * FROM {$this->prefix}orders");
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		$this->pdo->commit();
		return $data;
	}

}