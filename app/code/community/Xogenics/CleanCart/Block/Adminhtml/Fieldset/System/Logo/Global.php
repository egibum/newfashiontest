<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Xogenics
 * @package     Xogenics_cleancart
 * @copyright   Copyright (c) 2011 Xogenics IT
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fieldset renderer for cleancart text and logo settings
 * @author      Miroslav Pavkovic
 */
class Xogenics_Cleancart_Block_Adminhtml_Fieldset_System_Logo_Global
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Associative array of elements
     *
     * @var array
     */
    protected $_elements = array();

    /**
     * Custom template
     *
     * @var string
     */
    protected $_template = 'cleancart/global.phtml';

    /**
     * Getter for element label
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function getElementLabel(Varien_Data_Form_Element_Abstract $element)
    {
        return $element->getLabel();
    }
    
     /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $fieldset
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $fieldset)
    {
        foreach ($fieldset->getSortedElements() as $element) {
            $htmlId = $element->getHtmlId();
            $this->_elements[$htmlId] = $element;
        }
        $originalData = $fieldset->getOriginalData();
        $this->addData(array(
            'fieldset_label' => $fieldset->getLegend(),
            'fieldset_help_url' => isset($originalData['help_url']) ? $originalData['help_url'] : '',
        ));
        return $this->toHtml();
    }
    

  
   
}
