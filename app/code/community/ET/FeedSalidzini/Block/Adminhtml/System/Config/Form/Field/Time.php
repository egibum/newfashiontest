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

//if (!class_exists('Mage_Adminhtml_Block_System_Config_Form_Field_Heading')) {
// https://bugs.php.net/bug.php?id=52339
if (version_compare(Mage::getVersion(), '1.4.1', '<')) {
    class ET_FeedSalidzini_Block_Adminhtml_System_Config_Form_Field_Time
        extends Mage_Adminhtml_Block_Abstract
        implements Varien_Data_Form_Element_Renderer_Interface
    {
        public function render(Varien_Data_Form_Element_Abstract $element)
        {
            return sprintf(
                '<tr class="system-fieldset-sub-head" id="row_%s"><td><span id="%s">%s</span></td>
                <td colspan="4"><strong>'.date("H:i:s", Mage::getModel('core/date')->timestamp(time()))
                .'</strong></td></tr>',
                $element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
            );
        }
    }
} else {
    class ET_FeedSalidzini_Block_Adminhtml_System_Config_Form_Field_Time
        extends Mage_Adminhtml_Block_System_Config_Form_Field_Heading
    {
        public function render(Varien_Data_Form_Element_Abstract $element)
        {
            return sprintf(
                '<tr class="system-fieldset-sub-head" id="row_%s"><td><span id="%s">%s</span></td>
                <td colspan="4"><strong>'.date("H:i:s", Mage::getModel('core/date')->timestamp(time()))
                .'</strong></td></tr>',
                $element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
            );
        }
    }
}