<?php
    //ignore_user_abort(true);
    //set_time_limit(0);
    //ob_implicit_flush(true);
    class Terapeak_SalesAnalytics_IndexController extends Mage_Adminhtml_Controller_Action
    {
        
        public function indexAction()
        {
            $userCredentials = Mage::getSingleton('terapeak_salesanalytics/usercredentials');
            $userCredentials->setSrNo(1);
            $userCredentials->setUsername($this->getRequest()->getParam('input_username'));
            $userCredentials->setPassword($this->getRequest()->getParam('input_password'));
            
            if (!$userCredentials->getUsername() && !$userCredentials->getPassword())
            {
                //load usercredentials from the database if not present in the request body.
                $userCredentials->load('1');
            }
            
            $session = null;
            if ($userCredentials->getUsername() && $userCredentials->getPassword())
            {
                //make api call to get sessionid
                $session = Mage::helper('salesanalytics/transport_user')->getAdminSession($userCredentials);
                if (is_null($session) || empty($session))
                {
                    Mage::getSingleton('core/session')->addError('Login Failed! Please check your username or password.');
                }
            }
            
            //if a session id is returned, then let user login to sales analytics
            if ($session)
            {
                $userCredentials->save();
                //check for channel id;
                //if null then link channel and get channel id ,save in db and load data (product list)
                // else save channel id in db and proceed
                
                $storeName = Mage::app()->getStore()->getName();
                $storeId = Mage::app()->getStore()->getStoreId();
                $websiteName = Mage::app()->getWebsite()->getName();
                $channelId = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelId($storeId);
                $channelinfo = Mage::getModel('terapeak_salesanalytics/linkedchannel');
                $channelData = Mage::helper('salesanalytics/channel');
                if (!$channelData->isHistoryLoaded()) {
                    $channelData->initializeHistoricalCounts();
                    Mage::getSingleton('terapeak_salesanalytics/loadhistory')->setHistory('true');                    
                }
                if (is_null($channelId) && empty($channelId))
                {
                    $sellerName = $storeName . "-" . $storeId . "_" . Mage::getBaseUrl();
                    $channelInfoData = array("sellerName" => $sellerName, "siteName" => $websiteName);
                    Mage::helper('salesanalytics/transport_channel')->linkMagentoChannel($channelInfoData);
                    $channelId = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelId($storeId);
                    $channelinfo->setStoreId($storeId);
                    $channelinfo->setStoreName($storeName);
                    $channelinfo->setChannelId($channelId);
                    $channelinfo->save();
                    ignore_user_abort(true);
                    Mage::getSingleton('terapeak_salesanalytics/usersession')->setSession($session);
                    $this->loadLayout();
                    $this->renderLayout();
                }
                else
                {
                    $channelinfo->setStoreId($storeId);
                    $channelinfo->setStoreName($storeName);
                    $channelinfo->setChannelId($channelId);
                    $channelinfo->save();
                    Mage::getSingleton('terapeak_salesanalytics/usersession')->setSession($session);
                    $this->loadLayout();
                    $this->renderLayout();
                }
                
            }
            else
            {
                //redirect the user to the login page
                $this->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('salesanalytics/index/showlogin'));
            }
        }
        
        public function showLoginAction()
        {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        public function loadHistoryAction() {
            echo Mage::helper('core')->jsonEncode(Mage::helper('salesanalytics/channel')->loadHistoryAction());
        }
        
        public function showCreateUserAction()
        {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        public function createUserLoginAction()
        {
            $userCredentials = Mage::getModel('terapeak_salesanalytics/usercredentials');
            $userCredentials->setSrNo(1);
            $userCredentials->setUsername($this->getRequest()->getParam('input_create_username'));
            $userCredentials->setPassword($this->getRequest()->getParam('input_create_password'));
            
            $result = Mage::helper('salesanalytics/transport_user')->createTpSaUser($userCredentials);
            
            if ($result)
            {
                $userCredentials->save();
                //redirect to index to loginto the application
                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('salesanalytics/index/index'));
            }
            else
            {
                //show error and ask user to enter info again
                Mage::getSingleton('core/session')->addError('User creation failed! Please try again.');
                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('salesanalytics/index/showcreateuser'));
            }
        }
        
        public function logoutAction()
        {
            Mage::getSingleton('terapeak_salesanalytics/usersession')->setSession("");
            Mage::getModel('terapeak_salesanalytics/usercredentials')->load(1)->delete();
            $allStores = Mage::getResourceModel('core/store_collection')->getData();
            
            foreach ($allStores as $key => $value)
            {
                $linkedChannelRow = Mage::getModel('terapeak_salesanalytics/linkedchannel')->load($value['store_id']);
                $linkedChannelRow->delete();
            }
            
            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('salesanalytics/index/showlogin'));
        }
    }
    
    ?>
