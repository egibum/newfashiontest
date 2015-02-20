<?php
    
    class Terapeak_SalesAnalytics_Helper_Transport_Channel extends Terapeak_SalesAnalytics_Helper_Transport_Abstract
    {
        
        const MAGENTO_CHANNEL_TYPE = "magento";
        
        /**
         * This method will give magento channel id for the user
         *
         * @return type
         */
        public function getLinkedMagentoChannelId($storeId)
        {
            $magentoChannelId = NULL;
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $url = Mage::helper('salesanalytics/url')->getUserChannelInfoUrl();
                $url = $url . "?token=" . $session;
                $transport = $this->send($url, 'GET', null);
                $sellerName = Mage::getModel('core/store')->load($storeId)->getName() . "-" . $storeId;
                foreach ($transport['channels'] as $key => $value)
                {
                    if ($value['channelType'] == self::MAGENTO_CHANNEL_TYPE)
                    {
                        $magentoChannelId = $value["channelId"];
                        break;
                    }
                    
                }
                
            }
            return $magentoChannelId;
        }
        
        /**
         * This method will link user to magento channel
         *
         * @param type $data
         * @return type
         */
        public function linkMagentoChannel($data)
        {
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $url = Mage::helper('salesanalytics/url')->getMagentoChannelLinkingEndpointUrl();
                
                $url = $url . "?token=" . $session;
                $data = Mage::getModel("terapeak_salesanalytics/request_channeldata")->setModelData($data);
                
                $transport = $this->send($url, 'POST', $data);
            }
        }
        
        public function getLinkedMagentoChannelInfo($storeId)
        {
            $magentoChannelInfo = array();
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $url = Mage::helper('salesanalytics/url')->getUserChannelInfoUrl();
                $url = $url . "?&token=" . $session;
                $transport = $this->send($url, 'GET', null);
                $sellerName = Mage::getModel('core/store')->load($storeId)->getName() . "-" . $storeId;
                foreach ($transport['channels'] as $key => $value)
                {
                    
                    if ($value["channelType"] == self::MAGENTO_CHANNEL_TYPE)
                    {
                        $magentoChannelInfo['magentoChannelId'] = $value["channelId"];
                        $magentoChannelInfo['magentoSellerName'] = $value["sellerName"];
                        break;
                    }
                }
                
            }
            return $magentoChannelInfo;
        }
        
    }
    
    ?>
