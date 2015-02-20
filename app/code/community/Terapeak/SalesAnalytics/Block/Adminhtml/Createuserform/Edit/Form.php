<?php

class Terapeak_SalesAnalytics_Block_Adminhtml_Createuserform_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

        protected function _prepareForm()
        {
                $form = new Varien_Data_Form(
                                array('id' => 'edit_form',
                                    'action' => $this->getUrl('*/*/createuserlogin'),
                                    'method' => 'post',)
                );

                $form->setUseContainer(true);

                $this->setForm($form);

                $helper = Mage::helper('salesanalytics');

                $fieldSet = $form->addFieldset('create_user_form_fieldset', array(
                    'legend' => $helper->__('Create User'),
                    'class' => 'fieldset-wide',
                        ));

                $fieldSet->addField('input_create_username', 'text', array(
                    'name' => 'input_create_username',
                    'label' => $helper->__('Username'),
                    'class' => 'required-entry validate-email',
                    'required' => true,
                ));

                $fieldSet->addField('input_create_password', 'password', array(
                    'name' => 'input_create_password',
                    'label' => $helper->__('Password'),
                    'class' => 'required-entry validate-password',
                    'required' => true,
                ));

                $fieldSet->addField('input_create_confirm_password', 'password', array(
                    'name' => 'input_create_confirm_password',
                    'label' => $helper->__('Confirm Password'),
                    'class' => 'required-entry validate-cpassword',
                    'required' => true,
                ));
            
            $fieldSet->addField('checkbox', 'checkbox', array(
                    'name'      => 'terms_check',
                    'checked' => false,
                    'onclick' => "",
                    'onchange' => "",
                    'value'  => '1',
                    'required' => true,
                    'disabled' => false,
                    'after_element_html' => '&nbsp;I confirm I have read <A href="http://www.terapeak.com/policies/terms">Terapeak’s Software As A Service (“SAAS”) Subscription Agreement</A> and <A href="http://www.terapeak.com/policies/privacy">Privacy Policy</A> and accept them by ticking this box'
                ));

                return parent::_prepareForm();
        }

}

?>
