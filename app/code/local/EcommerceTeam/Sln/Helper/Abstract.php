<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

abstract class EcommerceTeam_Sln_Helper_Abstract
    extends Mage_Core_Helper_Abstract
{
    /**
     * @return Mage_Catalog_Model_Layer|Mage_CatalogSearch_Model_Layer|Mage_Core_Model_Abstract
     */
    public function getLayer()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($request->getModuleName() == 'catalogsearch'){
            /** @var $layer Mage_CatalogSearch_Model_Layer */
            $layer =  Mage::getSingleton('catalogsearch/layer');
        } else {
            /** @var $layer Mage_Catalog_Model_Layer */
            $layer =  Mage::getSingleton('catalog/layer');
        }
        return $layer;
    }
}