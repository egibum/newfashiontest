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
 * @copyright  Copyright (c) 2011 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class J2t_Onecheckout_Model_Layout extends Mage_Core_Model_Layout 
{
    public function generateBlocks($parent=null) {

        if (empty($parent)) {
            $parent = $this->getNode();
        }
        
        if (isset($parent['ifconfig']) && ($configPath = (string)$parent['ifconfig'])) {
            /*echo $configPath . " " . Mage::getStoreConfigFlag($configPath);
            echo "<br />";*/
            if (!Mage::getStoreConfigFlag($configPath)) {
                return;
            }
        }
        if (isset($parent['unlessconfig']) && ($configPath = (string)$parent['unlessconfig'])) {
            if (Mage::getStoreConfigFlag($configPath)) {
                return;
            }
        }
        parent::generateBlocks($parent);
    }
}
