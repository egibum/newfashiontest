<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Block_Adminhtml_Meta extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {	
    $this->_controller = 'adminhtml_meta';
    $this->_blockGroup = 'ecommerceteam_sln';
    $this->_headerText = $this->__('Meta Tags');
    parent::__construct();
  }
}