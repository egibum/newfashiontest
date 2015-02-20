<?php
    
    class Terapeak_SalesAnalytics_Model_Request_Buyer extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($customerData)
        {
            $this->setId($customerData['entity_id']);
            $this->setEmail($customerData['email']);
            $this->setName($customerData['firstname'] . " " . $customerData['lastname']);
            if (array_key_exists('dob', $customerData)) {
            	$this->setAge(Mage::helper('salesanalytics/util_datetime')->yearsSinceDate($customerData['dob']));
            }
            if (array_key_exists('gender', $customerData)) {
            	$this->setGender(Mage::helper('salesanalytics/util_gender')->genderFromCode($customerData['gender']));
            }
            
            return $this;
        }
        
    }
    
    ?>
