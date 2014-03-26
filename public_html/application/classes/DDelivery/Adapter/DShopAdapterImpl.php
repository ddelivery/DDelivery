<?php 

namespace DDelivery\Adapter;

class DShopAdapterImpl extends DShopAdapter
{
    public function getProductsFromCart()
    {
    	$products = array();
    	$prduct_item = array( 'id' => 1, 'width' => 2, 'height' => 6,
                              'length' => 2, 'weight' => 1, 'price' => 100, 'quantity' => 2 );
    	
    	$prduct_item2 = array( 'id' => 2, 'width' => 3, 'height' => 1,
    			'length' => 4, 'weight' => 1, 'price' => 200, 'quantity' => 1  );
    	
    	$products[] = $prduct_item;
    	$products[] = $prduct_item2;
    	
    	return $products;
    }
    
    public function getOrderPrice()
    {
    	
    }
}
?>
