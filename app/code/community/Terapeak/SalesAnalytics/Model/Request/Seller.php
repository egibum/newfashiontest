<?php
    
    class Terapeak_SalesAnalytics_Model_Request_Seller extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($customerData)
        {
            $this->setEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
            $this->setName(Mage::getStoreConfig('trans_email/ident_general/name'));
            $this->setCountryISOCode(Mage::getStoreConfig('general/country/default'));
            return $this;
        }
        
    }
    
    ?>
