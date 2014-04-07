	<?php
namespace DDelivery\DataBase;

use PDO;

class Order {
	
	/**
	 * @var PDO
	 */
	public $pdo;
	
	
	function __construct()
	{
		$this->pdo = SQLite::getPDO();
	}
	
	function createTable()
	{
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                    id INTEGER PRIMARY KEY,
					type INTEGER,
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
				    
                    serilize TEXT
                    )");
		
	}
	
	function selectAll()
	{
		$result = $this->pdo->query('SELECT * FROM orders');
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	/*
	function insertIntoTable()
	{	
		
		$messages = array(
				array('title' => 'Hello!',
						'message' => 'Just testing...',
						'time' => 1327301464),
				array('title' => 'Hello again!',
						'message' => 'More testing...',
						'time' => 1339428612),
				array('title' => 'Hi!',
						'message' => 'SQLite3 is cool...',
						'time' => 1327214268)
		);
		
	 	$insert = "INSERT INTO messages (title, message, time) 
                   VALUES (:title, :message, :time)";
    	$stmt = $this->pdo->prepare($insert);
	    // Bind parameters to statement variables
	    $stmt->bindParam(':title', $title);
	    $stmt->bindParam(':message', $message);
	    $stmt->bindParam(':time', $time);
 
	    // Loop thru all messages and execute prepared insert statement
	    foreach ($messages as $m) {
	      // Set values to bound variables
	      $title = $m['title'];
	      $message = $m['message'];
	      $time = $m['time'];
	 
	      // Execute statement
	      $stmt->execute();
	    }
	}
	*/
}