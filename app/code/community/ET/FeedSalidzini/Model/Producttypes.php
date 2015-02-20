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

class ET_FeedSalidzini_Model_Producttypes
{
    protected $_options;
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = Mage::getModel("catalog/product_type")->getOptions();
        }
        /*foreach($this->options as $optionKey=>$option)
            if($option["value"]=="bundle")
                unset($this->options[$optionKey]);
        foreach($this->options as $optionKey=>$option)
            if($option["value"]=="grouped")
                unset($this->options[$optionKey]);
        */
        return $this->_options;
    }
}