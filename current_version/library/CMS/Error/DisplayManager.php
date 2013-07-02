<?php

/**
 * CMS Aïdoo
 *
 * Copyright (C) 2013  Flamant Bleu Studio
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

class CMS_Error_DisplayManager extends Zend_Controller_Action_Helper_FlashMessenger
{
	private static $_instance;
	private $_flashMessenger;
	
	protected $_namespace = 'default';
	
	/**
	 * @deprecated user TYPE_INFO or TYPE_SUCCESS
	 */
	const TYPE_MESSAGE 	= 'message';
	const TYPE_WARNING 	= 'warning';
	const TYPE_ERROR 	= 'error';
	const TYPE_INFO 	= 'info';
	const TYPE_SUCCESS 	= 'success';
	
	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Error_DisplayManager
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new CMS_Error_DisplayManager();
		}
		
		return self::$_instance;
	}
	
	public function __construct()
	{
		if (!self::$_session instanceof Zend_Session_Namespace) {
            self::$_session = new Zend_Session_Namespace($this->getName());
		}
	}
	
	/**
	 * Add a message to display at the next page or current controler if has no redirection
	 * 
	 * @param string $message message to display (it's a translate id !!)
	 * @param const $type type of message (CMS_Error_DisplayManager const)
	 * 
	 * @see library/Zend/Controller/Action/Helper/Zend_Controller_Action_Helper_FlashMessenger::addMessage()
	 */
	public function addMessage($message, $type)
	{
		if (self::$_messageAdded === false) {
			self::$_session->setExpirationHops(1, null, true);
		}
		
		if (!is_array(self::$_session->{$this->_namespace})) {
			self::$_session->{$this->_namespace}[$type] = array();
		}
		
		self::$_session->{$this->_namespace}[$type][] = $this->_factory($message, $type);
		
		return $this;
	}
	
	/**
	 * Add and translate message to FlashMessenger
	 *
	 * @param $message
	 * @param $type
	 * @param $params
	 */
	public function addMessageT($message, $type, $params)
	{
		if (self::$_messageAdded === false) {
			self::$_session->setExpirationHops(1, null, true);
		}
		
		if (!is_array(self::$_session->{$this->_namespace})) {
			self::$_session->{$this->_namespace}[$type] = array();
		}
		
		$t = Zend_Registry::get('translate');
		
		if (is_null($params)) {
			self::$_session->{$this->_namespace}[$type][] = $this->_factory($t->_($message), $type);
		}
		else if (!is_array($params)) {
			self::$_session->{$this->_namespace}[$type][] = $this->_factory(sprintf($t->_($message), $params), $type);
		}
		else if (is_array($params)) {
			self::$_session->{$this->_namespace}[$type][] = $this->_factory(vsprintf($t->_($message), $params), $type);
		}
		
		return $this;
	}
	
	protected function _factory($message, $type)
	{
		$msg = new stdClass();
		$msg->message = $message;
		$msg->type = $type;
		
		return $msg;
	}
	
    public function getCurrentMessages($type = null)
    {
        if ($type === null) {
            return parent::getCurrentMessages();
        }
		
        if ($this->hasCurrentMessages($type)) {
            return self::$_session->{$this->_namespace}[$type];
        }
		
        return array();
    }
    
    public function hasCurrentMessages($type = null)
    {
    	if ($type === null)
    		return parent::hasCurrentMessages();
    	
    	return isset(self::$_session->{$this->_namespace}[$type]);
    }
    
	public function clearCurrentMessages($type = null)
    {
    	if ($type === null)
    		return parent::clearCurrentMessages();
    	
        if ($this->hasCurrentMessages($type)) {
            unset(self::$_session->{$this->_namespace}[$type]);
            return true;
        }
        
        return false;
    }
}
