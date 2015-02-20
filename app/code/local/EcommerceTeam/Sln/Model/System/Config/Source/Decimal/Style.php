<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_System_Config_Source_Decimal_Style
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => EcommerceTeam_Sln_Model_Url::URL_DECIMAL_STYLE_AS_ALL,
                'label' => Mage::helper('ecommerceteam_sln')->__('Like other attributes')
            ),
            array(
                'value' => EcommerceTeam_Sln_Model_Url::URL_DECIMAL_STYLE_QUERY,
                'label' => Mage::helper('ecommerceteam_sln')->__('Always in query string')
            ),
        );
    }

}