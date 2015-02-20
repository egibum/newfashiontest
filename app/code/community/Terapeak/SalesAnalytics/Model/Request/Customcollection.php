<?php
    
    class Terapeak_SalesAnalytics_Model_Request_CustomCollection extends Mage_Core_Model_Abstract
    {
        
        protected function _construct()
        {
            $this->_init('terapeak_salesanalytics/request_customcollection');
            $this->setCollectionData(array());
        }
        
        public function addItem($data)
        {
            $collectionArray = $this->getCollectionData();
            array_push($collectionArray, $data);
            $this->setCollectionData($collectionArray);
        }
        
    }
    
    ?>
