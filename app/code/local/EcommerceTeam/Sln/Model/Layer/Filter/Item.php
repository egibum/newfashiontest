<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */


class EcommerceTeam_Sln_Model_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /** @var  array */
    protected $_selectedAttributes;
    /** @var  EcommerceTeam_Sln_Helper_Data */
    protected $_helper;
    /** @var  EcommerceTeam_Sln_Model_Request */
    protected $_request;
    /** @var  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection  */
    protected $_filterableAttributes;
    /** @var  string */
    protected $_url;

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helper               = Mage::helper('ecommerceteam_sln');
        $this->_request              = Mage::getSingleton('ecommerceteam_sln/request');
        $this->_filterableAttributes = $this->getHelper()->getLayer()->getFilterableAttributes();
    }

    /**
     * @return EcommerceTeam_Sln_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return EcommerceTeam_Sln_Model_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        return $this->_filterableAttributes;
    }

    /**
     * Get current selected filters
     *
     * @return array
     */
    public function getSelectedAttributes()
    {
        if (is_null($this->_selectedAttributes)) {
            $this->_selectedAttributes = array();
            $selectedFilters = $this->getHelper()->getLayer()->getState()->getFilters();
            /** @var $filter EcommerceTeam_Sln_Model_Layer_Filter_Item */
            foreach ($selectedFilters as $filter) {
                if ($attribute = $filter->getFilter()->getData('attribute_model')) {
                    $this->_selectedAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }

        return $this->_selectedAttributes;
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        return Mage::helper('ecommerceteam_sln')->getRemoveUrl($this->getFilter()->getRequestVar(), $this->getValue(), $this->getValuePrefix());
    }

    /**
     * @return bool
     */
    public function isLastItem()
    {
        $values = Mage::getSingleton('ecommerceteam_sln/request')->getValue($this->getFilter()->getRequestVar());

        return (1 == count($values) && array_shift($values) == $this->getValue());
    }

    /**
     * Get filter item url
     *
     * @param bool $singleMode
     * @return string
     */
    public function getUrl($singleMode = false)
    {
        if (is_null($this->_url)) {
            $this->_url = Mage::helper('ecommerceteam_sln')->getUrl($this, $singleMode);
        }
        return $this->_url;
    }

    /**
     * @return bool
     */
    public function isBaseRelatedUrl()
    {
        return preg_replace('/\\?.+$/', '', $this->getUrl()) == $this->getRequest()->getOriginalBaseUrl();
    }
}
