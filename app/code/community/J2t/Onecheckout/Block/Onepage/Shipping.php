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
 * @copyright  Copyright (c) 2013 J2T DESIGN. (http://www.j2t-design.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class J2t_Onecheckout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping {
    
    public function getAddress()
    {
        if (version_compare(Mage::getVersion(), '1.5.0', '<=') ){
            return parent::getAddress();
        } else {
            if (is_null($this->_address)) {
                if (!$this->isCustomerLoggedIn()) {
                    $this->_address = $this->getQuote()->getShippingAddress();
                } else {
                    parent::getAddress();
                }
            }
            return $this->_address;
        }
        
    }
}

