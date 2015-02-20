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
 * @copyright  Copyright (c) 2011 J2T DESIGN. (http://www.j2t-design.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


require_once 'Mage/Checkout/controllers/CartController.php';
class J2t_Onecheckout_CartController extends Mage_Checkout_CartController
{
    
    public function indexAction()
    {
        
        /*$pos = strpos($this->_getRefererUrl(), 'checkout/onepage');
        if ($pos !== false) {
            $this->getResponse()->setRedirect($this->_getRefererUrl());
            return;
        }
        
        //echo $this->_getRefererUrl();
        //die;
        */
        
        parent::indexAction();
    }
    
    
    protected function _goBack()
    {
        $url = $this->_getRefererUrl();
        
        if (strpos($url, "onepage") !== false){
            Mage::getSingleton('j2tonecheckout/session')->unsetAll();
            Mage::getSingleton('j2tonecheckout/session')->setJ2tMessages(Mage::getSingleton('checkout/session')->getMessages());

            $backUrl = $this->_getRefererUrl();
            $this->getResponse()->setRedirect($backUrl);
            return $this;
        } else {
            return parent::_goBack();
        }
        
    }
    
    
    
    /*protected function _goBack()
    {
        
        if ($returnUrl = $this->getRequest()->getParam('return_url')) {
            // clear layout messages in case of external url redirect
            if ($this->_isUrlInternal($returnUrl)) {
                $this->_getSession()->getMessages(true);
            }
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()) {

            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }*/
}