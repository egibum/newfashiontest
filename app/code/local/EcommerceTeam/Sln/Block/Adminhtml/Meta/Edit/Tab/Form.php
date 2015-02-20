<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Adminhtml_Meta_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $data = '';
        if (Mage::getSingleton('adminhtml/session')->getmetaData())
        {
            $data =  Mage::getSingleton('adminhtml/session')->getMetaData();
        }
        elseif ( Mage::registry('meta_data') ) {
            $data = (Mage::registry('meta_data')->getData());
        }

        $this->setForm($form);
        $fieldset = $form->addFieldset('meta_form', array('legend'=>$this->__('Meta Details')));

        $fieldset->addField('url', 'text', array(
            'label'     => $this->__('Page Url'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'url',
        ));

        $fieldset->addField('meta_title', 'text', array(
            'label'     => $this->__('Meta Title'),
            'name'      => 'meta_title',
        ));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'label'     => $this->__('Meta Keywords'),
            'name'      => 'meta_keywords',
        ));

        $fieldset->addField('meta_description', 'textarea', array(
            'label'     => $this->__('Meta Description'),
            'name'      => 'meta_description',
        ));

        $fieldset->addField('page_title', 'text', array(
            'label'     => $this->__('Page Title'),
            'name'      => 'page_title',
        ));

        $fieldset->addField('cms_block', 'select', array(
            'label'     => $this->__('Cms Block'),
            'name'      => 'cms_block',
            'values'    => Mage::getModel('catalog/category_attribute_source_page')->getAllOptions()
        ));
        /** @var $robots EcommerceTeam_Sln_Model_System_Config_Source_Robots */
        $robots = Mage::getModel('ecommerceteam_sln/system_config_source_robots');
        $fieldset->addField('robots', 'select', array(
            'label'     => $this->__('Robots'),
            'name'      => 'robots',
            'values'    => $robots->toOptionArray()
        ));

        if ( Mage::getSingleton('adminhtml/session')->getMetaData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMetaData());
            Mage::getSingleton('adminhtml/session')->setMetaData(null);
        } elseif ( Mage::registry('meta_data') ) {
            $form->setValues(Mage::registry('meta_data')->getData());
        }
        return parent::_prepareForm();
    }
}