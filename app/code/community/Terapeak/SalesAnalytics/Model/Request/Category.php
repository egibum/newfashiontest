<?php
    
    class Terapeak_SalesAnalytics_Model_Request_Category extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($categoryData)
        {
            $this->setId($categoryData['entity_id']);
            $this->setName($categoryData['name']);
            return $this;
        }
        
    }
    
    ?>
