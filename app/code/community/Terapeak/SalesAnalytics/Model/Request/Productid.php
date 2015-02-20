<?php
    
    class Terapeak_SalesAnalytics_Model_Request_ProductId extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($prodIdInfoArray)
        {
            
            $this->setValue($prodIdInfoArray['value']);
            
            $this->setType($prodIdInfoArray['type']);
            return $this;
        }
        
    }
    
    ?>
