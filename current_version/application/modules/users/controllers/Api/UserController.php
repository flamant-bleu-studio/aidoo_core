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

class Users_Api_UserController extends CMS_Controller_Api{

	protected $_actionToken = array('getToken', 'putToken');
	protected static $_rights 	= array(	'index' 	=> array('mod_users' => 'view'),
														  	'get' 		=> array('mod_users' => 'view'),
															'put'		=> array('mod_users' => 'manage'));
	
	public function init()
	{
		parent::init();
		
		// Vérification de la possibilité de connexion sur le site, si celui ci possede un middle office
// 		if( !defined('CMS_MIDDLE_USER_GROUPE') || !defined('CMS_MIDDLE_INDEX') ) {
// 			throw new Exception(_t('No middle office'), 404);
// 		}
	}
	
	public function indexAction()
	{
				
		$this->getResponse()
		->appendBody("From indexAction() returning all user\n");
		
		$this->view->action = 'index';
	}
	
	public function getAction()
	{
		$id = $this->_request->getParam('id');
		
		if (!$id)
			return $this->getResponse()->setHttpResponseCode(500);
		
		$user = Users_Object_User::get(array('id' => $id));
		
		if ($user)
			$user = reset($user);
		
		$this->view->user = $user;
		$this->getResponse()->setHttpResponseCode(200); // Ok
	}
	
	public function postAction()
	{			
		if(!$params = CMS_Application_Tools::checkPOST(array('email', 'pwd', 'civility', 'firstname', 'lastname'))) 
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param

		// Check du format de l'email
		$valid_email = new Zend_Validate_EmailAddress();
		if (!$valid_email->isValid($params['email'])) 
			return $this->view->codeError = self::ERROR_CODE_EMAIL_INVALID; 
		
		// Si un autre utilisateur à déjà cet email
		if(Users_Object_User::get(array("email" => $params['email']))) 
			return $this->view->codeError = self::ERROR_CODE_EMAIL_USED; // email deja utilisé
		
		
		if (!defined('CMS_MIDDLE_USER_GROUPE'))
			throw new Exception(_t('You have to define user group'));
			
		$user = new Users_Object_User();
		$user->email 			= $params['email'];
		$user->password 	= $params['pwd'];
		$user->civility 			= $params['civility'];
		$user->firstname 	= $params['firstname'];
		$user->lastname 	= $params['lastname'];
		$user->isActive 		= 1;
		$user->isConfirm 	= 1;
		$user->password_encrypt = false;
		
// 		$codeVerif = Users_Lib_Manager::generateCodeVerif();
// 		$user->metas->codeVerif = $codeVerif;
		
		$user->setGroup(CMS_MIDDLE_USER_GROUPE);
		$user->save();

		$token = $user->setToken();

// 		$user->sendMailActivation($codeVerif);
		
		// Ajout des metas données fournis
		if (!empty($_POST['metas'])){
			foreach($_POST['metas'] as $metaName => $metaValue) {
				if ($metaValue)
					Users_Lib_Manager::addUserMetaInfo($user->id, $metaName, $metaValue);
			}
		}

		$this->view->token = $token;			
		$this->getResponse()->setHttpResponseCode(201); // Created
	}
	
	public function putAction()
	{	
		$id = $this->_request->getParam('id');
		
		if (!$id)
			return $this->getResponse()->setHttpResponseCode(500);

		$params = $this->_request->getParams();
		
		$user = Users_Object_User::get(array('id' => $id));
		
		if ($user){
			
			$user = reset($user);
		
			foreach ($params as $paramName => $paramValue) {
				// Si c'est dans les attributs de l'objet (et donc en base de donnée)
				if (array_key_exists($paramName, get_object_vars($user))) {
					if ($user->$paramName != $paramValue)
						$user->$paramName = $paramValue;
				// Sinon c'est dans les metas ? 
				} else if (array_key_exists($paramName, $user->metas)) {
					if ($user->metas->$paramName != $paramValue)
						$user->metas->$paramName = $paramValue; 
				}
			}
			
			$user->save();
			$this->getResponse()->setHttpResponseCode(201); // Created
		} else {
			$this->view->codeError = self::ERROR_CODE_USER;
			throw new Exception(_t('No content'), 401);
		}
	}
	
	public function deleteAction()
	{
		$id = $this->_request->getParam('id');
		
		if (!$id)
			throw new Exception(_t('missing id'), 500);
		
		$user = Users_Object_User::get(array('id' => $id));
		
		if ($user)
			$user = reset($user);
		else 
			throw new Exception(_t('No content'), 204);
		
 		$user->delete();
		
		$this->getResponse()->setHttpResponseCode(200); // Ok
	}
	
	public function connexionAction()
	{				
		if(!$params = CMS_Application_Tools::checkPOST(array('email', 'pwd')))
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
		
		// Check du format de l'email
		$valid_email = new Zend_Validate_EmailAddress();
		if (!$valid_email->isValid($params['email'])) 
			return $this->view->codeError = self::ERROR_CODE_EMAIL_INVALID; 
			
		if(CMS_Acl_User::login($params['email'], $params['pwd'], CMS_Acl_User::TYPE_LOGIN_MAIL_PASSWORD, true)){
			$user = reset(Users_Object_User::get(array('email' => $params['email'])));
	
			$token = $user->setToken();
			$this->view->token = $token;
		} else {
			return $this->view->codeError = self::ERROR_CODE_CONNEXION; // Mauvais mot de passe
		}
		
		$this->getResponse()->setHttpResponseCode(202); // Accepted
	}
	
	/*
	 * Fonction de récupération de mot de passe oublié via l'API Json
	 *  
	 * */
	public function forgotPasswordAction()
	{
		if(!$params = CMS_Application_Tools::checkPOST('email'))
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
			
		$valid_email = new Zend_Validate_EmailAddress();
		if (!$valid_email->isValid($params['email'])) 
			return $this->view->codeError = self::ERROR_CODE_EMAIL_INVALID; // Email invalid
			
		$user 	= Users_Object_User::get(array('email' => $params['email']));
	
		if( $user && empty($user->id_facebook) ) {
			$user = reset($user);
			$codeVerifPassword = Users_Lib_Manager::generateCodeVerif();
			$user->metas->codeVerifPassword = $codeVerifPassword;
			$user->save();
				
			$user->sendMailForgotPassword($codeVerifPassword);
		} else {
			return $this->view->codeError = self::ERROR_CODE_USER; // utilisateur inexistant
		}
	}
	
	public function getTokenAction()
	{
		$id_user = Users_Lib_Manager::getUserIDFromMeta('token', $this->_token);
		
		if (!$id_user)
			return $this->view->codeError = self::ERROR_CODE_TOKEN; 
				
		$this->_forward('get', null, null, array('id' => $id_user));
	}
	
	public function putTokenAction()
	{
		$id_user = Users_Lib_Manager::getUserIDFromMeta('token', $this->_token);
	
		 
		if (!$id_user)
			return $this->view->codeError = self::ERROR_CODE_TOKEN; 
	
		$params = $_POST;
		$params['id'] = $id_user;
		
		$this->_forward('put', null, null, $params);
	}
	
	public function headAction() {}
}
