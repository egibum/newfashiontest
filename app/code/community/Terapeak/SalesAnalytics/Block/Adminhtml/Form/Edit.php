<?php

class Terapeak_Salesanalytics_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

        public function __construct()
        {
                $transLogin = Mage::helper('salesanalytics')->__('Login');
                $createUserLabel = Mage::helper('salesanalytics')->__('Create User');
                $createUserUrl = Mage::helper('adminhtml')->getUrl('salesanalytics/index/showcreateuser');
                $this->_blockGroup = 'terapeak_salesanalytics';
                $this->_controller = 'adminhtml_form';
                $this->_headerText = $transLogin;
                parent::__construct();
                $this->_removeButton('back');
                $this->_removeButton('reset');
                $this->_removeButton('save');
                $this->_addButton('createuser', array(
                    'label' => $createUserLabel,
                    'onclick' => 'setLocation(\'' . $createUserUrl . '\')',
                    'class' => 'reset',
                        ), 1);
                $this->_addButton('login', array(
                    'label' => $transLogin,
                    'onclick' => 'editForm.submit();',
                    'class' => 'save',
                        ), 2);
        }

}

?>
