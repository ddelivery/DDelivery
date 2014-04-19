<?php
// setShopOrderId

class DDeliveryUITest extends PHPUnit_Framework_TestCase
{
	protected $fixture;
	
	protected function setUp()
	{	
		$shopAdapter = new \DDelivery\Adapter\DShopAdapterTest();
		$this->fixture = new \DDelivery\DDeliveryUI( $shopAdapter );
	}


    public function testGetCourierPointsForCity()
    {
        $points = $this->fixture->getCourierPointsForCity(151185);
        $this->assertGreaterThan( 0, count( $points ) );
    }

    public function testSaveFullOrder()
    {
        $order = $this->fixture->getOrder();
        $order->city = 151184;
        $order->localId = 1;
        $order->type = 2;
        $order->firstName = 'Дима';
        $order->secondName = 'Грушин';
        $order->toPhone = '9999999999';
        $order->toStreet = 'Вознесенская';
        $order->toHouse = '1а';
        $order->toFlat = '42';
        $order->toEmail = '';
        $id = $this->fixture->saveFullOrder($order);
        $this->assertGreaterThan( 0, $id );
    }
    public function testGetLocalStatusByDD()
    {
        $status = $this->fixture->getLocalStatusByDD(20);
        $this->assertGreaterThan( 0, $status );
    }
    public function testGetDDOrderStatus()
    {

    }
    public function testChangeOrderStatus()
    {

    }


    /*
	public function testSaveIntermediateOrder()
	{	
		
		$order = $this->fixture->getOrder();
		$selfpoint = $this->fixture->getSelfPoints(151185);
		
		$order->city = 151185;
		$order->type = 1;
		$order->setPoint($selfpoint[0]);
		$order->toName = 'Дима Грушин';
		$order->toPhone = '9999999999';
		$order->shopRefnum = 'xxx';
		$order->toStreet = 'Вознесенская';
		$order->toHouse = '1а';
		$order->toFlat = '42';
		$order->toEmail = '';
		
		$id = $this->fixture->saveIntermediateOrder();
		$data = $this->fixture->getAllOrders();
		
		$this->assertGreaterThan( 0, $id );
		$this->assertGreaterThan( 0, count($data) );
	}
	
	public function testInitIntermediateOrder()
	{
		$shopAdapter = new \DDelivery\Adapter\DShopAdapterTest();
		$ui = new \DDelivery\DDeliveryUI( $shopAdapter );
		$ui->initIntermediateOrder(1);
		$order = $ui->getOrder();
		$this->assertGreaterThan( 0, $order->type );
	}
	
	public function testSaveFullOrder()
	{
		$order = $this->fixture->getOrder();
		$selfpoint = $this->fixture->getCourierPointsForCity(151185);
		$order->city = 151185;
		$order->type = 2;
		$order->setPoint($selfpoint[0]);
		$order->toName = 'Дима Грушин';
		$order->toPhone = '9999999999';
		$order->shopRefnum = 'xxx';
		$order->toStreet = 'Вознесенская';
		$order->toHouse = '1а';
		$order->toFlat = '42';
		$order->toEmail = '';
		$id = $this->fixture->saveFullOrder(5);
		$this->assertGreaterThan( 0, $id );
	}
	
	public function testGetSelfPointsForCityAndCompany()
	{
		$result = $this->fixture->getSelfPointsForCityAndCompany('4,6', '4,25');
		$this->assertGreaterThan( 0, count($result) );
	}
	public function getCourierPointsForCity()
	{
		
	}
	public function testGetSelfDeliveryInfoForCity()
	{
		
	}
	public function testGetDeliveryInfoForPoint()
	{
		
	}
	public function testCheckOrderSelfValues()
	{
		
	}
	public function testCheckOrderCourierValues()
	{
		$order = $this->fixture->getOrder();
	}
	
	
	
	public function testGetSelfPoints()
	{
		$selfpoints = $this->fixture->getSelfPoints( 151185 );
		$this->assertGreaterThan( 0, count($selfpoints) );
	}
	
	public function testCreateSelfOrder()
	{
		$order = $this->fixture->getOrder();
		$selfpoint = $this->fixture->getSelfPoints(151185);
		
		$order->setPoint($selfpoint[0]);
		$order->toName = 'Дима Грушин';
		$order->toPhone = '9999999999';
		
		$order_id = $this->fixture->createSelfOrder();
		
		$this->assertGreaterThan( 0, $order_id );
	}
	
	public function testCreateCourierOrder()
	{
		$order = $this->fixture->getOrder();
		$selfpoint = $this->fixture->getCourierPointsForCity(151185);
		
		$order->setPoint($selfpoint[0]);
		$order->toName = 'Дима Грушин';
		$order->toPhone = '9999999999';
		$order->shopRefnum = 'xxx';
		$order->toStreet = 'Вознесенская';
		$order->toHouse = '1а';
		$order->toFlat = '42';
		$order->toEmail = '';
		$order_id = $this->fixture->createCourierOrder();
		
		$this->assertGreaterThan( 0, $order_id );
	}

    */
}