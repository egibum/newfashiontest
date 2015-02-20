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
//include_once 'Mage/Poll/Model/Resource/Poll.php';
class J2t_Onecheckout_Model_Poll extends Mage_Poll_Model_Poll
{
    
    public function getRandomJ2tId()
    {
        //return $this->_getResource()->getRandomId($this);
        return $this->_getSelectIds();
    }
    
    protected function _getSelectIds()
    {
        $collection = $this->getCollection();
        $select = $collection->getSelect()
                ->where('closed = ?', 2);
        
        $excludeIds = $this->getExcludeFilter();
        if ($excludeIds) {
            $select->where('main_table.poll_id NOT IN(?)', $excludeIds);
        }

        $storeId = $this->getStoreFilter();
        if ($storeId) {
            $select->join(
                array('store' => $this->getResource()->getTable('poll/poll_store')),
                "main_table.poll_id=store.poll_id AND store.store_id = '$storeId'",
                array()
            );
        }
        
        $customerId = $this->getCustomerFilter();
        if ($customerId) {
            $select->where("main_table.poll_id NOT IN (SELECT DISTINCT poll_id FROM ".$this->getResource()->getTable('poll_vote')." WHERE customer_id = '$customerId')");
        }
        
        //$select->orderRand()->limit(1);
        $select->order(new Zend_Db_Expr('RAND()'))->limit(1);
        
        $row = $collection->getFirstItem();
        if (!$row) return $this;
        return $row;
    }
}