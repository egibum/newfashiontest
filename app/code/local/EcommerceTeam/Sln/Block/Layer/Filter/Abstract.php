<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

abstract class EcommerceTeam_Sln_Block_Layer_Filter_Abstract extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        Mage_Core_Block_Abstract::__construct();
    }

    protected function _prepareFilter()
    {
        parent::_prepareFilter();
        if ($attribute = $this->getData('attribute_model')) {
            $this->_filter->setAttributeModel($attribute);
        }
        switch ($this->_filter->getFrontendType()):
            case EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_DEFAULT:
            default:
            $this->_template = 'ecommerceteam/sln/layer/filter/default.phtml';
            break;
            case EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_CHECKBOX:
                $this->_template = 'ecommerceteam/sln/layer/filter/checkbox.phtml';
            break;
            case EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_DROPDOWN:
                $this->_template = 'ecommerceteam/sln/layer/filter/dropdown.phtml';
            break;
            case EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_IMAGE:
                $this->_template = 'ecommerceteam/sln/layer/filter/image.phtml';
            break;
            case EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_INPUT:
                $this->_template = 'ecommerceteam/sln/layer/filter/input.phtml';
            break;
            case EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_SLIDER:
                $this->_template = 'ecommerceteam/sln/layer/filter/slider.phtml';
            break;
        endswitch;
    }
    /**
     * Retrieve filter items
     *
     * @return array
     */
    public function getAllItems()
    {
        return $this->_filter->getAllItems();
    }

    /**
     * Initialize filter model object
     *
     * @return Mage_Catalog_Block_Layer_Filter_Abstract
     */
    public function init()
    {
        $attribute    = $this->getAttributeModel();
        $registryKey  = $this->_filterModelName . ($attribute ? ('_' . $attribute->getAttributeCode()) : '');
        $filter       = Mage::registry($registryKey);

        if ($filter) {
            $this->_filter = $filter;
            $this->_prepareFilter();
        } else {
            parent::_initFilter();
            Mage::register($registryKey, $this->_filter);
        }

        return $this;
    }

    /**
     * @return $this|Mage_Catalog_Block_Layer_Filter_Abstract
     */
    protected function _initFilter()
    {
        $this->_filter = Mage::helper('ecommerceteam_sln/layer')->initializeFilter($this->_filterModelName, $this->getRequestVar());
        $this->_prepareFilter();
        $this->_filter->apply($this->getRequest(), $this);

        return $this;
    }

    public function startJavaScript()
    {
        if (!$this->getParentBlock()->getIsAjax()) {
            echo '<script type="text/javascript">';
        } else {
            ob_start();
        }
    }

    public function endJavaScript()
    {
        if (!$this->getParentBlock()->getIsAjax()) {
            echo '</script>';
        } else {
            $this->getParentBlock()->setJavaScript($this->getParentBlock()->getJavaScript() . ob_get_clean());
        }
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        if($this->getAllItemsCount()){
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function hasSelectedOptions()
    {
        $request = Mage::getSingleton('ecommerceteam_sln/request');
        return $request->getValue($this->getRequestVar());
    }

    /**
     * Retrieve filter items count
     *
     * @return int
     */
    public function getAllItemsCount()
    {
        return $this->_filter->getAllItemsCount();
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->_filter->getComment();
    }

     /**
     * @return string
     */
    abstract public function getRequestVar();
    
    public function getOptionsLimit()
    {
        $helper = Mage::helper('ecommerceteam_sln');
        $defaultLimit = $helper->getConfigData('options_limit');
        $limit = $this->_filter->getOptionsLimit();
        if ($limit === null) {
            $limit = $defaultLimit;
        }

        return $limit;
    }

    /**
     * @return EcommerceTeam_Sln_Helper_Data
     */
    public function getRemoveUrl()
    {
        return Mage::helper('ecommerceteam_sln')->getRemoveUrl($this->getRequestVar());
    }
}

