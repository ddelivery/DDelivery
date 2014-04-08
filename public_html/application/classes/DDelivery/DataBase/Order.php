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
                    id INTEGER PRIMARY KEY,
					type INTEGER,
					to_city INTEGER,
					order_id INTEGER, 
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
                    serilize TEXT,
				    point TEXT
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
		$insert = "INSERT INTO orders (type, serilize,point)
	                VALUES (:type, :serilize, :point )";
		$stmt = $this->pdo->prepare($insert);
		$point = $jsonOrder['point'];
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