<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Adminhtml_Meta_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('meta_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle($this->__('Meta Details'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => $this->__('Meta Details'),
          'title'     => $this->__('Meta Details'),
          'content'   => $this->getLayout()->createBlock('ecommerceteam_sln/adminhtml_meta_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}