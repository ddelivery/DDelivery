<?php 
class DDeliverySDKTest extends PHPUnit_Framework_TestCase
{
	
    protected $fixture;
	
    protected function setUp()
    {
        $this->fixture = new DDelivery \ DDeliverySDK('4bf43a2cd2be3538bf4e35ad8191365d', true);
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
    }
	
}

?>