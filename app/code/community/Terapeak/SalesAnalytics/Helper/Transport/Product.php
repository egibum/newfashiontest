<?php
    
    class Terapeak_SalesAnalytics_Helper_Transport_Product extends Terapeak_SalesAnalytics_Helper_Transport_Abstract
    {
        
        /**
         * This method will be used to call terapeak product listing end point
         *
         * @param type $session
         * @param type $data
         * @return type
         */
        public function callToListingNotificationEndpoint($session, $data, $channelId)
        {
            $url = Mage::helper('salesanalytics/url')->getListingNotificationUrl();
            $url = $url . "?token=" . $session . "&channelId=" . $channelId;
            $transport = $this->send($url, 'POST', $data);
            return $transport;
        }
        
        public function callToCustomNotificationEndpoint($session, $data, $channelId)
        {
            $url = Mage::helper('salesanalytics/url')->getNotificationUrl();
            $url = $url . "?token=" . $session . "&channelId=" . $channelId;
            $transport = $this->send($url, 'POST', $data);
        }
        
    }
    
    ?>
