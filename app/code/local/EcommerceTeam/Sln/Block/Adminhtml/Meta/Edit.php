<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Adminhtml_Meta_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ecommerceteam_sln';
        $this->_controller = 'adminhtml_meta';
        
        $this->_updateButton('save', 'label', $this->__('Save Item'));
        $this->_updateButton('delete', 'label', $this->__('Delete Item'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('meta_data') && Mage::registry('meta_data')->getId() ) {
            return $this->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('meta_data')->getTitle()));
        } else {
            return $this->__('Add Item');
        }
    }
}