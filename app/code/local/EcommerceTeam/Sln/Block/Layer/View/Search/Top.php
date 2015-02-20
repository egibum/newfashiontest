<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */


class EcommerceTeam_Sln_Block_Layer_View_Search_Top extends EcommerceTeam_Sln_Block_Layer_View_Search
{
   public function _construct(){
        parent::_construct();
        $this->setNavigationGroup(self::NAVIGATION_GROUP_TOP);
    }
}
