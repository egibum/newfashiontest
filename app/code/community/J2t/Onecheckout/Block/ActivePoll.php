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

class J2t_Onecheckout_Block_ActivePoll extends Mage_Core_Block_Template
{
    /**
     * Poll templates
     *
     * @var array
     */
    protected $_templates;

    /**
     * Current Poll Id
     *
     * @var int
     */
    protected $_pollId = null;

    /**
     * Already voted by current visitor Poll Ids array
     *
     * @var array|null
     */
    //protected $_votedIds = null;

    /**
     * Poll model
     *
     * @var Mage_Poll_Model_Poll
     */
    protected $_pollModel;

    public function __construct()
    {
        parent::__construct();
        $this->_pollModel = Mage::getModel('j2tonecheckout/poll');
    }

    /**
     * Set current Poll Id
     *
     * @param int $pollId
     * @return Mage_Poll_Block_ActivePoll
     */
    public function setPollId($pollId)
    {
        $this->_pollId = $pollId;
        return $this;
    }

    /**
     * Get current Poll Id
     *
     * @return int|null
     */
    public function getPollId()
    {
        return $this->_pollId;
    }


    /**
     * Get Poll Id to show
     *
     * @return int
     */
    public function getPollToShow()
    {
        if ($this->getPollId()) {
            return $this->getPollId();
        }
        // get last voted poll (from session only)
        $pollId = Mage::getSingleton('core/session')->getJustVotedPoll();
        if (empty($pollId)) {
            // get random not voted yet poll
            $votedIds = $this->getVotedPollsIds();
            
            $csutomer_id = null;
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $csutomer_id = $customerData->getId();
            }
            
            $pollModel = $this->_pollModel
                ->setExcludeFilter($votedIds)
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->setCustomerFilter($csutomer_id)
                ->getRandomJ2tId();
            
            $pollId = $pollModel->getId();
            
        }
        
        $this->setPollId($pollId);

        return $pollId;
    }

    /**
     * Get Poll related data
     *
     * @param int $pollId
     * @return array|bool
     */
    public function getPollData($pollId)
    {
        if (empty($pollId)) {
            return false;
        }
        $poll = $this->_pollModel->load($pollId);

        $pollAnswers = Mage::getModel('poll/poll_answer')
            ->getResourceCollection()
            ->addPollFilter($pollId)
            ->load()
            ->countPercent($poll);

        // correct rounded percents to be always equal 100
        $percentsSorted = array();
        $answersArr = array();
        foreach ($pollAnswers as $key => $answer) {
            $percentsSorted[$key] = $answer->getPercent();
            $answersArr[$key] = $answer;
        }
        asort($percentsSorted);
        $total = 0;
        foreach ($percentsSorted as $key => $value) {
            $total += $value;
        }
        // change the max value only
        if ($total > 0 && $total !== 100) {
            $answersArr[$key]->setPercent($value + 100 - $total);
        }

        return array(
            'poll' => $poll,
            'poll_id' => $pollId,
            'poll_answers' => $pollAnswers,
        );
    }


    /**
     * Add poll template
     *
     * @param string $template
     * @param string $type
     * @return Mage_Poll_Block_ActivePoll
     */
    public function setPollTemplate($template, $type)
    {
        $this->_templates[$type] = $template;
        return $this;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $coreSessionModel Mage_Core_Model_Session */
        $coreSessionModel = Mage::getSingleton('core/session');
        $justVotedPollId = $coreSessionModel->getJustVotedPoll();
        if ($justVotedPollId && !$this->_pollModel->isVoted($justVotedPollId)) {
            $this->_pollModel->setVoted($justVotedPollId);
        }

        $pollId = $this->getPollToShow();
        $data = $this->getPollData($pollId);
        $this->assign($data);

        $coreSessionModel->setJustVotedPoll(false);

        if ($this->_pollModel->isVoted($pollId) === true || $justVotedPollId) {
            $this->setTemplate($this->_templates['results']);
        } else {
            $this->setTemplate($this->_templates['poll']);
        }
        return parent::_toHtml();
    }


    /**
     * Get cache key informative items that must be preserved in cache placeholders
     * for block to be rerendered by placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $items = array(
            'templates' => serialize($this->_templates)
        );

        $items = parent::getCacheKeyInfo() + $items;

        return $items;
    }

}
