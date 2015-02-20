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
if (version_compare(Mage::getVersion(), '1.7.0', '>=') ){
class J2t_Onecheckout_Model_Observer_Abstract extends Mage_Captcha_Model_Observer {}
} else {
class J2t_Onecheckout_Model_Observer_Abstract {}
}
class J2t_Onecheckout_Model_Observer extends J2t_Onecheckout_Model_Observer_Abstract {
//class J2t_Onecheckout_Model_Observer extends Mage_Captcha_Model_Observer {
    
    /*protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }*/
    
    public function verifyRewardBlock($observer){
        $block = $observer->getBlock();
        if (!isset($block)) return;
        
        if (Mage::helper('core')->isModuleEnabled("Rewardpoints") 
                && $block->getType() == 'checkout/cart_coupon' 
                && (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'checkout' || Mage::app()->getFrontController()->getRequest()->getRouteName() == 'j2tonecheckout')
                && Mage::app()->getRequest()->getControllerName() == 'onepage'
                && (Mage::app()->getRequest()->getActionName() == 'index' || Mage::app()->getRequest()->getActionName() == 'coupon')
                && ($block->getNameInLayout() == 'checkout.cart.coupon' || $block->getNameInLayout() == 'j2t.onecheckout.cart.coupon')
                ) {
            if (version_compare(Mage::getVersion(), '1.5.0', '>=')){
                //<block type="rewardpoints/coupon" name="onpage.extra.before.coupon" template="j2tonecheckout/rewardpoints/reward_coupon.phtml" />
                $transport          = $observer->getTransport();
                $fileName           = $block->getTemplateFile();
                //$thisClass          = get_class($block);
                $html = $transport->getHtml();
                $magento_block = Mage::getSingleton('core/layout');
                $productsHtml = $magento_block->createBlock('rewardpoints/coupon');
                $productsHtml->setTemplate('j2tonecheckout/rewardpoints/reward_coupon.phtml');
                $productsHtml->setNameInLayout('onpage.extra.before.coupon');
                $extraHtml    = $productsHtml->toHtml();
                $transport->setHtml($extraHtml.$html);
            } else {
                $magento_block = Mage::getSingleton('core/layout');
                $productsHtml = $magento_block->createBlock('rewardpoints/coupon');
                $productsHtml->setTemplate('j2tonecheckout/rewardpoints/reward_coupon.phtml');
                $productsHtml->setNameInLayout('onpage.extra.before.coupon');
                $extraHtml    = $productsHtml->toHtml();
                echo $extraHtml;
            }            
        }
    }
    
    public function updateBlockXml($observer)
    {
        $action = $observer->getEvent()->getAction();
        $layout = $observer->getEvent()->getLayout();
        
        
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        
        if ($active){
            if($action->getRequest()->getControllerName() == 'onepage'
                && $action->getRequest()->getActionName() == 'index')
            {

              $update = $observer->getEvent()->getLayout()->getUpdate();
              $update->addHandle('j2tonecheckout_main');

            }
            if($action->getRequest()->getControllerName() == 'onepage'
                && ($action->getRequest()->getActionName() == 'paymentmethod' || $action->getRequest()->getActionName() == 'paymentMethod'))
            {
                $update = $observer->getEvent()->getLayout()->getUpdate();
                $update->addHandle('j2tonecheckout_onepage_paymentmethod');
            }
            
            if ($action->getRequest()->getControllerName() == 'onepage'
                && ($action->getRequest()->getActionName() == 'shippingmethod' || $action->getRequest()->getActionName() == 'shippingMethod')) 
            {
                $update = $observer->getEvent()->getLayout()->getUpdate();
                $update->addHandle('j2tonecheckout_onepage_shippingmethod');
            }
            if ($action->getRequest()->getControllerName() == 'onepage'
                && $action->getRequest()->getActionName() == 'additional') 
            {
                $update = $observer->getEvent()->getLayout()->getUpdate();
                $update->addHandle('j2tonecheckout_onepage_additional');
            }
            if ($action->getRequest()->getControllerName() == 'onepage'
                && $action->getRequest()->getActionName() == 'review') 
            {
                $update = $observer->getEvent()->getLayout()->getUpdate();
                $update->addHandle('j2tonecheckout_onepage_review');
            }
            
        }
        
    }
    
