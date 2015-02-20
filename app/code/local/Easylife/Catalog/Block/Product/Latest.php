<?php 
class Easylife_Catalog_Block_Product_Latest extends Mage_Catalog_Block_Product_Abstract{
	const DEFAULT_LIMIT = 9;
	protected function _beforeToHtml() {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('entity_id', 'desc')
            ->setPageSize($this->getLimit())
            ->setCurPage(1)
        ;

        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }
 	public function getLimit(){
 		if (!$this->getData('limit')){
 			$this->setData('limit', self::DEFAULT_LIMIT);
 		}
 		return $this->getData('limit');
 	}   
}
