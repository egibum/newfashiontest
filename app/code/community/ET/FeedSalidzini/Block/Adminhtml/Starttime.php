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

class ET_FeedSalidzini_Block_Adminhtml_Starttime extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<div id="additionalstarttime_template" style="display:none">';
        $html .= $this->_getRowTemplateHtml();
        $html .= '</div>';
        $html .= '<ul id="additionalstarttime_container">';
        if ($this->_getValue('hour')) {
            foreach ($this->_getValue('hour') as $i=>$f) {
                if ($i) {
                    $html .= $this->_getRowTemplateHtml($i);
                }
            }
        }
        $html .= '</ul>';

        $html .= $this->_getAddRowButtonHtml('additionalstarttime_container',
            'additionalstarttime_template', $this->__('Add additional start time'));

        return $html;
    }

    protected function _getRowTemplateHtml($i=0)
    {

        $html = '<li><fieldset>';
        $html .= '<label>'.$this->__('Additional start time:').' </label> <br />';

        // ====================================================
        // Varien_Data_Form_Element_Time

        $valueHrs = 0;
        $valueMin = 0;
        $valueSec = 0;
        if ($this->_getValue('hour/'.$i) ) {
                $valueHrs = $this->_getValue('hour/'.$i);
                $valueMin = $this->_getValue('min/'.$i);
                $valueSec = $this->_getValue('sec/'.$i);
        }

        $html .= '<select name="'. $this->getElement()->getName() 
            . '[hour][]" '.$this->serialize($this->getElement()->getHtmlAttributes())
            . ' style="width:40px">'."\n";
        for ( $i=0;$i<24;$i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '
                . ( ($valueHrs == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
        }
        $html.= '</select>'."\n";

        $html.= '&nbsp;:&nbsp;<select name="'. $this->getElement()->getName() 
            . '[min][]" '.$this->serialize($this->getElement()->getHtmlAttributes()).' style="width:40px">'."\n";
        for ( $i=0;$i<60;$i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($valueMin == $i) ? 'selected="selected"' : '' ) 
                .'>' . $hour . '</option>';
        }
        $html.= '</select>'."\n";

        $html.= '&nbsp;:&nbsp;<select name="'. $this->getElement()->getName() 
            . '[sec][]" '.$this->serialize($this->getElement()->getHtmlAttributes()).' style="width:40px">'."\n";
        for ( $i=0;$i<60;$i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($valueSec == $i) ? 'selected="selected"' : '' ) .'>' 
                . $hour . '</option>';
        }
        $html.= '</select>'."\n";
        // ====================================================

        $html .= '<p class="nm"><small>' . $this->__('On this time will start xml file generation process.') 
            . '</small></p>';

        $html .= '<br />';
        $html .= $this->_getRemoveRowButtonHtml();
        $html .= '</fieldset></li>';

        return $html;
    }


    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/'.$key);
    }

    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/'.$key)==$value ? 'selected="selected"' : '';
    }

    protected function _getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setClass('add '.$this->_getDisabled())
                ->setLabel($this->__($title))
                //$this->__('Add')
                ->setOnClick("Element.insert($('".$container."'), {bottom: $('".$template."').innerHTML})")
                ->setDisabled($this->_getDisabled())
                ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    protected function _getRemoveRowButtonHtml($selector='li', $title='Remove')
    {
        if (!$this->_removeRowButtonHtml) {
            $this->_removeRowButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setClass('delete v-middle '.$this->_getDisabled())
                ->setLabel($this->__($title))
                //$this->__('Remove')
                ->setOnClick("Element.remove($(this).up('".$selector."'))")
                ->setDisabled($this->_getDisabled())
                ->toHtml();
        }
        return $this->_removeRowButtonHtml;
    }
}