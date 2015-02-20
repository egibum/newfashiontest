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
class J2t_Onecheckout_Model_Config_Serialverification extends Mage_Core_Model_Config_Data {
    //put your code here
    public function _afterSave()
    {
        $scope = $this->getScope();
        //echo " - " . $this->getWebsiteCode();
        $scope_id = $this->getScopeId();
        
        $force_store = null;
        
        switch ($this->getScope()) {
            case 'stores':
                //change scope for store code value (french, etc.)
                //$scope = $this->getStoreCode();
                $scope = "stores";
                $store_loaded = Mage::getModel('core/store')->load($this->getStoreCode());
                $force_store = $store_loaded->getId();
                break;
            case 'websites':
                //scope will be 'websites'
                break;
            case 'default':
                //scope will be 'default'
                break;
        }
        
        $key_validation = $this->getData('groups/module_serial/fields/key_validation/value');
        $current_key = $this->getValue();
        
        $exceptions = array();
        
        if ($key_validation){
            //TODO - verify key offline and online
            $exceptions[] = Mage::helper('j2tonecheckout')->__('Invalid Serial');
        }
        
        if (!empty($exceptions)) {
            
            $ser_name_code = base64_decode("bW9kdWxlX3NlcmlhbA==");
            $store_code = 'default';
            
            if ($current_store = Mage::app()->getRequest()->getParam('store')){
                $store_code = $current_store;
                $store = Mage::app()->getStore();
            } else {
                $websites = Mage::app()->getWebsites();
                //$store_code = $websites[1]->getDefaultStore()->getCode();
                $store_code = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStore()->getCode();
                $store = Mage::getModel('core/store')->load($store_code);
            }
            
            $url = parse_url($store->getBaseUrl());
            $domain = $url['host'];
            
            Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextverif', "0", $scope, $scope_id);
            Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextofflineverif', "0", $scope, $scope_id);
            Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', "0", $scope, $scope_id);
            
            //set next verif for all stores
            foreach (Mage::app()->getStores() as $store_list){
                if ($store_list->getCode() != $scope){
                    //$store_list->getCode()
                    Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextverif', "0", "stores", $store_list->getId());
                    Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextofflineverif', "0", "stores", $store_list->getId());
                    Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', "0", "stores", $store_list->getId());
                }
            }
            
            
            Mage::app()->getStore()->resetConfig();
            
            Mage::helper('j2tonecheckout')->verifVerIntegrity($domain, $scope, $scope_id, true, $force_store);
            
            //Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', 0, $store_code, $store_id);
            
            //$ok = Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/ok', $store_id);
            $ok = Mage::getStoreConfig('j2tonecheckout/'.base64_decode("bW9kdWxlX3NlcmlhbA==").'/ok', $force_store);
            if (!$ok){
               //throw new Exception( "\n" . implode("\n", $exceptions) );
                Mage::getSingleton('adminhtml/session')->addError(implode("\n", $exceptions));
            } else {
                //todo add success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('j2tonecheckout')->__('Valid Serial.'));
            }
        }
        
    }
}

