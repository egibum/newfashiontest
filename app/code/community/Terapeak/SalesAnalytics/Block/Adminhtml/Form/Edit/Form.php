<?php

class Terapeak_Salesanalytics_Block_Adminhtml_Form_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

        protected function _prepareForm()
        {
                $form = new Varien_Data_Form(
                                array('id' => 'edit_form',
                                    'action' => $this->getUrl('*/*/index'),
                                    'method' => 'post',)
                );

                $form->setUseContainer(true);

                $this->setForm($form);

                $helper = Mage::helper('salesanalytics');

                $fieldset = $form->addFieldset('display', array(
                    'legend' => $helper->__('Terapeak Login'),
                    'class' => 'fieldset-wide'
                        ));

                $fieldset->addField('input_username', 'text', array(
                    'name' => 'input_username',
                    'label' => $helper->__('Username'),
                    'required' => true,
                    'class' => 'required-entry validate-email'
                ));

                $fieldset->addField('input_password', 'password', array(
                    'name' => 'input_password',
                    'label' => $helper->__('Password'),
                    'required' => true,
                    'class' => 'required-entry'
                ));
            
                $fieldset->addField('label', 'label', array(
                    'value' => '* You can use your existing Terapeak credentials to access MySales'
                ));

                return parent::_prepareForm();
        }

}

?>
