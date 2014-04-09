<?php

class DDeliveryUITest extends PHPUnit_Framework_TestCase
{
	protected $fixture;
	
	protected function setUp()
	{	
		$shopAdapter = new \DDelivery\Adapter\DShopAdapterTest();
		$this->fixture = new \DDelivery\DDeliveryUI( $shopAdapter );
	}
	
	public function testSaveIntermediateOrder()
	{	
		
		$id = $this->fixture->saveIntermediateOrder( null);
		$this->assertGreaterThan( 0, $id );
		
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
		
		$id = $this->fixture->saveIntermediateOrder(null);
		$this->assertGreaterThan( 0, $id );
	}
	
	public function testSaveIntermediateOrder()
	{
		$this->fixture->initIntermediateOrder(1);
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
	
	}
	public function testGetSelfPointsForCityAndCompany()
	{
		$result = $this->fixture->getSelfPointsForCityAndCompany('4,6', '4,25');
		$this->assertEquals( $result[0]->get('_id'), 2651 );
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
}