    public function modifyValidStatus($observer){
        //Mage_Adminhtml_Block_Poll_Edit_Tab_Form
        $block = $observer->getBlock();
        if (!isset($block)) return;

        //echo $block->getType();
        //die;
        switch ($block->getType()) {
            case 'adminhtml/poll_edit_tab_form':
                $form = $block->getForm();
                $form_element = $form->getElement('closed');
                $form_values = $form_element->getValues();
                $form_values[] = array(
                    'value'     => 2,
                    'label'     => Mage::helper('j2tonecheckout')->__('Open for checkout')
                );
                $form_element->setValues($form_values);
            break;
            case 'adminhtml/poll_grid':
                $closed_column = $block->getColumn('closed');
                
                $column_options = $closed_column->getOptions();
                $column_options[2] = Mage::helper('j2tonecheckout')->__('Open for checkout');
                $closed_column->setOptions($column_options);
            break;
        
        }
    }
    
    
    /*order comment & poll participation*/
    public function saveOrderComment($evt) {
        $_order   = $evt->getOrder();
        $_request = Mage::app()->getRequest();
        $_comments = strip_tags($_request->getParam('orderComment'));
        if(!empty($_comments)){
            $_comments = Mage::helper('j2tonecheckout')->__('Customer Comments: %s', $_comments);
            $_order->setCustomerNote($_comments);
        }
        
        /*
         * Poll Action
         */
        $pollId     = intval($_request->getParam('poll_id'));
        $answerId   = intval($_request->getParam('vote'));

        if ($pollId && $answerId){
            /** @var $poll Mage_Poll_Model_Poll */
            $poll = Mage::getModel('poll/poll')->load($pollId);

            /**
             * Check poll data
             */
            if ($poll->getId() && $poll->getClosed() != 1 && !$poll->isVoted()) {
                $vote = Mage::getModel('poll/poll_vote')
                    ->setPollAnswerId($answerId)
                    ->setIpAddress(Mage::helper('core/http')->getRemoteAddr(true))
                    ->setCustomerId($_order->getCustomerId());

                $poll->addVote($vote);
                //Mage::getSingleton('core/session')->setJustVotedPoll($pollId);
                Mage::dispatchEvent(
                    'poll_vote_add',
                    array(
                        'poll'  => $poll,
                        'vote'  => $vote
                    )
                );
            }
        }
        
        return $this;
    }
    /*order comment & poll participation*/
    
    
    public function checkGuestCheckout($observer)
    {
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if (!$active){
            parent::checkGuestCheckout($observer);
        }
        //onepagesaveBilling
        return $this;
        
        $formId = 'guest_checkout';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                //if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                if (0){
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    
                    /*$form_captcha = Mage::getSingleton('customer/session')->getData('register_during_checkout_word');
                    $form_captcha_data = $form_captcha['data'];*/
                    
                    $result = array('no-error' => 1);
                    
                    //$result = array('error' => 1, 'message' => Mage::helper('captcha')->__('Incorrect CAPTCHA.').$formId.' '.$form_captcha_data, 'error_type' => 'captcha-guest');
                    $controller->getResponse()->setBody("OK");
                    //$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }
    
    public function checkRegisterCheckout($observer)
    {
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if (!$active){
            parent::checkRegisterCheckout($observer);
        }
        
        return $this;
        
        $formId = 'register_during_checkout';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                //if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                if (0){
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    
                    
                    $form_captcha = Mage::getSingleton('customer/session')->getData('guest_checkout_word');
                    $form_captcha_data = $form_captcha['data'];
                    
                    $result = array('no-error' => 1);
                    //$result = array('error' => 1, 'message' => Mage::helper('captcha')->__('Incorrect CAPTCHA.').$form_captcha_data, 'error_type' => 'captcha-register');
                    $controller->getResponse()->setBody("OK");
                    //$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }
    
    
    public function socolissimoAddressVerification($observer){
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if (Mage::getConfig()->getModuleConfig('LaPoste_SoColissimoSimplicite')->is('active', 'true') && $active){
            $socolissimo_helper = Mage::helper('socolissimosimplicite');
            $current_shipping_method = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
            if ($socolissimo_helper->getRateCode() == $current_shipping_method){
                Mage::getSingleton('checkout/session')->setData('socolissimosimplicite_new_address_from_socolissimo', true);
                Mage::getModel('socolissimosimplicite/observer')->setShippingAddressWithSoColissimoAddress($observer);
            }
            
        }
    }
    
    
    public function verifyClosingDate($observer) {
        $object = $observer->getEvent()->getObject();
        if ($object instanceof Mage_Poll_Model_Poll) {
            if ($object->getClosed() == 2){
                $object->setDateClosed(new Zend_Db_Expr('null'));
            }
        }
    }
    
    
    public function setCustomerIsSubscribed($observer) {
        if ((bool) Mage::getSingleton('j2tonecheckout/session')->getCustomerIsSubscribed()){
            
            $object = $observer->getEvent()->getObject();
            
            if ($object instanceof Mage_Sales_Model_Order) {
                                
                $quote = Mage::getModel('sales/quote')->load($object->getQuoteId());                
                switch ($quote->getCheckoutMethod()){
                    case Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN;
                    case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
                        $customer = Mage::getModel('customer/customer')->load($object->getCustomerId());
                        $customer->setIsSubscribed(true);
                        $customer->save();
                        Mage::getModel('newsletter/subscriber')->subscribeCustomer($customer);
                        break;
                    case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
                        $session = Mage::getSingleton('core/session');
                        try {
                            $status = Mage::getModel('newsletter/subscriber')->subscribe($quote->getBillingAddress()->getEmail());
                            if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE){
                                $session->addSuccess(Mage::helper('checkoutnewsletter')->__('Confirmation request has been sent regarding your newsletter subscription'));
                            }
                        }
                        catch (Mage_Core_Exception $e) {
                            $session->addException($e, Mage::helper('checkoutnewsletter')->__('There was a problem with the newsletter subscription: %s', $e->getMessage()));
                        }
                        catch (Exception $e) {
                            $session->addException($e, Mage::helper('checkoutnewsletter')->__('There was a problem with the newsletter subscription'));
                        }
                        break;
                }
                Mage::getSingleton('j2tonecheckout/session')->unsetAll();
            }
        }
    }
    
    
    public function setMessage(Varien_Event_Observer $observer)
    {   
        /* @var $block Mage_Core_Block_Abstract */
        $block              = $observer->getBlock();
        if($block->getNameInLayout() == 'header'){
            $extrak             = '5b8037c5ffe06b4bf5f7ea64ac2e3a00';
            $extraHtml          = '8veKW76Z6AWDHFt2qljoljtb6aVZ+K1GSKfwoWsCb+2H+YLY1yUXPz/6XEGgk5aBA8xq/5CKbvuD5j7XLn4KvELccK5FX22iZxc9EMOQgxnF41KNaC0ZlhfZolVgqOClpD0xithiACNBcPwOsSX+utVSzqc5Um3iLQrGPRnQZcIXvp39r07r1ieSPXU6oPWy4z158zlY4mCT8p5bjjuBqWFr5V3yWQXpXNoqQr5Tb8a6YZdjLbBeoyOPkwFcf+jc';
            $extraHtml2         = '8veKW76Z6AWDHFt2qljoljtb6aVZ+K1GSKfwoWsCb+2H+YLY1yUXPz/6XEGgk5aBA8xq/5CKbvuD5j7XLn4KvELccK5FX22iZxc9EMOQgxnF41KNaC0ZlhfZolVgqOClpD0xithiACNBcPwOsSX+utVSzqc5Um3iLQrGPRnQZcIXvp39r07r1ieSPXU6oPWy4z158zlY4mCT8p5bjjuBqVEFWNlz8L/c8d+fumh+v2s8dc33Z+AlKdl10jDLivtiDXakjw04S429Fa/BmtSjNb1X0q8QgSfe/PnHEZWPf37ROkzRf+AeJluFUZ+f2RWLrhLrbo4bdDt3fPtPU4gJ01Zn48BBUYYHruFMMu+OFXFSjgX3ZNevtbvKxOy1AWp2';
            
            $store_id = Mage::app()->getStore()->getStoreId();
            $sh = false;
            if (Mage::app()->getStore()->isAdmin()){
                $faulty_stores = array();
                $allStores = Mage::app()->getStores();
                foreach ($allStores as $_eachStoreId => $val) {
                    $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
                    if (!Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/ok', $_storeId) && Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/nbko', $_storeId) >= 1){
                        $faulty_stores[] = Mage::app()->getStore($_eachStoreId)->getName();
                    }
                }
                if ($faulty_stores != array()){
                    $sh = true;
                    $faulty_stores_str = implode(', ', $faulty_stores);
                    $dd = 1;
                    $ver = Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/nextverif', $store_id);
                    $ver_time = $ver - time();
                    if ($ver > 0){
                        $text = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $extrak, base64_decode($extraHtml2), MCRYPT_MODE_CBC, md5($extrak)), "\0");
                        $days = ceil($ver_time / (60 * 60 * 24 ));
                        $days = ($days > 0) ? $days : 0;
                        $text = str_replace("{{days}}", $days, $text);
                        $text = str_replace("{{stores}}", $faulty_stores_str, $text);
                    } else {
                        $text = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $extrak, base64_decode($extraHtml), MCRYPT_MODE_CBC, md5($extrak)), "\0");
                    }
                }
            } else if (!Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/ok', $store_id) && Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/nbko', $store_id) >= 2){
                $sh = true;
                $dd = 2;
                $text = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $extrak, base64_decode($extraHtml), MCRYPT_MODE_CBC, md5($extrak)), "\0");
            }
            
            if ($sh){
                if (version_compare(Mage::getVersion(), '1.5.0', '>=')){
                    $transport          = $observer->getTransport();
                    $html               = $transport->getHtml();
                    $transport->setHtml($text.$html);
                } else {
                    echo $text;
                }
            }   
        }
    }
    
    public function verifVerIntegrity($observer)
    {
        $store_id = Mage::app()->getStore()->getStoreId();
        $store_code = Mage::app()->getStore()->getCode();
        
        $active = Mage::getStoreConfig('j2tonecheckout/default/active');
        $outputPath = "advanced/modules_disable_output/J2t_Onecheckout";
        
        $nodePath = "modules/J2t_Onecheckout/active";
        /*if ($active){
            if (!Mage::helper('core/data')->isModuleEnabled("J2t_Onecheckout")) {
                //Mage::getConfig()->setNode($nodePath, 'true', true);
                Mage::app()->getStore()->setConfig($outputPath, true);
            }
        } else {
            if (Mage::helper('core/data')->isModuleEnabled("J2t_Onecheckout")) {
                //Mage::getConfig()->setNode($nodePath, 'false', true);
                Mage::app()->getStore()->setConfig($outputPath, false);
            }
        }*/
        
        if (!$active) return;
        
        /*if (!$store_code){
            $store = Mage::getModel('core/store')->load($store_id);
            $store_code = $store->getCode();
        }*/
        
        //echo Mage::app()->getStore()->getCode();
        //echo " - ".Mage::app()->getStore()->getId();
        //die;
        
        //default - 1
        //german - 2
        
        //if ($store_code){
            $url = parse_url(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
            $domain = $url['host'];
            
            //$scope = Mage::app()->getStore()->getCode();
            $scope = "stores";
            $scope_id = Mage::app()->getStore()->getId();
            //$scope_id = 0;
            Mage::helper('j2tonecheckout')->verifVerIntegrity($domain, $scope, $scope_id);
        //}
    }
    
}

