<?php
//error_reporting(E_ALL);
    class Terapeak_SalesAnalytics_Model_Request_ProdSell extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($data)
        {
            $this->setChannelId("magento");
            $order = $data->getEvent()->getData('order');
            $items = $order->getAllItems();
            $itemCollection = Mage::getModel('terapeak_salesanalytics/request_customcollection');
            foreach ($items as $itemData)
            {
                $item = Mage::getModel('terapeak_salesanalytics/request_item')->setModelData($itemData);
                $itemCollection->addItem($item->getData());
                
            }
            $this->setItems($itemCollection->getCollectionData());
            $transaction = Mage::getModel('terapeak_salesanalytics/request_transaction')->setModelData($order);
            $this->setTransaction($transaction->getData());
            $shippingData = $order->getShippingAddress()->getData();
            $customerData = $order->getCustomer()->getData();
            $sellerName = $this->getSellerHandle();
            $reqSeller = Mage::getModel('terapeak_salesanalytics/request_seller');
            $reqSeller->setId($sellerName);
            $seller = $reqSeller->setModelData($customerData);
            $this->setSeller($seller->getData());
            if (array_key_exists('firstname', $customerData)) {
            	$buyer = Mage::getModel('terapeak_salesanalytics/request_buyer')->setModelData($customerData);
            } else {
            	$buyer = Mage::getModel('terapeak_salesanalytics/request_buyer')->setModelData($shippingData);
            }
            
            $buyer->setCountryISOCode($shippingData['country_id']);
            $buyer->setState($shippingData['region']);
            $buyer->setsetCity($shippingData['city']);
            $this->setBuyer($buyer->getData());
            $this->setSource("magento");
            return $this;
        }
        
        public function setSellerData($data, $sellerName)
        {
            $sellerInfo = $data->getSeller();
            $sellerInfo['id'] = $sellerName;
            $data->setSeller($sellerInfo);
            
            return $data;
        }
        
        public function setHistoricalTransactions($order)
        {
            $orderDetails = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());
            $items = $orderDetails->getAllVisibleItems();
            $itemCollection = Mage::getModel('terapeak_salesanalytics/request_customcollection');
            foreach ($items as $itemData)
            {
                $item = Mage::getModel('terapeak_salesanalytics/request_item')->setModelData($itemData);
                $itemCollection->addItem($item->getData());
                
            }
            $this->setItems($itemCollection->getCollectionData());
            $transaction = Mage::getModel('terapeak_salesanalytics/request_transaction');
            $transaction->setId($order->getIncrementId());
            $transaction->setType("FixedPrice");
            $transaction->setShipCost($order->getBaseShippingAmount());
            $txDate = new DateTime($order->getCreatedAt());
            $transaction->setTime($txDate->getTimestamp() * 1000);
            $transaction->setCurrencyISOCode($order->getBaseCurrencyCode());
            $shippingData = $orderDetails->getShippingAddress();
            $customerId = $orderDetails->getCustomerId();
            if (is_null($customerId) || empty($customerId)) {
                $customerId = 'guest';
            }
            $customerName = $shippingData['firstname'] . " " . $shippingData['lastname'];
            
            $buyer = Mage::getModel('terapeak_salesanalytics/request_buyer');
            $buyer->setId($customerId);
            $buyer->setEmail($shippingData['email']);
            $buyer->setName($customerName);
            $buyer->setCountryISOCode($shippingData['country_id']);
            $buyer->setState($shippingData['region']);
            $buyer->setCity($shippingData['city']);
            $buyer->setAge(Mage::helper('salesanalytics/util_datetime')->yearsSinceDate($orderDetails->getCustomerDob()));
            $buyer->setGender(Mage::helper('salesanalytics/util_gender')->genderFromCode($orderDetails->getCustomerGender()));
            
            $seller = Mage::getModel('terapeak_salesanalytics/request_seller');
            $seller->setId($this->getSellerHandle());
            $seller->setModelData($order);
            $this->setTransaction($transaction->getData());
            $this->setBuyer($buyer->getData());
            $this->setSeller($seller->getData());
            $this->setSource("magento");
            $this->setChannelId("magento");
            
            return $this;
        }
        
    }
    
    ?>
