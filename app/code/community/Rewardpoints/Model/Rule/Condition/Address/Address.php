<?php
/**
 * J2T Autoadd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    J2t_Autoadd
 * @copyright  Copyright (c) 2010 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Rewardpoints_Model_Rule_Condition_Address_Address extends Mage_SalesRule_Model_Rule_Condition_Address
{
    //salesrule/rule_condition_address
    public function loadAttributeOptions()
    {
        /*$attributes = array(
            'base_subtotal_inc_tax' => Mage::helper('autoadd')->__('Subtotal (Incl. Tax)'),
            'base_subtotal' => Mage::helper('autoadd')->__('Subtotal'),
            'total_qty' => Mage::helper('autoadd')->__('Total Items Quantity'),
            'weight' => Mage::helper('autoadd')->__('Total Weight'),
            'payment_method' => Mage::helper('autoadd')->__('Payment Method'),
            'shipping_method' => Mage::helper('autoadd')->__('Shipping Method'),
            'postcode' => Mage::helper('autoadd')->__('Shipping Postcode'),
            'region' => Mage::helper('autoadd')->__('Shipping Region'),
            'region_id' => Mage::helper('autoadd')->__('Shipping State/Province'),
            'country_id' => Mage::helper('autoadd')->__('Shipping Country'),
        );*/
        
        $temp = parent::loadAttributeOptions();
        
        $attributes = $temp->getAttributeOption();
        $attributes = array('base_subtotal_inc_tax' => Mage::helper('rewardpoints')->__('Subtotal (Incl. Tax)')) + $attributes; 

        $this->setAttributeOption($attributes);

        return $this;
    }

    
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal_inc_tax':
                return 'numeric';
        }
        return parent::getInputType();
    }
    
    public function validate(Varien_Object $object)
    {
        if ($object instanceof Rewardpoints_Model_Quote && !$object->getQuote()) {
            $object->setQuote($object);
        } 
        $address = $object;
        
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            }
            else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }
        $address->setBaseSubtotalIncTax($address->getBaseSubtotal() + $address->getBaseTaxAmount());
        
        return parent::validate($object);
    }
}
