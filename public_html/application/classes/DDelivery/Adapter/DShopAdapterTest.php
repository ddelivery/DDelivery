<?php 

namespace DDelivery\Adapter;

class DShopAdapterTest extends DShopAdapter
{
    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
	public function getApiKey()
	{
		return '4bf43a2cd2be3538bf4e35ad8191365d'; 
	}
	
	public function getPathByDB(){
		
		return '/home/mrozk/git/ddelivery_front_module/public_html/tests/ddelivery.db';
	}
	
    public function _getProductsFromCart()
    {
    	$products = array();
    	
    	$products[] = new \DDelivery\Order\DDeliveryProduct( 1, 2, 6, 2, 
    			                                             1, 100, 2, 'Пиджак' );
    	$products[] = new \DDelivery\Order\DDeliveryProduct(2, 3, 1,  
    			                                            1, 1, 200, 1,'Куртка кожанная') ;
    	
    	return $products;
    }
    
    public function getAmount()
    {
    	return 100.5;
    }
    
    
    public function getOrderPrice()
    {
    	
    }

    /**
     * Должен вернуть url до каталога с статикой
     * @return string
     */
    public function getStaticPath()
    {
        // TODO: Implement getStaticPath() method.
    }

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public function getPhpScriptURL()
    {
        // TODO: Implement getPhpScriptURL() method.
    }

}
?>
