<?php
    
    class Terapeak_SalesAnalytics_Model_Observer
    {
        
        /**
         * This method will handle magento checkout_submit_all_after event for terapeak salesanalytics product selling data update
         * @param Varien_Event_Observer $observer
         *
         */
        public function checkoutSubmitAllAfter(Varien_Event_Observer $observer)
        {
            $prodSell = Mage::getModel('terapeak_salesanalytics/request_prodsell');
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $storeId = Mage::app()->getStore()->getStoreId();
                $channelInfo = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelInfo($storeId);
                $channelId = $channelInfo['magentoChannelId'];
                $sellerName = $channelInfo['magentoSellerName'];
                $prodSell->setSellerHandle($sellerName);
                $data = $prodSell->setModelData($observer);
                $txArray = array('0' => $data->getData());
                $transactions = array('transactions' => $txArray);
                Mage::helper('salesanalytics/transport_product')->callToCustomNotificationEndpoint($session, $transactions, $channelId);
                $items = $observer->getOrder()->getAllItems();
                $prodList = Mage::getModel('terapeak_salesanalytics/request_prodlist');
                
                $prodList->setSellerHandle($sellerName);
                $dataWithSellerName = $prodList->setModelData($items);
                //call to update prod listing
                $transport = Mage::helper('salesanalytics/transport_product')->callToListingNotificationEndpoint($session, $dataWithSellerName, $channelId);
            }
        }
        
        /**
         * This method will handle magento catalog_Product_Save_Commit_After event for terapeak salesanalytics product listing data update
         * @param Varien_Event_Observer $observer
         *
         */
        public function catalogProductSaveCommitAfter(Varien_Event_Observer $observer)
        {
            $prodList = Mage::getModel('terapeak_salesanalytics/request_prodlist');
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $storeId = Mage::app()->getStore()->getStoreId();
                $channelInfo = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelInfo($storeId);
                $channelId = $channelInfo['magentoChannelId'];
                $sellerName = $channelInfo['magentoSellerName'];
                $prodList->setSellerHandle($sellerName);
                $data = $prodList->addListing($observer->getProduct());
                $transport = Mage::helper('salesanalytics/transport_product')->callToListingNotificationEndpoint($session, $data, $channelId);
            }
        }
        
        /**
         * This method will handle magento catalog_product_delete_after_done event for terapeak salesanalytics product selling data update
         * @param Varien_Event_Observer $observer
         *
         */
        public function catalogProductDeleteAfterDone(Varien_Event_Observer $observer)
        {
            $prodList = Mage::getModel('terapeak_salesanalytics/request_prodlist');
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $storeId = Mage::app()->getStore()->getStoreId();
                $channelInfo = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelInfo($storeId);
                $channelId = $channelInfo['magentoChannelId'];
                $sellerName = $channelInfo['magentoSellerName'];
                $prodList->setSellerHandle($sellerName);
                $prodList->setState('Inactive');
                $data = $prodList->addListing($observer->getProduct());
                $transport = Mage::helper('salesanalytics/transport_product')->callToListingNotificationEndpoint($session, $data, $channelId);
            }
            
        }
    }
    ?>

