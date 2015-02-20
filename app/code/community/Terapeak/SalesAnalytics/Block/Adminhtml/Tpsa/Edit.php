<?php

class Terapeak_SalesAnalytics_Block_Adminhtml_Tpsa_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

        public function __construct()
        {
                $logoutLabel = Mage::helper('salesanalytics')->__('Log Out from MySales');
                $this->_blockGroup = "terapeak_salesanalytics";
                $this->_controller = "adminhtml_tpsa";
                $this->_headerText = Mage::helper('salesanalytics')->__('Terapeak MySales');
                ;
                $logoutUrl = Mage::helper('adminhtml')->getUrl('salesanalytics/index/logout');
                parent::__construct();
                $this->_removeButton('back');
                $this->_removeButton('save');
                $this->_removeButton('reset');
                $this->_addButton($logoutLabel, array(
                    'label' => $logoutLabel,
                    'onclick' => 'setLocation(\'' . $logoutUrl . '\')',
                    'class' => 'save',
                        ), 1);
        }

}

?>
