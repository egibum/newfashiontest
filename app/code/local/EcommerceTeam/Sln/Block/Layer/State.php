<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */


class EcommerceTeam_Sln_Block_Layer_State extends Mage_Catalog_Block_Layer_State
{

    /**
     * Initialize Layer State template
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ecommerceteam/sln/layer/state.phtml');
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        return Mage::helper('ecommerceteam_sln/request')->getClearUrl();
    }

    public function getActiveFilters()
    {
        $filters    = array();
        $allFilters = parent::getActiveFilters();
        if (!empty($allFilters)) {
            /** @var $attributes Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection */
            $attributes            = $this->getFilterableAttributes();
            $categoryFilterEnabled = $this->getCategoryFilterEnabled();
            foreach ($allFilters as $item) {
                if ($attributeModel = $item->getFilter()->getData('attribute_model')) {
                    if (!empty($attributes) && $attributes->getItemByColumnValue('attribute_code', $attributeModel->getAttributeCode())) {
                        $filters[] = $item;
                    }
                } else {
                    if ('cat' == $item->getFilter()->getRequestVar() && $categoryFilterEnabled) {
                        $filters[] = $item;
                    }
                }
            }
        }
        return $filters;
    }
}
