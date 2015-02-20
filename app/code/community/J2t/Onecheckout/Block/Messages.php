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
 * @copyright  Copyright (c) 2011 J2T DESIGN. (http://www.j2t-design.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class J2t_Onecheckout_Block_Messages extends Mage_Core_Block_Template {
    
    protected $_messages;
    protected $_messagesFirstLevelTagName = 'ul';
    protected $_messagesSecondLevelTagName = 'li';
    protected $_escapeMessageFlag = false;
    
    
    public function getMessageCollection()
    {
        $this->_messages = Mage::getSingleton('j2tonecheckout/session')->getJ2tMessages();
        /*if (!($this->_messages instanceof Mage_Core_Model_Message_Collection)) {
            $this->_messages = Mage::getModel('core/message_collection');
        }*/
        return $this->_messages;
    }
    
    
    public function getMessages($type=null)
    {
        if ($this->getMessageCollection()){
            return $this->getMessageCollection()->getItems($type);
        }
        else return false;
    }
    
    
    public function getHtml($type=null)
    {
        $html = '<' . $this->_messagesFirstLevelTagName . ' id="admin_messages">';
        foreach ($this->getMessages($type) as $message) {
            $html.= '<' . $this->_messagesSecondLevelTagName . ' class="'.$message->getType().'-msg">'
                . ($this->_escapeMessageFlag) ? $this->htmlEscape($message->getText()) : $message->getText()
                . '</' . $this->_messagesSecondLevelTagName . '>';
        }
        $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        return $html;
    }
    
    
    public function getGroupedHtml()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $html = '';
        foreach ($types as $type) {
            if ( $messages = $this->getMessages($type) ) {
                if ( !$html ) {
                    $html .= '<' . $this->_messagesFirstLevelTagName . ' class="messages">';
                }
                $html .= '<' . $this->_messagesSecondLevelTagName . ' class="' . $type . '-msg">';
                $html .= '<' . $this->_messagesFirstLevelTagName . '>';

                foreach ( $messages as $message ) {
                    $html.= '<' . $this->_messagesSecondLevelTagName . '>';
                    $html.= ($this->_escapeMessageFlag) ? $this->htmlEscape($message->getText()) : $message->getText();
                    $html.= '</' . $this->_messagesSecondLevelTagName . '>';
                }
                $html .= '</' . $this->_messagesFirstLevelTagName . '>';
                $html .= '</' . $this->_messagesSecondLevelTagName . '>';
            }
        }
        if ( $html) {
            $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        }
        return $html;
    }
    
    protected function _toHtml()
    {
        $temp = $this->getGroupedHtml();
        //Mage::getSingleton('j2tonecheckout/session')->unsetAll();
        //Mage::getSingleton('checkout/session')->getMessages(true);
        return $temp;
    }
}


