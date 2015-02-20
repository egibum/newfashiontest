<?php
    
    class Terapeak_SalesAnalytics_Model_Resource_UserCredentials extends Mage_Core_Model_Resource_Db_Abstract
    {
        
        protected function _construct()
        {
            $this->_init('terapeak_salesanalytics/usercredentials', 'sr_no');
            $this->_isPkAutoIncrement = false;
        }
        
    }
    
    ?>
