<?php
/**
 *
 * @package    DDelivery.Point
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @author  mrozk <mrozk2012@gmail.com>
 */
namespace DDelivery\Point;

/**
 * DDeliveryPointSelf - компания для самовывоза 
 * 
 * Этот клас ассоциируется с команией через которую будет осуществлятся самовывоз
 * у этой компании много точек по городу и все они хранятся в переменной $pointLocation
 *
 * @package  DDelivery.Point
 */
class DDeliveryPointSelf extends DDeliveryAbstractPoint{
	
	/**
	 * масcив с располажением точек по городу для компании
	 * @var array 
	 */
    private $pointLocation = array();
    
    /**
     * фильтрует расположение точек только для текущей компании
     * 
     * @param array $locationConteiner - массив с расположением точек
     * для разных компаний
     * 
     */
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
    
    /**
     * получить масcив с располажением точек по городу для компании
     */
    public function getPointsLocation()
    {
    	return $this->pointLocation;
    }
    
}