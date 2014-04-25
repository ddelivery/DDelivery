<?php
// setShopOrderId

class DDeliveryUITest extends PHPUnit_Framework_TestCase
{
	protected $fixture;
	
	protected function setUp()
	{	
		$shopAdapter = new \DDelivery\Adapter\DShopAdapterTest();
		$this->fixture = new \DDelivery\DDeliveryUI( $shopAdapter );
        $order = $this->fixture->getOrder();
        $order->city = 151184;
        $order->type = 1;
        $order->firstName = 'Дима';
        $order->secondName = 'Грушин';
        $order->toPhone = '9999999999';
        $order->toStreet = 'Вознесенская';
        $order->toHouse = '1а';
        $order->toFlat = '42';
        $order->toEmail = '';
        $order->localId = 2;
        $order->localId = 2;
        $order->paymentVariant = 'cash';
        $order->localStatus = 'xxx';
        $order->shopRefnum = 14;



	}


    public function testGetCourierPointsForCity()
    {
        $order = $this->fixture->getOrder();
        $order->city = 151185;
        $points = $this->fixture->getCourierPointsForCity( $order );
        $this->assertGreaterThan( 0, count( $points ) );

    }

    public function testGetSelfPoints()
    {
        $order = $this->fixture->getOrder();
        $order->city = 151185;
        $selfpoints = $this->fixture->getSelfPoints( $order );
        $this->assertGreaterThan( 0, count($selfpoints) );
    }

    public function testSaveFullOrder()
    {
        $order = $this->fixture->getOrder();
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
        $orderStatus = $this->fixture->getDDOrderStatus(947);
        $this->assertGreaterThan( 0, $orderStatus );
    }

    public function testInitIntermediateOrder()
    {
        $orderList = $this->fixture->initIntermediateOrder(1);
        $this->assertEquals(1, count($orderList));
    }
    public function testGetMinMaxPriceAndPeriodDelivery()
    {
        $order = $this->fixture->getOrder();
        $selfPoints = $this->fixture->getCourierPointsForCity( $order );
        $info = $this->fixture->getMinMaxPriceAndPeriodDelivery( $selfPoints );
        $this->assertGreaterThan( 0, $info['max_price'] );
    }

    public function testGetMinPriceAndPeriodCourier()
    {
        $order = $this->fixture->getOrder();
        $info = $this->fixture->getMinPriceAndPeriodCourier( $order );
        $this->assertGreaterThan( 0, $info['max_price'] );
    }

    public function testGetMinPriceAndPeriodSelf()
    {
        $order = $this->fixture->getOrder();
        $info = $this->fixture->getMinPriceAndPeriodSelf( $order );
        $this->assertGreaterThan( 0, $info['max_price'] );
    }

    public function testValidateOrderToGetPoints()
    {
        $order = $this->fixture->getOrder();
        $order->city = 0;
        $notValid = $this->fixture->_validateOrderToGetPoints( $order );
        $this->assertFalse( $notValid );
    }

    public function testCreateSelfOrder()
    {
        $order = $this->fixture->getOrder();
        $pointself = $this->fixture->getSelfPoints($order);
        $order->setPoint($pointself[0]);
        $order->type = 1;
        $ddID = $this->fixture->createSelfOrder($order);
        $this->assertGreaterThan( 0, $ddID );
    }

    public function testCreateCourierOrder()
    {
        $order = $this->fixture->getOrder();
        $pointcoirier = $this->fixture->getCourierPointsForCity($order);
        $order->setPoint($pointcoirier[0]);
        $order->type = 2;
        $ddID = $this->fixture->createCourierOrder($order);
        $this->assertGreaterThan( 0, $ddID );
    }

	public function testSaveIntermediateOrder()
	{
        $order = $this->fixture->getOrder();
        $id = $this->fixture->saveIntermediateOrder($order);
        $this->assertGreaterThan( 0, $id );
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