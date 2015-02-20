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

class ET_FeedSalidzini_Model_Productuniquetypes
{

    public function toOptionArray()
    {
        return array(
            0 => Mage::helper('feedsalidzini')->__('Get product from first category found'), 
            // Из первой встретившейся категории
            1 => Mage::helper('feedsalidzini')->__('Get products from root (ignore categories)'), 
            // Не учитывать категории
            2 => Mage::helper('feedsalidzini')->__('Get product from deepest category') 
            // Из самой глубокой категории
        );
    }

}