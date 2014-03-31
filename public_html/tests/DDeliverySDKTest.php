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
	
    public function testGetDeliveryPoints()
    {
        $result = $this->fixture->deliveryPoints();
        $this->assertTrue($result->success);
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
    
    public function testCalculatorPickup()
    {
    	$result = $this->fixture->calculatorPickupForCompany( 1, 10, 10, 10, 1, 0 );
    	$this->assertTrue($result->success);
    }
    
    public function testCalculatorCurier()
    {
    	$result = $this->fixture->calculatorCourier( 151185, 10, 10, 10, 1, 0 );
    	$this->assertTrue($result->success);
    }
    
    public function testGetAutoCpmpleteCity()
    {
    	$result = $this->fixture->getAutoCompleteCity('Иваново');
    	$this->assertTrue($result->success);
    }
    	
}

?>