<?php
// setShopOrderId
use DDelivery\Order\DDeliveryProduct;
include_once(__DIR__ .'/../example/IntegratorShop.php');


class DDeliveryUITest extends PHPUnit_Framework_TestCase
{
	protected $fixture;

    protected $courierOrder;

    protected $selfOrder;

    protected function  _getDemoProducts()
    {
        $products = array();

        $products[] = new DDeliveryProduct(
            1,	//	int $id id товара в системе и-нет магазина
            20,	//	float $width длинна
            13,	//	float $height высота
            25,	//	float $length ширина
            0.5,	//	float $weight вес кг
            1000,	//	float $price стоимостьв рублях
            1,	//	int $quantity количество товара
            'Веселый клоун'	//	string $name Название вещи
        );
        $products[] = new DDeliveryProduct(2, 10, 13, 15, 0.3, 1500, 2, 'Грустный клоун');
        return $products;
    }
	protected function setUp()
	{	
        $shopAdapter = new IntegratorShop();
		$this->fixture = new \DDelivery\DDeliveryUI( $shopAdapter, true );

        $this->courierOrder = new \DDelivery\Order\DDeliveryOrder($this->_getDemoProducts());
        $this->selfOrder = new \DDelivery\Order\DDeliveryOrder($this->_getDemoProducts());

        $this->selfOrder->city = 151184;
        $this->selfOrder->type = 1;
        $this->selfOrder->firstName = 'Дима';
        $this->selfOrder->secondName = 'Грушин';
        $this->selfOrder->toPhone = '9999999999';
        $this->selfOrder->toStreet = 'Вознесенская';
        $this->selfOrder->toHouse = '1а';
        $this->selfOrder->toFlat = '42';
        $this->selfOrder->toEmail = '';
        $this->selfOrder->localId = 1;
        $this->selfOrder->paymentVariant = 'cash';
        $this->selfOrder->localStatus = 'xxx';
        $this->selfOrder->shopRefnum = 14;


        $this->courierOrder->city = 151184;
        $this->courierOrder->type = 2;
        $this->courierOrder->firstName = 'Дима';
        $this->courierOrder->secondName = 'Грушин';
        $this->courierOrder->toPhone = '9999999999';
        $this->courierOrder->toStreet = 'Вознесенская';
        $this->courierOrder->toHouse = '1а';
        $this->courierOrder->toFlat = '42';
        $this->courierOrder->toEmail = '';
        $this->courierOrder->localId = 2;
        $this->courierOrder->paymentVariant = 'cash';
        $this->courierOrder->localStatus = 'xxx';
        $this->courierOrder->shopRefnum = 14;


	}

    public function testGetCourierPointsForCity()
    {
        $points = $this->fixture->getCourierPointsForCity( $this->courierOrder );
        $this->assertGreaterThan( 0, count( $points ) );
    }

    public function testGetSelfPoints()
    {

        $selfpoints = $this->fixture->getSelfPoints( $this->selfOrder );
        $this->assertGreaterThan( 0, count($selfpoints) );
    }

    public function testSaveFullOrder()
    {
        $id = $this->fixture->saveFullOrder($this->courierOrder);
        $this->assertGreaterThan( 0, $id );
    }

    public function testGetDDOrderStatus()
    {
        $orderStatus = $this->fixture->getDDOrderStatus(1188);
        $this->assertGreaterThan( 0, $orderStatus );
    }

    public function testGetLocalStatusByDD()
    {
        $status = $this->fixture->getLocalStatusByDD(20);
        $this->assertGreaterThan( 0, $status );
    }



    public function testValidateOrderToGetPoints()
    {
        $this->selfOrder->city = 0;
        $notValid = $this->fixture->_validateOrderToGetPoints( $this->selfOrder );
        $this->assertFalse( $notValid );
    }

    public function testCreateSelfOrder()
    {
        $pointself = $this->fixture->getSelfPoints($this->selfOrder);
        $this->selfOrder->setPoint($pointself[0]);
        $ddID = $this->fixture->createSelfOrder($this->selfOrder);

        $this->assertGreaterThan( 0, $ddID );
    }


    public function testCreateCourierOrder()
    {
        $pointcoirier = $this->fixture->getCourierPointsForCity($this->courierOrder);
        $this->courierOrder->setPoint($pointcoirier[0]);
        $ddID = $this->fixture->createCourierOrder($this->courierOrder);
        $this->assertGreaterThan( 0, $ddID );
    }

    public function testGetPullOrdersStatus()
    {
        $pointcoirier = $this->fixture->getCourierPointsForCity($this->courierOrder);
        $this->courierOrder->setPoint($pointcoirier[0]);
        $ddID = $this->fixture->createCourierOrder($this->courierOrder);
    }
    /*
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