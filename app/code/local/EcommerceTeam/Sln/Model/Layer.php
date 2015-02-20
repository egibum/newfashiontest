<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

/**
 * @method EcommerceTeam_Sln_Model_Layer setIsSliderEnabled(boolean $value)
 * @method boolean getIsSliderEnabled()
 */

class EcommerceTeam_Sln_Model_Layer extends Mage_Catalog_Model_Layer
{
    protected $selectWithoutFilter = array();

    public function __construct()
    {
        if (!Mage::registry('layer_loaded')) {
            Mage::register('layer_loaded', true);
        }
    }

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $collection->groupByAttribute('entity_id');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * @param string|null $request_var
     * @return bool|Varien_Db_Select
     */
    public function getSelectWithoutFilter($request_var = null)
    {
        if ($request_var) {
            if (isset($this->selectWithoutFilter[$request_var])) {
                return $this->selectWithoutFilter[$request_var];
            } else {
                return false;
            }
        } else {
            return $this->selectWithoutFilter;
        }
    }

    /**
     * Initialize request model
     *
     * @return void
     */
    public function initRequest()
    {
        Mage::getSingleton('ecommerceteam_sln/request');
    }

    /**
     * Initialize filter configuration
     *
     * @return void
     */
    public function initFilters()
    {
        $this->initRequest();
        $filterableAttributes = $this->getFilterableAttributes();
        $productCollection    = $this->getProductCollection();
        /** @var $request EcommerceTeam_Sln_Model_Request */
        $request = Mage::getSingleton('ecommerceteam_sln/request');
        if ($request->getValue('price')) {
            $this->selectWithoutFilter['price'] = clone $productCollection->getSelect();
        }
        if ($request->getValue('cat')) {
            $this->selectWithoutFilter['cat'] = clone $productCollection->getSelect();
        }
        foreach ($filterableAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if($request->getValue($attributeCode, false)){
                $this->selectWithoutFilter[$attributeCode] = clone $productCollection->getSelect();
            }
            if (EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_SLIDER == $attribute->getFrontendType()) {
                $this->setIsSliderEnabled(true);
            }
        }

        return $this;
    }

    /**
     * Get all catalog attributes without attribute set filter
     *
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getAllFilterableAttributes()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }

    /**
     * Add advanced data to collection
     *
     * @param  $collection
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $dataTable = 'ecommerceteam_sln_data';
        $collection = parent::_prepareAttributeCollection($collection);
        $collection->addIsFilterableFilter();
        $collection->getSelect()->joinLeft(
            array($dataTable => Mage::getSingleton('core/resource')->getTableName('ecommerceteam_sln_attribute_data')),
            "`main_table`.`attribute_id` = `{$dataTable}`.`attribute_id`",
            array(
                'frontend_type'     => 'frontend_type',
                'navigation_group'  => 'group_id',
                'comment'           => 'comment',
                'options_limit'     => 'options_limit',
            )
        );

        return $collection;
    }
}
