<?php
    
    class Terapeak_SalesAnalytics_Model_Request_ProductIds extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        function __construct()
        {
            $this->productIdsDefault = array('upc', 'ean', 'jan', 'isbn', 'mpn', 'brand', 'sku');
        }
        
        public function setModelData($itemData)
        {
            $productIdsDefault = $this->productIdsDefault;
            $productIdsCollection = Mage::getModel('terapeak_salesanalytics/request_customcollection');
            foreach ($productIdsDefault as $key => $value)
            {
                if (array_key_exists($value, $itemData))
                {
                    $tempProdIdInfoArray = array("value" => $itemData[$value], "type" => $value);
                    $productId = Mage::getModel('terapeak_salesanalytics/request_productid')->setModelData($tempProdIdInfoArray);
                    $productIdsCollection->addItem($productId->getData());
                }
            }
            
            return $productIdsCollection->getCollectionData();
        }
        
    }
    
    ?>
