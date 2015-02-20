<?php

    
    class Terapeak_SalesAnalytics_Helper_Data extends Mage_Core_Helper_Abstract
    {
        public function categoriesForProduct($productId)
        {
            $allCat = array();
            
            $product = $this->retrieveProduct($productId);
            $categoryIds = $product->getCategoryIds();
            
            foreach ($categoryIds as $key => $value)
            {
                $catHeirarchy = $this->categoryHeirarchy($value);
                //add this heirarchy only if it is not already present.
                $this->addCatHeirarchyIfNotPresent($allCat, $catHeirarchy);
            }
            
            $allCat = $this->convertToNormalArray($allCat);
            return $allCat;
        }
        
        public function retrieveProduct($productId)
        {
            $product = Mage::getModel('catalog/product')->load($productId);
            return $product;
        }
        
        private function categoryHeirarchy($categoryId)
        {
            $catHeirarchy = array();
            
            $currentCategory = Mage::getModel('catalog/category')->load($categoryId);
            $category = Mage::getModel('terapeak_salesanalytics/request_category')->setModelData($currentCategory);
            $catHeirarchy[$currentCategory->getLevel()] = $category->getData();
            
            while ($currentCategory->getLevel() != 1)
            {
                $currentCategory = Mage::getModel('catalog/category')->load($currentCategory->getParentId());
                $category = Mage::getModel('terapeak_salesanalytics/request_category')->setModelData($currentCategory);
                $catHeirarchy[$currentCategory->getLevel()] = $category->getData();
            }
            
            return $catHeirarchy;
        }
        
        private function addCatHeirarchyIfNotPresent(&$array, $heirarchy)
        {
            //iterate through the complete array and add the current heirarchy only if it doesnot exist already
            if (count($array) == 0)
            {
                array_push($array, $heirarchy);
            }
            else
            {
                $addHeirarchy = true;
                foreach ($array as $key => $value)
                {
                    //check if this category heirarchy is the one which we are planning on adding
                    $count = 1;
                    while ($count <= count($heirarchy))
                    {
                        if ($heirarchy[$count]["id"] == $value[$count]["id"])
                        {
                            if (array_key_exists($count + 1, $heirarchy) && !array_key_exists($count + 1, $value))
                            {
                                $addHeirarchy = false;
                                $array[$key] = $heirarchy;
                                break;
                            }
                            else if (!array_key_exists($count + 1, $heirarchy) && array_key_exists($count + 1, $value))
                            {
                                $addHeirarchy = false;
                            }
                        }
                        $count++;
                    }
                }
                if ($addHeirarchy)
                {
                    array_push($array, $heirarchy);
                }
            }
        }
        
        private function convertToNormalArray($allCat)
        {
            $tempAllCat = array();
            foreach ($allCat as $key => $value)
            {
                $tempArray = array();
                for ($count = 1; $count <= count($value); $count++)
                {
                    $value1 = $value[$count];
                    array_push($tempArray, $value1);
                }
                
                array_push($tempAllCat, $tempArray);
            }
            
            return $tempAllCat;
        }
        
    }
    
    ?>
