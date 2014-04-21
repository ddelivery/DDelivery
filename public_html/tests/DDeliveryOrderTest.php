<?php 
class DDeliveryOrderTest extends PHPUnit_Framework_TestCase
{
	protected $fixture;
	
	protected function setUp()
	{
		$products[] = new \DDelivery\Order\DDeliveryProduct( 1, 2, 6, 2, 
    			                                             1, 100, 2, 'Пиджак' );
    	$products[] = new \DDelivery\Order\DDeliveryProduct( 2, 3, 1,  
    			                                             1, 1, 200, 1,'Куртка кожанная') ;
		$this->fixture = new DDelivery\Order\DDeliveryOrder( $products );
	}
	
	public function testGetProductParams()
	{
		$this->fixture->getProductParams();
		$this->assertEquals($this->fixture->dimensionSide1, 5);
		$this->assertEquals($this->fixture->dimensionSide2, 6);
		$this->assertEquals($this->fixture->dimensionSide3, 3);
	}
}
