<?php
class Newfashion_Morefrom_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    public function getMoreFrom($currentCatIds,$manufacturer) {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                             ->addAttributeToSelect('name')
                             ->addAttributeToSelect('url')
                             ->addAttributeToSelect('thumbnail')
                             ->addAttributeToFilter('entity_id', $currentCatIds)
                             ->addIsActiveFilter();
                             
        $counter = 0;
        $html = '<ul class="more-from-cat">';
        foreach($categoryCollection as $cat):
            if ($counter == 0) { 
                $mainCat['name'] = $cat->getName(); 
                $mainCat['url'] = $cat->getUrl(); 
            } elseif ($manufacturer == $cat->getName()) {
                $manufacturerUrl = $cat->getUrl(); 
                $manufacturerImage = Mage::getBaseUrl('media').'catalog/category/'.$cat->getThumbnail(); 
            } else {
               
               $html .= '<li><a href="'.$mainCat['url'].'">' .$mainCat['name'].'</a> > <a href="'.$cat->getUrl().'">' .ucwords(strtolower($cat->getName())).'</a></li>'; 
            }
            $counter++;
        endforeach;
        $html.='</ul>';
        if ($manufacturerUrl != NULL) {
            $html.='<ul class="more-from-brand"><li class="first"><img src="'.$manufacturerImage.'" alt="" /></li><li><a href="'.$mainCat['url'].'">' .$mainCat['name'].'</a> > <a href="'.$manufacturerUrl.'">' .ucwords(strtolower($manufacturer)).'</a></li></ul>';
        }
        return $html;
    }
	
	public function getBrandCategoryID($currentCatIds,$manufacturer) {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                             ->addAttributeToSelect('name')
                             ->addAttributeToFilter('entity_id', $currentCatIds)
                             ->addIsActiveFilter();
		foreach($categoryCollection as $cat):
		if ($cat->getName() == $manufacturer) {
			return $cat->getId();
		}
		endforeach;
	}
	
	public function getRandomProductFromBrand($name) {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                             ->addAttributeToSelect('name')
                             ->addAttributeToSelect('url')
                             ->addAttributeToSelect('thumbnail')
                             ->addAttributeToFilter('entity_id', $currentCatIds)
                             ->addIsActiveFilter();
        foreach($categoryCollection as $cat):
		
		$cat->getUrl();
		endforeach;
	}
}
?>