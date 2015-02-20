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

class  J2t_Onecheckout_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function verifVerIntegrity($domain, $scope = "websites", $scope_id = "0", $force_verif = false, $force_store = null)
    {
        
        $ser_name_code = base64_decode("bW9kdWxlX3NlcmlhbA==");
        
        $version = Mage::getConfig()->getModuleConfig("J2t_Onecheckout")->version;
        $version_array = explode('.', $version);
        $module_branch_version = $version_array[0].'.'.$version_array[1];
        
        
        $module_name = 'j2tonecheckout';
        
        $_sermd5_array = str_split(md5($domain.$module_branch_version.$module_name), 4);
        $_servalue = strtoupper(implode('-', $_sermd5_array));
        
        //$module_branch_version = '0.2';
        $module_key = Mage::getStoreConfig('j2tonecheckout/'.$ser_name_code.'/key', $force_store);        
        $next_verification = (int)Mage::getStoreConfig('j2tonecheckout/'.$ser_name_code.'/nextverif', $force_store);
        $next_offline_verification = (int)Mage::getStoreConfig('j2tonecheckout/'.$ser_name_code.'/nextofflineverif', $force_store);
        
        //$store = Mage::getModel('core/store')->load($store_code);
        
        /*$url = parse_url($store->getBaseUrl());
        $domain = $url['host'];*/
        
        
        //offline verification
        
        //echo $next_offline_verification." < ".time();
        //die;
        
        if ($next_offline_verification < time()){
            if (strpos($module_key, $_servalue) === false){
                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', "0", $scope, $scope_id);
            } else {
                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', "1", $scope, $scope_id);
            }
            Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextofflineverif', mktime(0, 0, 0, date('m'), date('d')+7, date('Y')), $scope, $scope_id);
            Mage::app()->getStore()->resetConfig();
        }
        
        $first_verif = (int)Mage::getStoreConfig('j2tonecheckout/'.$ser_name_code.'/ok', $force_store);
        $nbko = (int)Mage::getStoreConfig('j2tonecheckout/'.$ser_name_code.'/nbko', $force_store);
        
        if ($next_verification < time() || $force_verif){
            if ($first_verif || $force_verif){
                $url = base64_decode("aHR0cDovL3d3dy5qMnQtZGVzaWduLm5ldC9qMnRtb2R1bGVpbnRlZ3JpdHkvaW5kZXgvY2hlY2tJbnRlZ3JpdHkvdmVyc2lvbi8=")."$module_branch_version/serial/$module_key/code/j2tonecheckout/domain/$domain";
                
                $curl_session = curl_init($url);
                curl_setopt ($curl_session, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_session, CURLOPT_HTTPGET,  1);
                curl_setopt ($curl_session, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt ($curl_session, CURLOPT_TIMEOUT, 20);
                $return_curl = curl_exec ($curl_session);
                curl_close($curl_session);
                
                if ($return_curl === "" && $return_curl !== "0" && $return_curl !== "1"){
                    $return_curl = 1;
                }
                
                if ($return_curl == 1){
                    Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nbko', "0", $scope, $scope_id);
                } else {
                    $cpt = $nbko + 1;
                    Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nbko', $cpt."", $scope, $scope_id);
                }

                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', $return_curl, $scope, $scope_id);
                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextverif', mktime(0, 0, 0, date('m'), date('d')+14, date('Y')), $scope, $scope_id);
                Mage::app()->getStore()->resetConfig();
            } else {
                $cpt = $nbko + 1;
                
                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nbko', $cpt."", $scope, $scope_id);
                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/ok', "0", $scope, $scope_id);
                Mage::getConfig()->saveConfig('j2tonecheckout/'.$ser_name_code.'/nextverif', mktime(0, 0, 0, date('m'), date('d')+14, date('Y')), $scope, $scope_id);
                Mage::app()->getStore()->resetConfig();
            }    
        }
        
        //https://starmate.freewww.biz/magento_tests_1_7_0_0/default/j2tmoduleintegrity/index/checkIntegrity/version/1.7/serial/KYdinYT/code/rewardpoints
        //check online (once every 10 days)
        //if url not accessible for 10s (curl timeout 10s), do not process
        //if ko, store verification status + date of verification
        //verify against version branch, module name, domain name
    }
}
