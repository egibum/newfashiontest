<?php
class CommerceStack_Recommender_Block_Catalog_Product_Edit_Tab_Crosssell extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Crosssell
{
    public function isReadonly()
    {
        return true;
    }
    
    public function getSelectedCrossSellProducts()
    {
        $this->_emptyText = Mage::helper('adminhtml')->__('Not enough page view/order data yet for product. Random cross-sells will be shown.');
        Mage::registry('current_product')->getLinkInstance()->useLinkSourceCommerceStack();
        $currentProduct = Mage::registry('current_product');
        $products = array();
        
        // If this product has a parent, use that instead since we do not produce recommendations
        // for children
        $configurableProductModel = Mage::getModel('catalog/product_type_configurable');
        $parentIdArray = $configurableProductModel->getParentIdsByChild($currentProduct->getId());
        if(count($parentIdArray) > 0)
        {
            $currentProduct = Mage::getModel('catalog/product')->load($parentIdArray[0]);
        }
        
        $products = array();
        foreach ($currentProduct->getCrossSellProducts() as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }
        return $products;
    }
    
    protected function _prepareCollection()
    {
        $currentProduct = $this->_getProduct();
        
        // If this product has a parent, use that instead since we do not produce recommendations
        // for children
        $configurableProductModel = Mage::getModel('catalog/product_type_configurable');
        $parentIdArray = $configurableProductModel->getParentIdsByChild($currentProduct->getId());
        if(count($parentIdArray) > 0)
        {
            $currentProduct = Mage::getModel('catalog/product')->load($parentIdArray[0]);
        }
        
        $collection = Mage::getModel('catalog/product_link')->useCrossSellLinks()
            ->useLinkSourceCommerceStack()
            ->getProductCollection()
            ->setProduct($currentProduct)
            ->setPositionOrder()
            ->addAttributeToSelect('*');

        $collection->addStoreFilter($this->getRequest()->getParam('store'));

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in'=>$productIds));
        }

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('count', array(
            'header'    => Mage::helper('customer')->__('Times Bought Together'),
            'index'     => 'count'
        ));

        $col = $this->getColumn('position');
        $col->setEditable(false);
    }
}