<?php
    
    class Terapeak_SalesAnalytics_Model_Resource_Historicaldata extends Mage_Core_Model_Resource_Db_Abstract
    {
        
        protected function _construct()
        {
            $this->_init('terapeak_salesanalytics/historicaldata', 'store_id');
            $this->_isPkAutoIncrement = false;
        }
        
    }
    
    ?>
