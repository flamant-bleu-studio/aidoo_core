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

abstract class  CMS_Controller_Api extends Zend_Rest_Controller 
{
	protected $_context 				= array("json");	// Context utilisés 
	protected $_actionToken 		= array();				// Action protégé par un token (api externe) ou par une connexion (api interne)
	protected static $_rights 			= array();				// Droit pour les utilisateurs (api interne)
	
	protected $_token					=	null;					// Token envoyé dans le header
	protected $_user						=	null;					// Utilisateur instancié 
	
	const ERROR_CODE_NO_CONTENT		= 40;
	const ERROR_CODE_USER 					= 30;
	const ERROR_CODE_EMAIL_USED 		= 22;
	const ERROR_CODE_EMAIL_INVALID 	= 21;
	const ERROR_CODE_TOKEN				= 15;
	const ERROR_CODE_CONNEXION 		= 10;
	const ERROR_CODE_PARAM 				= 8;
	const ERROR_CODE_INTERNAL 			= 5;
	
		
	public function init()
	{	
		/* Gestionnaire d'exception REST (sans vue, json encodé) */
		$front = Zend_Controller_Front::getInstance();
		$front->throwExceptions(true);
		set_exception_handler(array($this, 'restException'));
		
		// Disable des vues, plus du layout (pour le zend rest qui utilise cette classe sans le switcher)
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		// JsonContext pour l'api interne
		$switcherContext = $this->_helper->contextSwitch();
		foreach ($this->_context as $context)
			$switcherContext->addActionContext($this->getRequest()->get('action'), $context)->setSuffix($context, '')->initContext();
		
		// Controle des droits sur l'action attaquée pour la partie AJAX interne
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$backAcl = CMS_Acl_Back::getInstance();
			if (!empty(static::$_rights[$this->getRequest()->get('action')]))
				foreach (static::$_rights[$this->getRequest()->get('action')] as $keyRight => $valueRight)
					if (!$backAcl->hasPermission($keyRight, $valueRight))
						throw new Exception(_t('Unauthorized token'), 401);
			
			$this->_user = Zend_Registry::get('user');
		} else {
			//Check du token
			$this->_token = $this->getRequest()->getHeader('token');
			if (in_array($this->getRequest()->get('action'), $this->_actionToken)){
				if (!Users_Object_User::checkToken($this->_token)) {
					$this->view->codeError = self::ERROR_CODE_TOKEN; // token invalid
					throw new Exception(_t('Unauthorized token'), 401);
				} else {
					$id_user = Users_Lib_Manager::getUserIDFromMeta('token', $this->_token);
					
					if ($id_user)
						$this->_user = reset(Users_Object_User::get(array('id' => $id_user)));
				}
			}
		}		
			
		$this->view->error = false;
	}
	
	
	public function postDispatch()
	{
		parent::postDispatch();
		
		/* Gestion des erreurs */
		if ($this->view->codeError) {
			$this->view->error = true;
			
			switch ($this->view->codeError){
				case self::ERROR_CODE_TOKEN 		: $this->getResponse()->setHttpResponseCode(401); break;
				case self::ERROR_CODE_INTERNAL	: $this->getResponse()->setHttpResponseCode(500); break;
			}
		}
		
		$this->getHelper('Json')->suppressExit = true;
		$this->_helper->json($this->view);
	}
	
	public function restException($exception = null)
	{
		if ($exception->getCode())
			$this->getResponse()->setHttpResponseCode($exception->getCode());
		
		$this->view->error = true;
		
		$this->view->message = $exception->getMessage();
		
		$this->_helper->json($this->view);
	}
	
	/**
	 * Return the Request object
	 *
	 * @return CMS_Controller_Request_Http
	 */
	public function getRequest()
	{
		return parent::getRequest();
	}
}