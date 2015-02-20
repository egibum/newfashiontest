<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_FeedSalidzini
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

class ET_FeedSalidzini_Model_Productvisibility
{
    protected $_options;
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $options = Mage::getModel("catalog/product_visibility")->getOptionArray();
            $res = array();
            foreach ($options as $index => $value) {
                if ($index == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) {
                    continue; // Exclude
                }
                $res[] = array(
                   'value' => $index,
                   'label' => $value
                );
            }
            $this->_options = $res;
        }

        return $this->_options;
    }
}
