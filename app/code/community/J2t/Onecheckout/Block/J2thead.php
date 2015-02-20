<?php
/**
 * J2t_Onecheckout
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
 * @package    J2t_Onecheckout
 * @copyright  Copyright (c) 2013 J2T DESIGN. (http://www.j2t-design.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class J2t_Onecheckout_Block_J2thead extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $active = Mage::getStoreConfig('j2tonecheckout/default/active', Mage::app()->getStore()->getStoreId());
        if ($active){
            $min = Mage::getStoreConfig('j2tonecheckout/default/minified', Mage::app()->getStore()->getId());
            $responsive = Mage::getStoreConfig('j2tonecheckout/default/responsive', Mage::app()->getStore()->getId());

            $js = ($min) ? "min-" : "";

            $this->getLayout()->getBlock('head')->addItem('skin_js','js/'.$js."j2t_onecheckout.js");
            $this->getLayout()->getBlock('head')->addItem('skin_css','css/'.$js."j2t_onecheckout.css");   

            if ($responsive){
                $this->getLayout()->getBlock('head')->addItem('skin_css','css/'.$js."j2t_onecheckout-responsive.css");   
            }
        }
    }

}
