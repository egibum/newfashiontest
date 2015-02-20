<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_Resource_Eav_Mysql4_Attribute_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ecommerceteam_sln/attribute');
    }

    public function _beforeLoad()
    {
        $this->getSelect()->group('option_id');
        return parent::_beforeLoad();
    }

}
