<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Layer_Filter_Decimal extends EcommerceTeam_Sln_Block_Layer_Filter_Abstract
{

    protected $_minMaxValue = array();

    /**
     * Initialize Decimal Filter Model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = EcommerceTeam_Sln_Helper_Layer::FILTER_MODEL_ATTRIBUTE_DECIMAL;
    }

    /**
     * Get current minimal price
     *
     * @return float
     */
    public function getMinPriceInt()
    {
        return floatval($this->_filter->getMinValue());
    }

    /**
     * Get current maximal value
     *
     * @return float
     */
    public function getMaxPriceInt()
    {
        return floatval($this->_filter->getMaxValue());
    }

    /**
     * @return string
     */
    public function getRequestVar()
    {
        return Mage::helper('ecommerceteam_sln/layer')->getAttributeRequestVar($this->getAttributeModel());
    }
}
