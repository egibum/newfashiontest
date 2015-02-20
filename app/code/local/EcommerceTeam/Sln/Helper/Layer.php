<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Helper_Layer
    extends EcommerceTeam_Sln_Helper_Abstract
{
    /** @var  Mage_Catalog_Model_Resource_Product_Attribute_Collection */
    protected $_filterableAttributes;
    /** @var  array  */
    protected $_filterableAttributesByGroup = array();
    /** @var  array */
    protected $_filters;

    const FILTER_MODEL_ATTRIBUTE_DROPDOWN = 'ecommerceteam_sln/layer_filter_attribute';
    const FILTER_MODEL_ATTRIBUTE_DECIMAL  = 'ecommerceteam_sln/layer_filter_decimal';
    const FILTER_MODEL_ATTRIBUTE_PRICE    = 'ecommerceteam_sln/layer_filter_price';
    const FILTER_MODEL_CATEGORY           = 'ecommerceteam_sln/layer_filter_category';

    /**
     * @return string
     */
    public function getCategoryRequestVar()
    {
        return 'cat';
    }

    /**
     * @return string
     */
    public function getPriceRequestVar()
    {
        return 'price';
    }

    /**
     * @param Varien_Object $attribute
     * @return mixed
     */
    public function getAttributeRequestVar(Varien_Object $attribute)
    {
        return $attribute->getAttributeCode();
    }

    /**
     * @param string $filterModelName
     * @param string $requestVar
     * @return Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function initializeFilter($filterModelName, $requestVar)
    {
        if (!isset($this->_filters[$requestVar])) {
            /** @var Mage_Catalog_Model_Layer_Filter_Attribute $filter */
            $filter = Mage::getModel($filterModelName);
            $filter->setLayer($this->getLayer());
            $filter->setRequestVar($requestVar);

            $this->_filters[$requestVar] = $filter;
        }

        return $filter;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        if (is_null($this->_filters)) {
            $filters[$this->getCategoryRequestVar()] = $this->initializeFilter(self::FILTER_MODEL_CATEGORY, $this->getCategoryRequestVar());
            foreach ($this->getFilterableAttributes() as $attribute) {
                if ('price' == $attribute->getAttributeCode()) {
                    $requestVar = $this->getPriceRequestVar();
                    $model      = self::FILTER_MODEL_ATTRIBUTE_PRICE;
                } elseif ('decimal' == $attribute->getBackendType()) {
                    $requestVar = $this->getAttributeRequestVar($attribute);
                    $model      = self::FILTER_MODEL_ATTRIBUTE_DECIMAL;
                } else {
                    $requestVar = $this->getAttributeRequestVar($attribute);
                    $model      = self::FILTER_MODEL_ATTRIBUTE_DROPDOWN;
                }

                $filters[$requestVar] = $this->initializeFilter($model, $requestVar);
            }

            $this->_filters = $filters;
        }

        return $this->_filters;
    }

    /**
     * @param $requestVar
     * @return bool|Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function getFilter($requestVar)
    {
        $this->getFilters();

        return isset($this->_filters[$requestVar]) ? $this->_filters[$requestVar] : false;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        if (is_null($this->_filterableAttributes)) {
            $this->_filterableAttributes = $this->getLayer()->getAllFilterableAttributes();
        }

        return $this->_filterableAttributes;
    }

    /**
     * @param string $navigationGroup
     * @return Varien_Data_Collection
     */
    public function getFilterableAttributesByGroup($navigationGroup)
    {
        if (!isset($this->_filterableAttributesByGroup[$navigationGroup])) {
            $collection = new Varien_Data_Collection();
            $attributeCollection = $this->getFilterableAttributes();
            if (EcommerceTeam_Sln_Block_Layer_View::NAVIGATION_GROUP_DEFAULT == $navigationGroup) {
                foreach ($attributeCollection as $key => $attribute) {
                    $_navigationGroup = $attribute->getNavigationGroup();
                    if(!$_navigationGroup || $_navigationGroup == $navigationGroup){
                        $collection->addItem($attribute);
                    }
                }
            } else {
                foreach ($attributeCollection as $key => $attribute) {
                    if($attribute->getNavigationGroup() == $navigationGroup){
                        $collection->addItem($attribute);
                    }
                }
            }
            $this->_filterableAttributesByGroup[$navigationGroup] = $collection;
        }

        return $this->_filterableAttributesByGroup[$navigationGroup];
    }

    /**
     * @param EcommerceTeam_Sln_Model_Layer_Filter_Item $filterItem
     * @return bool
     */
    public function isCategoryClearItem(EcommerceTeam_Sln_Model_Layer_Filter_Item $filterItem)
    {
        if ('cat' == $filterItem->getFilter()->getRequestVar()) {
            return $filterItem->isLastItem();
        }

        return false;
    }
}