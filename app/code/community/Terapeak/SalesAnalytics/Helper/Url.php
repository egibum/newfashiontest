<?php
    
    /**
     *
     * This file will be used for url cofiguration as per the environment type ie. dev/prod/test
     *
     */
    class Terapeak_SalesAnalytics_Helper_Url extends Mage_Core_Helper_Abstract
    {
        //this key should have any one of keys specified in the envs array
        const envKey = 'DEV';
        
        private static $envs = array(
                                     'DEV' => 'dev',
                                     'TEST' => 'test',
                                     'PROD' => 'prod'
                                     );
        
        //base urls to use. We replace the string TEMPENV in these to the equired environment when constructing the final url.
        private static $baseUrls = array(
                                         'NOTIFICATION' => 'https://mmi-n.terapeak.com/services',
                                         'SA_END_POINTS' => 'https://mmi.terapeak.com/services',
                                         'SA' => 'https://mmi.terapeak.com'
                                         );
        
        private static $resources = array(
                                          'SESSION' => 'tokens',
                                          'CUSTOM_NOTIFICATION' => 'notifications',
                                          'LISTING_NOTIFICATION' => 'notifications/listings',
                                          'CHANNEL_PERFORMANCE' => 'users/channels/performance',
                                          'CHANNEL_LINKING' => 'sellers/channels/links/magento',
                                          'STD_NOTIFICATION' => 'notifications',
                                          'USER_CREATE' => 'users',
                                          'CHANNEL_INFO' => 'users/analyze/info',
                                          'LOGIN' => 'index'
                                          );
        
        /**
         * This method will gives user session url as per environment type in which sales analytics application is deployed
         *
         * @return type url
         *
         */
        public function getSessionUrl()
        {
        	return $this->constructUrl('SA_END_POINTS', 'SESSION');
        }
        
        /**
         * This method will gives custom notification url as per environment type in which sales analytics application is deployed
         *
         * @return type url
         *
         */
        public function getNotificationUrl()
        {
        	return $this->constructUrl('SA_END_POINTS', 'STD_NOTIFICATION');
        }
        
        /**
         * This method will gives Listing Notification url as per environment type in which sales analytics application is deployed
         *
         * @return type url
         *
         */
        public function getListingNotificationUrl()
        {
        	return $this->constructUrl('SA_END_POINTS', 'LISTING_NOTIFICATION');
        }
        
        /**
         * This method will gives Magento Channel Linking Endpoint url as per environment type in which sales analytics application is deployed
         *
         * @return type url
         *
         */
        public function getMagentoChannelLinkingEndpointUrl()
        {
            return $this->constructUrl('SA_END_POINTS', 'CHANNEL_LINKING');
        }
        
        /**
         * This method will gives User Channel Performance url as per environment type in which sales analytics application is deployed
         *
         * @return type url
         *
         */
        public function getUserChannelPerformanceUrl()
        {
        	return $this->constructUrl('SA_END_POINTS', 'CHANNEL_PERFORMANCE');
        }
        
        /**
         * This method will gives User Channel Performance url as per environment type in which sales analytics application is deployed
         *
         * @return type url
         *
         */
        public function getUserChannelInfoUrl()
        {
        	return $this->constructUrl('SA_END_POINTS', 'CHANNEL_INFO');
        }
        
        /**
         * This method will gives User Channel Performance url as per environment type in which sales analytics application is deployed
         * 
         * @return type url
         * 
         */
        public function getTPSAUserUrl()
        {
       		return $this->constructUrl('SA_END_POINTS', 'USER_CREATE');		
        }
        
        /**
         * This method will gives salesanalytics app login url as per environment type in which sales analytics application is deployed
         * 
         * @return type url
         * 
         */
        public function getTPSALoginUrl()
        {
        	return $this->constructUrl('SA', 'LOGIN');
        }
        
        private function constructUrl($baseUrlOpt, $resourceOpt)
        {
            $url = str_replace("TEMPENV", $this->currentEnv(), self::$baseUrls[$baseUrlOpt]);
            return $url . "/" . self::$resources[$resourceOpt];
        }
        
        private function currentEnv()
        {
            return self::$envs[self::envKey];
        }
    }
    
    ?>
