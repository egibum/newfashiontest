<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_Url_Rewrite
    extends Mage_Core_Model_Url_Rewrite
{
    /**
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @return Mage_Core_Model_Url
     */
    public function rewrite(Zend_Controller_Request_Http $request = null,
        Zend_Controller_Response_Http $response = null)
    {
        if (is_null($request)) {
            $request = Mage::app()->getFrontController()->getRequest();
        }

        if (!Mage::helper('ecommerceteam_sln')->forceLayered()) {
            if (true == parent::rewrite($request, $response)) {
                return true;
            }
        }

        /** @var $helper EcommerceTeam_Sln_Helper_Request */
        $helper = Mage::helper('ecommerceteam_sln/request');
        $helper->rewrite($request);

        return true;
    }
}
