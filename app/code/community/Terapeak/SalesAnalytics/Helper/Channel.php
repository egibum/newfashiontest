<?php
    
    class Terapeak_SalesAnalytics_Helper_Channel extends Mage_Core_Helper_Abstract
    {
        public function _construct()
        {
            parent::_construct();
        }
        
        public function loadHistoricalTransactions($page, $products)
        {
            $result = false;
            if (!is_null($page) && !empty($page) && !is_null($products) && !empty($products)) {
                $prodSell = Mage::getModel('terapeak_salesanalytics/request_prodsell');
                $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
                if (!is_null($session) && !empty($session))
                {
                    $channelInfo = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelInfo(Mage::app()->getStore()->getStoreId());
                    $channelId = $channelInfo['magentoChannelId'];
                    $sellerName = $channelInfo['magentoSellerName'];
                    $prodSell->setSellerHandle($sellerName);
                    $transport = Mage::helper('salesanalytics/transport_product');
                    $orderCollections = Mage::getResourceModel('sales/order_collection')->addAttributeToSelect('*')->setPage($page, $products);
                    $transactions = array();
                    try {
                        foreach ($orderCollections as $order) {
                			$txData = $prodSell->setHistoricalTransactions($order);
                            array_push($transactions, $txData->getData());
                        }
                        $collection = array('transactions' => $transactions);
                        $transport->callToCustomNotificationEndpoint($session, $collection, $channelId);
                        $result = true;
                    } catch (Exception $ex) {
                		Mage::log('Exception when trying to send historical transaction data.');
                		Mage::log($ex);
                	}
                }
            }
            return $result;
        }
        
        public function loadProductCatalogue($page, $products)
        {
            $result = false;
            if (!is_null($page) && !empty($page) && !is_null($products) && !empty($products)) {
                $prodList = Mage::getModel('terapeak_salesanalytics/request_prodlist');
                $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
                if (!is_null($session) && !empty($session))
                {
                    $allProds = Mage::getResourceModel('catalog/product_collection')->setPage($page, $products)->getData();
                    $channelInfo = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelInfo(Mage::app()->getStore()->getStoreId());
                    $channelId = $channelInfo['magentoChannelId'];
                    $sellerName = $channelInfo['magentoSellerName'];
                    $prodList->setSellerHandle($sellerName);
                    $transport = Mage::helper('salesanalytics/transport_product');
                    try {
                        $data = $prodList->setHistoricalData($allProds);
                        $transport->callToListingNotificationEndpoint($session, $data, $channelId);
                        $result = true;
                    } catch (Exception $ex) {
                		Mage::log('Exception when trying to send historical transaction data.');
                		Mage::log($ex);
                	}
                }
            }
            return $result;
        }
        
        public function isHistoryLoaded() {
            $result = true;
            $storeId = Mage::app()->getStore()->getStoreId();
            $historicalData = Mage::getModel('terapeak_salesanalytics/historicaldata')->load($storeId);
            if (is_null($historicalData->getOrdersFilled()) || is_null($historicalData->getProdsFilled()) || $historicalData->getOrdersFilled() < $historicalData->getOrdersAvail() || $historicalData->getProdsFilled() < $historicalData->getProdsAvail()) {
                $result = false;
            }
            return $result;            
        }
        
        public function initializeHistoricalCounts() {
            $storeId = Mage::app()->getStore()->getStoreId();
            $historicalData = Mage::getSingleton('terapeak_salesanalytics/historicaldata');
            $existStoreData = $historicalData->load($storeId);
            if (is_null($existStoreData) || empty($existStoreData) || ($existStoreData->getStoreId() != $storeId)) {
                $prodFilled = 0;
                $prodAvail = Mage::getResourceModel('catalog/product_collection')->getSize();
                $orderAvail = Mage::getResourceModel('sales/order_collection')->addAttributeToSelect('*')->getSize();
                $orderFilled = 0;
                $historicalData->setProdsAvail($prodAvail);
                $historicalData->setProdsFilled($prodFilled);
                $historicalData->setOrdersAvail($orderAvail);
                $historicalData->setOrdersFilled($orderFilled);
                $historicalData->setStoreId($storeId);
                $historicalData->save();
            }
        }
        
        public function loadHistoryAction() {
            $rows = 100;
            $storeId = Mage::app()->getStore()->getStoreId();
            $historicalData = Mage::getModel('terapeak_salesanalytics/historicaldata')->load($storeId);
            $prodsAvail = $historicalData->getProdsAvail();
            $ordersAvail = $historicalData->getOrdersAvail();
            $prodsFilled = $historicalData->getProdsFilled();
            $ordersFilled = $historicalData->getOrdersFilled();
            if ($prodsFilled < $prodsAvail) {
                $prodPage = (int) ($prodsFilled / $rows);
                $prodPage++;
                $result = $this->loadProductCatalogue($prodPage, $rows);
                if ($result) {
                    $historicalData->setProdsFilled($this->getLoadedNumber($prodPage, $rows, $prodsAvail));
                }
            }
            if ($ordersFilled < $ordersAvail) {
                $orderPage = (int) ($ordersFilled / $rows);
                $orderPage++;
                $result = $this->loadHistoricalTransactions($orderPage, $rows);
                if ($result) {
                    $historicalData->setOrdersFilled($this->getLoadedNumber($orderPage, $rows, $ordersAvail));
                }
            }
            $historicalData->save();
            return $historicalData;
        }
        
        public function getLoadedNumber($page, $rows, $avail) {
            $result = $page * $rows;
            if ($result > $avail) {
                $result = $avail;
            }
            return $result;
        }
        
    }
    
    ?>