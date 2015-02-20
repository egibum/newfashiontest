<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Layer_Filter_Attribute extends EcommerceTeam_Sln_Block_Layer_Filter_Abstract
{
    protected $_optionCollection;

    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = EcommerceTeam_Sln_Helper_Layer::FILTER_MODEL_ATTRIBUTE_DROPDOWN;
    }

    public function getAdvancedOptionCollection()
    {
        if (is_null($this->_optionCollection)) {
            $this->_optionCollection = Mage::getResourceModel('ecommerceteam_sln/attribute_collection');
            $this->_optionCollection->addFieldToFilter('attribute_id', $this->getAttributeModel()->getAttributeId());
        }
        return $this->_optionCollection;
    }

    /**
     * @return string
     */
    public function getRequestVar()
    {
        return Mage::helper('ecommerceteam_sln/layer')->getAttributeRequestVar($this->getAttributeModel());
    }
}
