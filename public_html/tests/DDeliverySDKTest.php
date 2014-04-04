<?php 
class DDeliverySDKTest extends PHPUnit_Framework_TestCase
{
	
    protected $fixture;
	
    protected function setUp()
    {
        $this->fixture = new DDelivery\Sdk\DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
    }
	
    protected function tearDown()
    {
        $this->fixture = NULL;
    }
    
    public function testGetCityByIp()
    {
        $result = $this->fixture->getCityByIp('188.162.64.72');
        $this->assertTrue($result->success);
        $this->assertEquals(151185, $result->response['city_id']);
    }
    
    public function testGetSelfDeliveryPoints()
    {
    	$result = $this->fixture->getSelfDeliveryPoints( '4,6', '4,25' );
    	$this->assertTrue($result->success);
    }
    
    public function testGetAutoCpmpleteCity()
    {
    	$result = $this->fixture->getAutoCompleteCity('Иваново');
    	$this->assertTrue($result->success);
    }
    public function testCalculatorPickupForCompany()
    {
    	$result = $this->fixture->calculatorPickupForCompany(50, 10, 
    			   10,  10, 1, 0);
    	$this->assertTrue( $result->success );
    }
    public function testCalculatorPickupForCity()
    {
    	$result = $this->fixture->calculatorPickupForCity( 151185, 10, 10, 10, 1, 0 );
    	$this->assertTrue( $result->success );
    }
    public function testAddSelfOrder(){
    	$result = $this->fixture->addSelfOrder( 50, 10, 
    	          10, 10,true, 1, 'Дима Грушин', '9999999999', 
    			  'Товар 1, шт', 0, 0);
    	$this->assertTrue( $result->success );
    	$this->assertGreaterThan( 0, $result->response['order']);
    }
    
    public function testAddCourierOrder()
    {
    	$result = $this->fixture->addCourierOrder( 151185, 17, 
			    			10, 10, 10, 
			    			'xxx', false, 1, 'Дима Грушин', '9999999999', 
			    			'Товар 1, шт', 0, 0, 
			    			'asd asd', 'asd asd asd', '4a');
    	$this->assertTrue( $result->success );
    	$this->assertGreaterThan( 0, $result->response['order']);
    	
    	
    }

    public function testCalculatorCurier()
    {
    	$result = $this->fixture->calculatorCourier( 151185, 10, 10, 10, 1, 0 );
    	$this->assertEquals($result->success, 1);
    }
    
    	
}

?>