<?php
error_reporting(E_ALL);    
    class Terapeak_SalesAnalytics_Helper_Transport_User extends Terapeak_SalesAnalytics_Helper_Transport_Abstract
    {
        
        /**
         * This method will give admin session with the magento admin user cerdentilas
         *
         * @return string
         */
        public function getAdminSession($userCredentials)
        {
            if (!isset($userCredentials) && is_null($userCredentials))
            {
                $userCredentials = Mage::getSingleton('terapeak_salesanalytics/usercredentials');
                $userCredentials->load('1');
            }
            $userSessionUrl = Mage::helper('salesanalytics/url')->getSessionUrl();
            $response = $this->send($userSessionUrl, "POST", $userCredentials);
            $token = null;
            if (!is_null($response) && $response['success'] == 'true')
            {
            	$token = $response["token"];
            }
            return $token;
        }
        
        /**
         * This method will be used for creating terapeak salesanalytics user from magento extension
         *
         * @param type $data
         */
        public function createTpSaUser($data)
        {
            $result = false;
            $userCreationUrl = Mage::helper('salesanalytics/url')->getTPSAUserUrl();
            $response = $this->send($userCreationUrl, "POST", $data);
            
            if ($response['success'] == 'true')
            {
                $result = true;
            }
            return $result;
        }
    }
    
    ?>
