<?php

/**
* This class is the base class for all classes which we will write in order to make api calls for this extention.
* All Transport classes should have code solely to make an api call to a particular terapeak api. Every other thing should be handled else where. 
*/
abstract class Terapeak_SalesAnalytics_Helper_Transport_Abstract extends Mage_Core_Helper_Abstract
{

        /**
         * This method will send call to external end points using GET or POST method
         * 
         * @param type $apiType url that needs to be called
         * @param type $requestType GET or POST
         * @param type $data model data that needs to be send with url 
         * @return type json
         * @throws Exception
         */
        public function send($apiType, $requestType, $data)
        {
                $curlAdapter = new Varien_Http_Adapter_Curl();
                $curlAdapter->setConfig(array('timeout'   => 20));
                $body_param = Mage::helper('core')->jsonEncode($data);

                $header = $this->getHeader();
                try
                {
                        $curlAdapter->write($requestType, $apiType, '1.1', $header, $body_param);
                        $result = $curlAdapter->read();
                    
                        $response = preg_split('/^\r?$/m', $result, 2);
                        $response = trim($response[1]);
                        $res = NULL;
                        if (!is_null($response) && !empty($response))
                        {
                                $res = Mage::helper('core')->jsonDecode($response);
                        } else {
                            Mage::log('Encountered problems trying to contact Terapeak');
                            Mage::log($curlAdapter->getError());
                        }
                }
                catch (Exception $e)
                {
                        //eat the execption. We should not crash the magento instance if our extention throws exceptions
                        Mage::log('Exception when trying to send data to Terapeak:');
                        Mage::log($e);
                }
                return $res;
        }

        /**
         * This method set header content type as application/json
         * 
         * @return type
         * 
         */
        private function getHeader()
        {
                $content_type = "application/json";
                $http_header_str = "Content-Type: " . $content_type;
                return array($http_header_str);
        }

}

?>
