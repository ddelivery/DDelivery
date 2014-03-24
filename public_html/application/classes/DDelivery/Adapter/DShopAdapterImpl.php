<?php 

namespace DDelivery\Adapter;

class DShopAdapterImpl extends DShopAdapter
{
    public function getProductsFromCart()
    {
    	$products = array();
    	$prduct_item = array( 'id' => 1, 'width' => 1, 'height' => 1,
                              'length' => '1', 'weight' => 1, 'price' => 100 );
    	
    	$prduct_item2 = array( 'id' => 1, 'width' => 1, 'height' => 1,
    			'length' => '1', 'weight' => 1, 'price' => 100 );
    	
    	$products[] = $prduct_item;
    	$products[] = $prduct_item2;
    	
    	return $products;
    }
    
    public function getOrderPrice()
    {
    	
    }
}
?>
