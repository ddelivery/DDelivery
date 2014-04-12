<?php

namespace DDelivery\DataBase;

use PDO;

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
	
	public function createTable()
	{
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                          id INT PRIMARY KEY,
					      type INT,
					      to_city INT,
					      order_id INT,
				          date DATETIME,
				          ddeliveryorder_id INT,
				          point_id INT,  
					      delivery_company INT,
				          shop_refnum VARCHAR(50),
					      dimension_side1 INT,
				 	      dimension_side2 INT,
					      dimension_side3 INT,
					      confirmed TINYINT,
					      weight REAL,
					      declared_price REAL,
					      payment_price REAL,
					      to_name VARCHAR(100),
					      to_phone VARCHAR(10),
					      goods_description VARCHAR(255),
				          to_street  VARCHAR(100),    
				          to_house VARCHAR(50),
				          to_flat VARCHAR(25),
				          to_email VARCHAR(50),
                          serilize TEXT
                        )");
	}
	
	public function isRecordExist( $id )
	{
		$sth = $this->pdo->prepare('SELECT id FROM orders WHERE id = :id');
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		$result = (count($data))?1:0;
		return $result;
	}
	
	public function saveFullCourierOrder( $intermediateID, $to_city, $delivery_company, 
                                          $dimensionSide1, $dimensionSide2, 
    			                          $dimensionSide3, $shop_refnum, $confirmed, 
    			                          $weight, $to_name, $to_phone, $goods_description, 
    			                          $declaredPrice, $paymentPrice, $to_street, 
                                          $to_house, $to_flat, $ddeliveryOrderID) 
	{
		$this->pdo->beginTransaction();
		if( $this->isRecordExist( $intermediateID ) )
		{
			$query = 'UPDATE orders SET type = :type, to_city = :to_city, ddeliveryorder_id = :ddeliveryorder_id, delivery_company = :delivery_company,
					  dimension_side1 = :dimension_side1, dimension_side2 = :dimension_side2, dimension_side3 := dimension_side3,
					  confirmed = :confirmed, weight = :weight, declared_price = :declared_price, payment_price = :payment_price, to_name = :to_name,
					  to_phone = :to_phone, goods_description = :goods_description, to_street= :to_street, 
					  to_house = :to_house, to_flat = :to_flat   WHERE id=:id';
				
			$stmt = $this->pdo->prepare($query);
			$stmt->bindParam( ':id', $id );
		}
		else
		{
			$query = 'INSERT INTO orders (type, to_city, ddeliveryorder_id, delivery_company, dimension_side1,
                      dimension_side2, dimension_side3, confirmed, weight, declared_price, payment_price, to_name,
                      to_phone, goods_description, to_house, to_street, to_phone)
	                  VALUES (:type, :to_city, :ddeliveryorder_id, :delivery_company, :dimension_side1,
                      :dimension_side2, :dimension_side3, :confirmed, :weight, :declared_price, :payment_price, :to_name,
                      :to_phone, :goods_description, :to_house, :to_street, :to_phone)';
			$stmt = $this->pdo->prepare($query);
		}
		$type = 2;
		$stmt->bindParam( ':type', $type );
		$stmt->bindParam( ':to_city', $to_city );
		$stmt->bindParam( ':ddeliveryorder_id', $ddeliveryOrderID );
		$stmt->bindParam( ':delivery_company', $delivery_company );
		$stmt->bindParam( ':dimension_side1', $dimension_side1 );
		$stmt->bindParam( ':dimension_side2', $dimension_side2 );
		$stmt->bindParam( ':dimension_side3', $dimension_side3 );
		$stmt->bindParam( ':confirmed', $confirmed );
		$stmt->bindParam( ':weight', $weight );
		$stmt->bindParam( ':declared_price', $declared_price );
		$stmt->bindParam( ':payment_price', $payment_price );
		$stmt->bindParam( ':to_name', $to_name );
		$stmt->bindParam( ':to_phone', $to_phone );
		$stmt->bindParam( ':goods_description', $goods_description );
		$stmt->bindParam( ':to_house', $to_house );
		$stmt->bindParam( ':to_street', $to_street );
		$stmt->bindParam( ':to_phone', $to_phone );
		$stmt->execute();
		
		$this->pdo->commit();
		
	}
	
	public function saveFullSelfOrder( $intermediateID, $pointID, $dimensionSide1, $dimensionSide2,
                                       $dimensionSide3, $confirmed, $weight, $to_name,
                                       $to_phone, $goods_description, $declaredPrice, 
    			                       $paymentPrice, $ddeliveryOrderID )
	{
		$this->pdo->beginTransaction();
		if( $this->isRecordExist( $intermediateID ) )
		{
			
			$query = 'UPDATE orders SET type = :type, point_id = :point_id, to_city = :to_city, ddeliveryorder_id = :ddeliveryorder_id,
					  dimension_side1 = :dimension_side1, dimension_side2 = :dimension_side2, dimension_side3 := dimension_side3, 
					  confirmed = :confirmed, weight = :weight, declared_price = :declared_price, payment_price = :payment_price, to_name = :to_name,
					  to_phone = :to_phone, goods_description = :goods_description WHERE id=:id';
			
			$stmt = $this->pdo->prepare($query);
			$stmt->bindParam( ':id', $id );
		}
		else 
		{
			
			$query = 'INSERT INTO orders (type, to_city, ddeliveryorder_id, delivery_company, dimension_side1,
                      dimension_side2, dimension_side3, confirmed, weight, declared_price, payment_price, to_name,
                      to_phone, goods_description, point_id)
	                  VALUES (:type, :to_city, :ddeliveryorder_id, :delivery_company, :dimension_side1,
                      :dimension_side2, :dimension_side3, :confirmed, :weight, :declared_price, :payment_price, :to_name,
                      :to_phone, :goods_description, :point_id)';
			$stmt = $this->pdo->prepare($query);
		}
		
		$type = 1;
		$stmt->bindParam( ':type', $type );
		$stmt->bindParam( ':to_city', $to_city );
		$stmt->bindParam( ':point_id', $point_id );
		$stmt->bindParam( ':ddeliveryorder_id', $ddeliveryorder_id );
		$stmt->bindParam( ':delivery_company', $delivery_company );
		$stmt->bindParam( ':dimension_side1', $dimension_side1 );
		$stmt->bindParam( ':dimension_side2', $dimension_side2 );
		$stmt->bindParam( ':dimension_side3', $dimension_side3 );
		$stmt->bindParam( ':confirmed', $confirmed );
		$stmt->bindParam( ':weight', $weight );
		$stmt->bindParam( ':declared_price', $declared_price );
		$stmt->bindParam( ':payment_price', $payment_price );
		$stmt->bindParam( ':to_name', $to_name );
		$stmt->bindParam( ':to_phone', $to_phone );
		$stmt->bindParam( ':goods_description', $goods_description );
		$stmt->execute();
		print_r( $stmt->errorInfo());
		$this->pdo->commit();
	}
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
		$sth = $this->pdo->prepare('SELECT serilize, point FROM orders WHERE id = :id');
		$sth->bindParam( ':id', $id );
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_COLUMN);
		return $data;
	}
	
	public function selectAll()
	{
		$sth = $this->pdo->query('SELECT * FROM orders');
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
}