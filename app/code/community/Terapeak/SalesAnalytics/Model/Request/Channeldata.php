<?php
    
    class Terapeak_SalesAnalytics_Model_Request_ChannelData extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($data)
        {
            $this->setSellerName($data['sellerName']);
            $this->setSiteName($data['siteName']);
            return $this;
        }
        
    }
    
    ?>
