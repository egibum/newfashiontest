<?php
//error_reporting(E_ALL);
    class Terapeak_SalesAnalytics_Model_Request_ProdList extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function _construct()
        {
            parent::_construct();
            $this->setListings(array());
        }
        
        public function setModelData($data)
        {
            
            foreach ($data as $itemData)
            {
                $product = Mage::getModel('catalog/product')->load($itemData['product_id']);
                $this->addListing($product);
            }
            return $this;
        }
        
        public function setHistoricalData($allProds)
        {
            foreach ($allProds as $key => $value) {
                $product = Mage::getModel('catalog/product')->load($value['entity_id']);
                $this->addListing($product);
            }
            return $this;
        }
        
        public function addListing($data)
        {
            $listing = Mage::getModel('terapeak_salesanalytics/request_listing');
            $sellerName = $this->getSellerHandle();
            $itemState = $this->getState();
            $listing->setState($itemState);
            $listing->setSellerHandle($sellerName);
            $listing->setModelData($data);
            $collection = $this->getListings();
            array_push($collection, $listing->getData());
            $this->setListings($collection);
            return $this;
        }
        
    }
    
    ?>
