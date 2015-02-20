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

$active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
if ($active){
    class J2t_Onecheckout_OnepageController_Abstract extends Mage_Checkout_Controller_Action {}
} else {
    require_once 'Mage/Checkout/controllers/OnepageController.php';
    class J2t_Onecheckout_OnepageController_Abstract extends Mage_Checkout_OnepageController {
        public function saveShippingMethodAction()
        {
            return parent::saveShippingMethodAction();
        }
        public function savePaymentAction()
        {
            return parent::savePaymentAction();
        }
        public function paymentMethodAction()
        {
            return parent::paymentMethodAction();
        }
    }
}

class J2t_Onecheckout_OnepageController extends J2t_Onecheckout_OnepageController_Abstract
{
    
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    public function socolissimoIframeAction()
    {
        $_helper = Mage::helper('socolissimosimplicite');
        $script = '<script type="text/javascript">
            var urlFormSoColissimoSimplicite  = "'.$_helper->getFormUrl().'";
            // Remove ending slash for iframe src
            urlFormSoColissimoSimplicite = urlFormSoColissimoSimplicite.replace(/\\/$/, \'\');
            var socoIframe = document.getElementById(\'socolissimosimplicite_iframe\');
            socoIframe.src = urlFormSoColissimoSimplicite;
            socoIframe.style.display = \'block\';
            </script>';
        
        echo '<html><head></head><body><iframe id="socolissimosimplicite_iframe" src="about:blank" style="width:572px; height:1100px; border: 0 none; display:none;"></iframe>'.$script.'</body></html>';
        die;
    }
    
    public function saveShippingMethodAction()
    {
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if (!$active){
            return parent::saveShippingMethodAction(); 
        }
        if (Mage::getConfig()->getModuleConfig('LaPoste_SoColissimoSimplicite')->is('active', 'true')){
            $process_socolissimo = false;
            //get current quote shipping method
            $socolissimo_helper = Mage::helper('socolissimosimplicite');
            $current_shipping_method = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
            //check if selected shipping method != soColissimo
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost('shipping_method', '');
                
                if ($data != $current_shipping_method && $data == $socolissimo_helper->getRateCode()){
                    $process_socolissimo = true;
                }
            }
            
            //echo Mage::getSingleton('checkout/session')->getData('socolissimosimplicite_checkout_onepage_nextstep').' != payment'." && {$socolissimo_helper->getRateCode()} != $current_shipping_method";
            
            
            if( (Mage::getSingleton('checkout/session')->getData('socolissimosimplicite_checkout_onepage_nextstep') != 'payment' || $socolissimo_helper->getRateCode() != $current_shipping_method) || $process_socolissimo){
                $this->defaultSaveShippingMethod();
            } else {
                echo "Method soColissimo";
                die;
            }
        } /*else if (Mage::getConfig()->getModuleConfig('Kiala_LocateAndSelect')->is('active', 'true') && strpos(Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod(), "kiala") !== false)){
            //TODO: verify if the user manually change this to something else
            echo "Method kiala";
            die;
        } */ else {
            $this->defaultSaveShippingMethod();
        }
    }
    
    protected function defaultSaveShippingMethod()
    {
        /*if ($this->_expireAjax()) {
            return;
        }*/
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                /*$result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );*/
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
    
    public function savePaymentAction()
    {
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if (!$active){
            return parent::savePaymentAction(); 
        }
        if (!$this->getRequest()->isPost()) {
            $this->_ajaxRedirectResponse();
            return;
        }
        try {
            if (Mage::getConfig()->getModuleConfig('LaPoste_SoColissimoSimplicite')->is('active', 'true')){
                Mage::getSingleton('checkout/session')->setData('socolissimosimplicite_flag', true);
            }
            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            
            if ($data == array()){
                echo "Not Available";
                die;
            }
            
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
            
            if (Mage::getConfig()->getModuleConfig('LaPoste_SoColissimoSimplicite')->is('active', 'true')){
                //parent::savePaymentAction();
                Mage::getSingleton('checkout/session')->setData('socolissimosimplicite_flag', false);
            }
            
            
            
            /*$result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);*/
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function paymentMethodAction()
    {
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if (!$active){
            return parent::paymentMethodAction(); 
        }
        /*if ($this->_expireAjax()) {
            return;
        }*/
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    public function couponAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->getMessages(true);
    }
    
    public function rewardAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->getMessages(true);
    }
    
    public function reviewAction()
    {
        //$quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote = Mage::getModel('checkout/cart')->getQuote();
        //$method->isAvailable($this->getQuote());
        if ($quote->getPayment()){
        //if ($quote->getPayment() && $quote->getPayment()->getMethodInstance()){
            if (!$quote->getPayment()->hasMethodInstance()){
                $quote->getPayment()->hasMethodInstance(true);
            }
        }
        if ($quote->getId()){
            $quote->setTotalsCollectedFlag(false)->collectTotals();
            $quote->save();
        } 
        
        if (!Mage::helper('checkout/cart')->getCart()->getItemsCount()){
            echo 'NO-ITEMS';
            die;
        }
        
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function j2tsaveBillingAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote->getId()){
            $quote->setTotalsCollectedFlag(false)->collectTotals();
            $quote->save();
        } 
        if ($this->getRequest()->isPost()) {
            if (!Mage::getSingleton('checkout/session')->getQuote()->isVirtual()) {
                //$data = $this->getRequest()->getPost('billing', array());
                $data = $this->getRequest()->getPost();
                
                $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;
                
                //print_r($data);
                
                if (isset($data['country_id'])){
                    //$usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;
                    $cart = Mage::getSingleton('checkout/cart');
                    $quote = $cart->getQuote();
                    $quote->getShippingAddress()->setCountryId($data['country_id']);
                    if ($usingCase){
                        $quote->getBillingAddress()->setCountryId($data['country_id']);
                    } else if (isset($data['billing_country_id'])){
                        if ($data['billing_country_id'] != ''){
                            $quote->getBillingAddress()->setCountryId($data['billing_country_id']);
                        }
                    }
                    if ($data['region_id'] != ''){
                        $quote->getShippingAddress()->setRegionId($data['region_id']);
                        if ($usingCase){
                            $quote->getBillingAddress()->setRegionId($data['region_id']);
                        } else if (isset($data['billing_region_id'])){
                            if ($data['billing_region_id'] != ''){
                                $quote->getBillingAddress()->setRegionId($data['billing_region_id']);
                            }
                        }
                    }
                    if ($data['postcode'] != ''){
                        $quote->getShippingAddress()->setPostcode($data['postcode']);
                        if ($usingCase){
                            $quote->getBillingAddress()->setPostcode($data['postcode']);
                        } else if (isset($data['billing_postcode'])){
                            if ($data['billing_postcode'] != ''){
                                $quote->getBillingAddress()->setPostcode($data['billing_postcode']);
                            }
                        }
                    }
                    
                    if ($usingCase){
                        $quote->getShippingAddress()->setSameAsBilling(1);
                    } else {
                        $quote->getShippingAddress()->setSameAsBilling(0);
                    }
                    
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                    $quote->getShippingAddress()->collectShippingRates();
                    
                    $quote->save();
                } elseif (isset($data['address_id'])) {
                    $customerAddress = Mage::getModel('customer/address')->load($data['address_id']);
                    if ($customerAddress->getId()) {
                        $cart = Mage::getSingleton('checkout/cart');
                        $quote = $cart->getQuote();
                        $quote->getShippingAddress()->setCountryId($customerAddress->getCountryId());
                        if ($customerAddress->getRegionId() != ''){
                            $quote->getShippingAddress()->setRegionId($customerAddress->getRegionId());
                        }
                        if ($customerAddress->getPostCode() != ''){
                            $quote->getShippingAddress()->setPostcode($customerAddress->getPostCode());
                        }
                        $quote->getShippingAddress()->setCollectShippingRates(true);
                        $quote->getShippingAddress()->collectShippingRates();
                        $quote->save();
                    }
                } elseif (isset($data['reset'])) {
                    echo 'Reset ';
                    $cart = Mage::getSingleton('checkout/cart');
                    $quote = $cart->getQuote();
                    
                    $shipping = $quote->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();

                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue)
                            && !isset($data[$shippingKey])) {
                            $shipping->unsetData($shippingKey);
                        }
                    }
                    
                    $shipping->setCollectShippingRates(true)
                        ->save();
                }                
            }
        }
        echo 'OK';
        die;
    }
    
}
