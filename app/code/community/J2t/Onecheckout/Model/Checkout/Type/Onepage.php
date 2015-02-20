<?php
/**
 * J2t_Onecheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    J2t_Onecheckout
 * @copyright  Copyright (c) 2011 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class J2t_Onecheckout_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    public function saveBilling($data, $customerAddressId)
    {
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if ($active){
            if (isset($data['is_subscribed']) && !empty($data['is_subscribed'])){
                //$this->getCheckout()->setCustomerIsSubscribed(1);
                //j2tonecheckout
                Mage::getSingleton('j2tonecheckout/session')->setCustomerIsSubscribed(1);
            }
            else {
                //$this->getCheckout()->setCustomerIsSubscribed(0);
                Mage::getSingleton('j2tonecheckout/session')->setCustomerIsSubscribed(0);
            }
        }
        
        return parent::saveBilling($data, $customerAddressId);
    }
}
