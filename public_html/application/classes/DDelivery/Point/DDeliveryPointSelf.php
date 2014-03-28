<?php
namespace DDelivery\Point;

class DDeliveryPointSelf extends DDeliveryAbstractPoint{
	
    private $pointLocation = array();
    
    public function filterLocationInfo( $locationConteiner = array() )
    {	
    	$companyID = $this->getDeliveryInfo()->get('delivery_company');
    	
    	foreach ( $locationConteiner as $item  )
    	{
    	    if( $companyID == $item['company_id'])
    	    {
    	    	$this->pointLocation[] = $item;
    	    }
    	}
    }
    
    public function getPointsLocation()
    {
    	return $this->pointLocation;
    }
    
}