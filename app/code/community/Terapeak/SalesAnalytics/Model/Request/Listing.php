<?php
    
    class Terapeak_SalesAnalytics_Model_Request_Listing extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($data)
        {
            $dateUtil = Mage::helper('salesanalytics/util_datetime');
            $this->setItemId($data->getData('entity_id'));
            $channel = Mage::app()->getStore($data->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            $this->setChannel($channel);
            $this->setChannelType('magento');
            $currentDate = $dateUtil->currentDate();
            $this->setListingStartDate($dateUtil->toAPIDateFormat($data->getData('created_at')));
            $this->setLastUpdateDate($dateUtil->toAPIDateFormat($currentDate));
            $existState = $this->getState();
            if (is_null($existState)) {
                $this->setState($data->getData('status') == 1 ? 'Active' : 'Inactive');                
            }
            if ($this->getState() == 'Inactive') {
                $this->setListingEndDate($dateUtil->toAPIDateFormat($currentDate));                
            }
            
            $this->setTitle($data->getData('name'));
            $this->setInStockAmount((int) Mage::getModel('cataloginventory/stock_item')->load($data->getData('entity_id'), "product_id")->getQty());
            
            $this->setPerItemPrice($data->getData('price'));
            $this->setCurrencyISO('USD');
            
            $this->setListingType('FixedPrice');
            
            //$categories = Mage::helper('salesanalytics/categoryhelper')->categoriesForProduct($data->getData('entity_id'));
            //$this->setCategories($categories);
            
            $productIds = Mage::getModel('terapeak_salesanalytics/request_productids');
            $this->setProductIds($productIds->setModelData($data->getData()));
        }
        
    }
    
    ?>
