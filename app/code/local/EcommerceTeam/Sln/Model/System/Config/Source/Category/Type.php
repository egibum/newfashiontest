<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_System_Config_Source_Category_Type
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {

        $helper = Mage::helper('ecommerceteam_sln');

        return array(
            array(
                'value' => EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_DEFAULT,
                'label' => $helper->__('Default')),
            array(
                'value' => EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_CHECKBOX,
                'label' => $helper->__('Checkbox')),
            array(
                'value' => EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_DROPDOWN,
                'label' => $helper->__('Dropdown')),
        );
    }

}
