<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Layer_Filter_Category extends EcommerceTeam_Sln_Block_Layer_Filter_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = EcommerceTeam_Sln_Helper_Layer::FILTER_MODEL_CATEGORY;
    }

    /**
     * @return string
     */
    public function getRequestVar()
    {
        return Mage::helper('ecommerceteam_sln/layer')->getCategoryRequestVar();
    }
}
