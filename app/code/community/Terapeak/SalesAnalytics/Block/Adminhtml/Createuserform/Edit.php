<?php

class Terapeak_SalesAnalytics_Block_Adminhtml_Createuserform_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

        public function __construct()
        {
                $createUserLabel = Mage::helper('salesanalytics')->__('Create User');
                $this->_blockGroup = "terapeak_salesanalytics";
                $this->_controller = "adminhtml_createuserform";
                $this->_headerText = $createUserLabel;
                parent::__construct();
                //$this->_removeButton('back');
                $this->_removeButton('save');
                $this->_removeButton('reset');
                $this->_addButton($createUserLabel, array(
                    'label' => $createUserLabel,
                    'onclick' => 'editForm.submit();',
                    'class' => 'save',
                        ), 1);
        }

}

?>
