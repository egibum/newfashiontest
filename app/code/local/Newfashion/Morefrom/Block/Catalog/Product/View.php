<?php
class Newfashion_Morefrom_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    public function getMoreFrom($currentCatIds,$manufacturer) {
		$rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
        $categoryCollection = Mage::getResourceModel('catalog/category_collection');
		$categoryCollection->setStoreId(Mage::app()->getStore()->getId())
                             ->addAttributeToSelect('name')
                             ->addAttributeToSelect('url')
                             ->addAttributeToSelect('thumbnail')
                             ->addAttributeToFilter('include_in_menu' , 1)
                             ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
                             ->addAttributeToFilter('entity_id', $currentCatIds)
                             ->addIsActiveFilter();
                             
        $counter = 0;
        $html = '<ul class="more-from-cat">';
        foreach($categoryCollection as $cat):
            if ($counter == 0) { 
                $mainCat['name'] = $cat->getName(); 
                $mainCat['url'] = $cat->getUrl(); 
            } elseif ($manufacturer == $cat->getName()) {
				$brandID = $cat->getId();
                $manufacturerUrl = $cat->getUrl();
				if ($cat->getThumbnail()) {
					$manufacturerImage = Mage::getBaseUrl('media').'catalog/category/'.$cat->getThumbnail(); 
				} else {
					$manufacturerImage = false;
				}
            } else {
               
               $html .= '<li><a href="'.$mainCat['url'].'">' .$mainCat['name'].'</a> > <a href="'.$cat->getUrl().'">' .ucwords(strtolower($cat->getName())).'</a></li>'; 
            }
            $counter++;
        endforeach;
        $html.='</ul>';
        if ($manufacturerUrl != NULL) {
			if ($manufacturerImage == false) {
				$html.='<ul class="more-from-brand"><li><a href="'.$mainCat['url'].'">' .$mainCat['name'].'</a> > <a href="'.$manufacturerUrl.'">' .ucwords(strtolower($manufacturer)).'</a></li></ul>';
			} else {
				$html.='<ul class="more-from-brand"><li class="first"><img src="'.$manufacturerImage.'" alt="" /></li><li><a href="'.$mainCat['url'].'">' .$mainCat['name'].'</a> > <a href="'.$manufacturerUrl.'">' .ucwords(strtolower($manufacturer)).'</a></li></ul>';
			}
        }
		$output['html'] = $html;
		$output['brand_id'] = $brandID;
        return $output;
    }
	
	public function isReplacingBlock($_product) {
		// get the necessary model
		$_helper2 = Mage::getModel('catalog/product_type_configurable')
					->setProduct($_product);
		$_subproducts = $_helper2
						->getUsedProductCollection()
						->addAttributeToSelect('color')
						->addFilterByRequiredOptions();

		// initialise our colours array
		$_colors = array();
		$_color_id = array();

		// look for attribute value of color in each associated product and assign to $_colours array
		foreach ($_subproducts as $_subproduct) {
		  // look for attribute value of color in each associated product and assign to $_colours array

		  $label = $_subproduct->getAttributeText('color');
		  $_colors[] = $label;

		  if (!isset($_color_id[$label])) {
			$_color_id[$label] = $_subproduct->getData('color');
		  }
		}

		// remove dup's
		$_color_swatch = array_unique($_colors);
		return count($_color_swatch);

	}
	
	public function getRandomProductFromBrand($categoryID) {
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSort()
			->addAttributeToFilter('type_id', 'configurable')
			->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryID));
			$products->getSelect()->order(new Zend_Db_Expr('RAND()'));
		$counter = 0; 
		foreach($products as $product):
			if ($counter == 6) { return $productArray; }
			$fullProduct = Mage::getModel('catalog/product')->load($product->getId()); //Product ID
			$images_obj = $this->helper('catalog/image')->init($fullProduct, 'small_image')->resize(150, 150);
			$images[] = (string)$images_obj;
			$productArray[$product->getId()]['image'] = (string)$images_obj;
			$productArray[$product->getId()]['url'] = $fullProduct->getProductUrl();
			$productArray[$product->getId()]['name'] = $fullProduct->getName();
			$counter++;
		endforeach;
		
		
	}
}
?>