<?php
/**
 * @package    DDelivery.DataBase
 *
 * @author  mrozk 
 */

namespace DDelivery\DataBase;

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
	
	
	public function __construct()
	{
		$this->pdo = SQLite::getPDO();
		$this->createTable();
	}
	
	/**
	 * Создаем таблицу orders
	 */
	public function createTable()
	{
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                          id INTEGER PRIMARY KEY AUTOINCREMENT,
					      type INTEGER,
					      to_city INTEGER,
				          status INTEGER,
					      order_id INTEGER,
				          date TEXT,
				          ddeliveryorder_id INTEGER,
				          point_id INTEGER,  
					      delivery_company INTEGER,
				          shop_refnum TEXT,
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
                          serilize TEXT
                        )");
	}


    /**
     * Проверяем на существование запись
     *
     * @param int $id
     * @return int
     */
	public function isRecordExist( $id )
	{
        if(!$id)
            return 0;
		$sth = $this->pdo->prepare('SELECT id FROM orders WHERE id = :id');
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		$result = (count($data))?1:0;
		return $result;
	}
	
	/**
	 * 
	 * Сохраняем значения курьерского заказа
	 * 
	 * @param int $intermediateID id существующего заказа
	 * @param int $to_city
	 * @param int $delivery_company
	 * @param int $dimensionSide1
	 * @param int $dimensionSide2
	 * @param int $dimensionSide3
	 * @param string $shop_refnum
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
	public function saveFullCourierOrder( $intermediateID, $to_city, $delivery_company, 
                                          $dimensionSide1, $dimensionSide2, 
    			                          $dimensionSide3, $shop_refnum, $confirmed, 
    			                          $weight, $to_name, $to_phone, $goods_description, 
    			                          $declaredPrice, $paymentPrice, $to_street, 
                                          $to_house, $to_flat, $ddeliveryOrderID ) 
	{
		$this->pdo->beginTransaction();
		if( $this->isRecordExist( $intermediateID ) )
		{   
			
			$query = 'UPDATE orders SET type = :type, to_city = :to_city, ddeliveryorder_id = :ddeliveryorder_id, delivery_company = :delivery_company,
					  dimension_side1 = :dimension_side1, dimension_side2 = :dimension_side2, dimension_side3 = :dimension_side3,
					  confirmed = :confirmed, weight = :weight, declared_price = :declared_price, payment_price = :payment_price, to_name = :to_name,
					  to_phone = :to_phone, goods_description = :goods_description, to_street= :to_street, 
					  to_house = :to_house, to_flat = :to_flat, date = :date, 
					  shop_refnum =:shop_refnum  WHERE id=:id';
				
			$stmt = $this->pdo->prepare($query);
			$stmt->bindParam( ':id', $intermediateID );
		}
		else
		{
			$query = 'INSERT INTO orders (type, to_city, ddeliveryorder_id, delivery_company, dimension_side1,
                      dimension_side2, dimension_side3, confirmed, weight, declared_price, payment_price, to_name,
                      to_phone, goods_description, to_flat, to_house, to_street, to_phone, date, shop_refnum)
	                  VALUES (:type, :to_city, :ddeliveryorder_id, :delivery_company, :dimension_side1,
                      :dimension_side2, :dimension_side3, :confirmed, :weight, :declared_price, :payment_price, :to_name,
                      :to_phone, :goods_description, :to_flat, :to_house, :to_street, :to_phone, :date, :shop_refnum)';
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
		$stmt->execute();
		$this->pdo->commit();
	}
	
	/**
	 *
	 * Сохраняем значения заказа самовывоза
	 *
	 * @param int $intermediateID id существующего заказа
	 * @param int $pointID 
	 * @param int $delivery_company
	 * @param int $dimensionSide1
	 * @param int $dimensionSide2
	 * @param int $dimensionSide3
	 * @param string $shop_refnum
	 * @param int $confirmed
	 * @param float $weight
	 * @param string $to_name
	 * @param string $to_phone
	 * @param string $goods_description
	 * @param string $declaredPrice
	 * @param string $paymentPrice
	 * @param $ddeliveryOrderID - id заказа на стороне сервера ddelivery
	 * @param $toCity 
	 * @param $companyID 
	 *
	 */
	public function saveFullSelfOrder( $intermediateID, $pointID, $dimensionSide1, $dimensionSide2,
                                       $dimensionSide3, $confirmed, $weight, $to_name,
                                       $to_phone, $goods_description, $declaredPrice, 
    			                       $paymentPrice, $ddeliveryOrderID, $toCity, $companyID )
	{
		
		$this->pdo->beginTransaction();
		
		if( $this->isRecordExist( $intermediateID ) )
		{
			
			$query = 'UPDATE orders SET type = :type, point_id = :point_id, to_city = :to_city, ddeliveryorder_id = :ddeliveryorder_id,
					  dimension_side1 = :dimension_side1, dimension_side2 = :dimension_side2, dimension_side3 = :dimension_side3, 
					  confirmed = :confirmed, weight = :weight, declared_price = :declared_price, payment_price = :payment_price, to_name = :to_name,
					  to_phone = :to_phone, goods_description = :goods_description,date = :date, 
					  delivery_company = :delivery_company WHERE id=:id';
			
			$stmt = $this->pdo->prepare($query);
			$stmt->bindParam( ':id', $id );
		}
		else 
		{
			
			$query = 'INSERT INTO orders ( type, to_city, ddeliveryorder_id,  dimension_side1,
                      dimension_side2, dimension_side3, confirmed, weight, declared_price, payment_price, to_name,
                      to_phone, goods_description, point_id, delivery_company, date)
	                  VALUES ( :type, :to_city, :ddeliveryorder_id, :dimension_side1,
                      :dimension_side2, :dimension_side3, :confirmed, :weight, :declared_price, :payment_price, :to_name,
                      :to_phone, :goods_description, :point_id, :delivery_company, :date)';
			$stmt = $this->pdo->prepare($query);
		}
		$dateTime = date( "Y-m-d H:i:s" );
		$type = 1;
		$stmt->bindParam( ':type', $type );
		$stmt->bindParam( ':to_city', $toCity );
		$stmt->bindParam( ':point_id', $pointID );
		$stmt->bindParam( ':ddeliveryorder_id', $ddeliveryOrderID );
		$stmt->bindParam( ':dimension_side1', $dimensionSide1 );
		$stmt->bindParam( ':dimension_side2', $dimensionSide2 );
		$stmt->bindParam( ':dimension_side3', $dimensionSide3 );
		$stmt->bindParam( ':confirmed', $confirmed );
		$stmt->bindParam( ':weight', $weight );
		$stmt->bindParam( ':declared_price', $declaredPrice );
		$stmt->bindParam( ':payment_price', $paymentPrice);
		$stmt->bindParam( ':to_name', $to_name );
		$stmt->bindParam( ':to_phone', $to_phone );
		$stmt->bindParam( ':goods_description', $goods_description );
		$stmt->bindParam( ':delivery_company', $companyID );
		$stmt->bindParam( ':date', $dateTime );
		$stmt->execute();
		$this->pdo->commit();
	}
	
	/**
	 *
	 * Обновляем промежуточное значение заказа
	 *
	 * @param int $id id заказа
	 * @param json упакованые параметры промежуточного заказа
	 * 
	 */
	public function updateOrder( $id, $jsonOrder )
	{
		$update = 'UPDATE orders SET type = :type, serilize = :serilize 
		           WHERE id=:id';
		$stmt = $this->pdo->prepare($update);
		$point = $jsonOrder['point'];
		$order = json_encode( $jsonOrder);
		// Bind parameters to statement variables
		$stmt->bindParam( ':type', $jsonOrder['type'] );
		$stmt->bindParam( ':serilize', $order );
		$stmt->bindParam( ':id', $id );
		$stmt->execute();
	}
	/**
	 *
	 * Создаем промежуточное значение заказа
	 *
	 * @param json упакованые параметры промежуточного заказа
	 *
	 */
	public function insertOrder( $jsonOrder )
	{
		$insert = "INSERT INTO orders (type, serilize)
	                VALUES (:type, :serilize )";
		$stmt = $this->pdo->prepare($insert);
		$order = json_encode( $jsonOrder);
		// Bind parameters to statement variables
		$stmt->bindParam( ':type', $jsonOrder['type'] );
		$stmt->bindParam( ':serilize', $order );
		$stmt->execute();
			
		return  $this->pdo->lastInsertId();
	}
	
	public function selectByID( $id )
	{
		$sth = $this->pdo->prepare('SELECT * FROM orders WHERE id = :id');
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function selectSerializeByID( $id )
	{
		$sth = $this->pdo->prepare('SELECT serilize FROM orders WHERE id = :id');
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_COLUMN);
		return $data;
	}
	
	public function selectAll()
	{   
		$this->pdo->beginTransaction();
		$sth = $this->pdo->query('SELECT * FROM orders');
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		$this->pdo->commit();
		return $data;
	}
	
}