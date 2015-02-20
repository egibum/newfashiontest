<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_System_Config_Source_Robots
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $helper EcommerceTeam_Sln_Helper_Data */
        $helper = Mage::helper('ecommerceteam_sln');

        return array(
            array(
                'value' => $helper::ROBOTS_INDEX_FOLLOW,
                'label' => $helper->__('Index, Follow')),
            array(
                'value' => $helper::ROBOTS_NOINDEX_NOFOLLOW,
                'label' => $helper->__('Noindex, Nofollow'))
        );
    }

}
