<?php

class MDN_BarcodeLabel_Helper_List extends Mage_Core_Helper_Abstract {
    
    /**
     * 
     */
    public function getCount()
    {
       return Mage::getModel('BarcodeLabel/List')->getCollection()->getSize(); 
    }
    
    /**
     * 
     */
    public function getAvailableCount()
    {
        return 0;
    }
    
}
