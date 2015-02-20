<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

/** @var $helper EcommerceTeam_Sln_Helper_Data */
$helper = Mage::helper('ecommerceteam_sln');
if ($helper->isMagentoEnterprise()) {
    class UrlRewrite_Model_Url_Rewrite extends Enterprise_UrlRewrite_Model_Url_Rewrite_Request { }
} else {
    class UrlRewrite_Model_Url_Rewrite extends Mage_Core_Model_Url_Rewrite_Request { }
}
class EcommerceTeam_Sln_Model_Core_Url_Rewrite_Request extends UrlRewrite_Model_Url_Rewrite {

    public function rewrite()
    {
        if (!Mage::helper('ecommerceteam_sln')->forceLayered()) {
            if (false == parent::rewrite()) {
                return false;
            }
        }

        /** @var $helper EcommerceTeam_Sln_Helper_Request */
        $helper = Mage::helper('ecommerceteam_sln/request');
        $helper->rewrite($this->_request);

        return true;
    }
}