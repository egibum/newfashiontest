<?php
    
    class Terapeak_SalesAnalytics_Model_Resource_LinkedChannel extends Mage_Core_Model_Resource_Db_Abstract
    {
        
        protected function _construct()
        {
            $this->_init('terapeak_salesanalytics/linkedchannel', 'store_id');
            $this->_isPkAutoIncrement = false;
        }
        
    }
    
    ?>
