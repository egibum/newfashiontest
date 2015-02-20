<?php
//error_reporting(E_ALL);
    
    class Terapeak_SalesAnalytics_Model_Request_Item extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($itemData)
        {
            $this->setItemId($itemData['product_id']);
            $this->setTitle($itemData['name']);
            $this->setListingFees(0);
            
            $this->setQuantitySold((int)$itemData['qty_ordered']);
            $this->setItemPrice($itemData['price']);
            
            $product = Mage::getModel('catalog/product')->load($itemData['product_id']);
            $productIds = Mage::getModel('terapeak_salesanalytics/request_productids')->setModelData($product->getData());
            foreach ($productIds as $key => $value)
            {
                if ($value["type"] == "ean")
                {
                    $productId = $value;
                }
            }
            //$class_name = get_class(Mage::helper('salesanalytics/categoryhelper'));
			//$methods = get_class_methods($class_name);
			//$categories = Mage::helper('salesanalytics/categoryhelper')->categoriesForProduct($itemData['product_id']);
            //if (count($categories) > 0) {
            //    $this->setCategories($categories[0]);
            //}
            $this->setViewURL(Mage::getUrl($product->getUrlPath()));
            $this->setQuantityAvailable((int) ($product->getStockItem()->getData('qty')));
            return $this;
        }
        
    }
    
    ?>
