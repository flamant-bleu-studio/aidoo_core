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

class Users_FrontController extends CMS_Controller_Action
{
	private $config;
	
	public function init()
	{
		parent::init();
		
		$config = CMS_Application_Config::getInstance();
		$this->config = json_decode($config->get('mod_users-options'), true);
	}
	
	/**
	 * Logout user
	 */
	public function logoutAction() {
		
		$user_id = Zend_Registry::get('user')->id;
		
		$user = reset(Users_Object_User::get(array("id" => $user_id)));
		
		// Facebook User
		if ($user->id_facebook) {
			$facebook = new CMS_Facebook_Facebook(array(
				'appId'  => FACEBOOK_APPID,
				'secret' => FACEBOOK_SECRET,
			));
			
			/*
			 * Fix bug Facebook PHP SDK doesn't logout user
			 */
			setcookie('fbs_'.$facebook->getAppId(), '', time()-100, '/', 'domain.com');
			session_destroy();
		}
		
		Zend_Auth::getInstance()->clearIdentity();
		
		return $this->_redirect($this->_helper->route->full('users', array('action' => 'login')));
	}
	
	/**
	 * Login User 
	 */
	public function loginAction() {
		
		$this->disableSmartyCache();
		
		if( !defined('CMS_MIDDLE_USER_GROUPE') || empty($this->config['pageMiddleOffice']) ) {
			throw new Exception(_t('Page not found'), 404);
		}
		
		$urlMiddleOffice = CMS_Page_PersistentObject::getPageFromID($this->config['pageMiddleOffice'])->getUrl();
		
		$auth = Zend_Auth::getInstance();
		
		if ($auth->hasIdentity()) {
			$user_id = Zend_Registry::get('user')->id;
			$user = reset(Users_Object_User::get(array("id" => $user_id)));
			
			if( !empty($user->metas->mustEditProfil) )
				$this->_redirect($this->_helper->route->full("users_middle", array('action' => 'edit-profil')));
			else 
				$this->_redirect($urlMiddleOffice);
		}
		
		$typeLogin = $this->config['typeLogin'];
		
		/**
		 * Login
		 */
		$loginForm = new Users_Form_Login(array('typeLogin' => $typeLogin));
		$loginForm->setAction($this->_helper->route->short("login"));
		
		if($this->getRequest()->isPost() && $_POST['type'] == 'login') {
			if($loginForm->isValid($_POST)) {
				
				if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_PASSWORD) {
					$validLogin = CMS_Acl_User::login($loginForm->getValue("email"), $loginForm->getValue("password"));
				}
				else if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY) {
					$validLogin = CMS_Acl_User::login($loginForm->getValue("email"), null, CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY);
				}
				
				if ($validLogin === true) {
					$this->_redirect($this->_helper->route->short("login"));
				}
				else {
					_error(_t('The email address or password is incorrect'));
					$this->_redirect($this->_helper->route->short("login"));
				}
			}
			else {
				$loginForm->populate($_POST);
			}
		}
		
		/**
		 * Inscription
		 */
		$formInscription = new Users_Form_Register(array('typeLogin' => $typeLogin));
		$formInscription->setAction($this->_helper->route->short("login"));
		
		if($this->getRequest()->isPost() && $_POST['type'] == 'register') {
			if ($formInscription->isValid($_POST)) {
				
				$users = Users_Object_User::get(array("email" => $formInscription->getValue("email")));
				
				if( $users ) {
					$formInscription->getElement("email")->addError(_t('This email address is already used'));
					$formInscription->populate($_POST);	
				}
				else {
					if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_PASSWORD) {
						
						$user = new Users_Object_User();
						$user->username 	= $formInscription->getValue("username");
						$user->email 		= $formInscription->getValue("email");
						$user->password 	= $formInscription->getValue("password");
						$user->civility 	= $formInscription->getValue("civility");
						$user->firstname 	= $formInscription->getValue("firstname");
						$user->lastname 	= $formInscription->getValue("lastname");
						$user->isActive 	= true;
						$user->isConfirm 	= false;
						$user->id_facebook 	= null;
						$user->setGroup(CMS_MIDDLE_USER_GROUPE);
						
						$codeVerif = Users_Lib_Manager::generateCodeVerif();
						$user->metas->codeVerif = $codeVerif;
						$user->metas->mustEditProfil = 0;
						
						$user_id = $user->save();
						
						$this->sendMailActivation($user, $codeVerif);
						
						$config = CMS_Application_Config::getInstance();
						$opts = json_decode($config->get('mod_users-options'), true);
						
						if($opts['mailAdminNewAccount'])
							$this->notifyAdmin($user, $opts['emailNotify']);
		
						_message(_t('Account created successfully')."<br />".
								 _t('An activation email has been sent to you, please follow instructions to confirm your account'));
					}
					else if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY) {
						
						$user = new Users_Object_User();
						$user->email 		= $formInscription->getValue("email");
						$user->password 	= md5(time());
						$user->firstname 	= $formInscription->getValue("firstname");
						$user->lastname 	= $formInscription->getValue("lastname");
						$user->isActive 	= true;
						$user->isConfirm 	= true;
						$user->setGroup(CMS_MIDDLE_USER_GROUPE);
						
						$user_id = $user->save();
						
						_message(_t('Account created successfully'));
					}
					
					return $this->_redirect($this->_helper->route->short("login"));
				}
			}
			else {
				$formInscription->populate($_POST);
			}
		}
		
		/*
		 * Facebook
		 */
		$facebook = new CMS_Facebook_Facebook(array(
			'appId'  => FACEBOOK_APPID,
			'secret' => FACEBOOK_SECRET,
		));
		
		$user_facebook = $facebook->getUser();
		
		if ($user_facebook) {
			try {
				$user_profile = $facebook->api('/me');
			}
			catch (CMS_Facebook_FacebookApiException $e) {
				error_log($e);
				$user_facebook = null;
			}
		}
		
		if ($user_facebook && $user_profile) {
			
			$user = reset(Users_Object_User::get(array("id_facebook" => $user_facebook, "email" => $user_profile["email"])));
			
			if( !$user ) {
				$user = new Users_Object_User();
				$user->email		= $user_profile["email"];
				$user->password		= "";
				$user->isActive 	= true;
				$user->isConfirm 	= true;
				$user->firstname 	= $user_profile["first_name"];
				$user->lastname 	= $user_profile["last_name"];
				$user->id_facebook 	= $user_facebook;
				$user->username		= $user_profile["first_name"];
				$user->setGroup(CMS_MIDDLE_USER_GROUPE);
				$user->metas->mustEditProfil = 1;
				$user_id = $user->save();
			}
			
			CMS_Acl_User::login($user->email, "", CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY);
			
			$user = new Users_Object_User($auth->getIdentity());
			
			return $this->_redirect($this->_helper->route->short("login"));
		}
		
		$this->view->loginForm		 	= $loginForm;
		$this->view->formInscription 	= $formInscription;
		$this->view->loginUrlFacebook 	= $facebook->getLoginUrl(array(
			"scope" => "email",
			"redirect_uri" => "http://".$_SERVER['SERVER_NAME'].BASE_URL.$this->_helper->route->full("users", array("action" => "login"))."/")
		);
		
		$this->view->typeLogin = $typeLogin;
	}
	
	/**
	 * Voir un profil d'un membre 
	 */
	public function viewAction() {
		$id = $this->getRequest()->getParam('page');
	
		$user = new Users_Object_User($id);
		$this->view->user = $user;
		
		$where = array(
				'status' 	=> Articles_Object_Article::STATUS_PUBLISH,
				array('isPermanent = ? AND date_start < ? OR date_start < ? AND date_end > ?', 1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s')),
				'author' => $id
		);
		
		$articles = Articles_Object_Article::get($where, array("date_start" => "DESC"));
		$this->view->articles = $articles;
		
		$config = CMS_Application_Config::getInstance();
		$options = json_decode($config->get("mod_categories-options"), true);
		
		$this->view->sizeImage = ((isset($this->_options['imageFormat'])) ? $this->_options['imageFormat'] : '');
		
		// Users précédent et suivant
		$opts = json_decode($config->get('mod_users-options'), true);
		$where = array(
			'group' 	=> $opts['groupFrontList']
		);
		
		$this->view->next_user = Users_Object_User::getNext($id, $where);
		$this->view->prev_user = Users_Object_User::getPrevious($id, $where);
	}
	
	/**
	 * Lister les profils des membres
	 */
	public function listAction() {
		
		$config = CMS_Application_Config::getInstance();
		$opts = json_decode($config->get('mod_users-options'), true);
		
		if(!is_array($opts['groupFrontList']) || empty($opts['groupFrontList']))
			throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
			
		$paginator = new CMS_Application_Paginator();
		
		$paginator->setRouteParams(array(
				'route' => 'users',
				'module' => 'users',
				'controller' => 'front',
				'action' => 'list'
		));
				
		$paginator->nbItems = Users_Object_User::count(array('group' => $opts['groupFrontList']));
		$paginator->byPage = 18;
		
		$this->view->pagination = $paginator->paginate();

		$where = array(
				'offset' 	=> $paginator->getFromLimit(),
				'limit' 	=> $paginator->byPage,
				'group' 	=> $opts['groupFrontList']
		);
		$users = Users_Object_User::get($where);
		
		$this->view->users = $users;
	}

	
	/**
	 * Valider un compte membre
	 */
	public function confirmEmailAction() {
		
		$code = $this->getRequest()->getParam('page');
	
		$id = Users_Lib_Manager::getUserIDFromCodeVerif($code)->user_id;
		
		if ($id) {
			$user = new Users_Object_User($id);
			$user->isConfirm = true;
			$user->metas->codeVerif = "";
			$user->save();

			_message(_t('Account validated successfully')."<br />".
					 _t('From now on, you can connect to your account'));
			
		}
		else {
			_error(_t('Validation code invalid : unable to validate account'));
		}

		return $this->_redirect($this->_helper->route->short('login'));
		
	}
	
	/**
	 * Envoie d'un mail d'activation d'un compte membre
	 */
	public function sendMailActivation($user, $codeVerif){
			
		$mail = new Zend_Mail('UTF-8');
		
		// Récupération de Smarty
		$view = Zend_Layout::getMvcInstance()->getView();
		// Récupération du chemin actuel des templates
		$path = $view->getScriptPaths();
		// Changement de chemin des templates pour selectionner celui des mails
		$view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render/emails');
		
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		$validationUrl = $helper->full('users', array('action'=>"confirm-email", 'page' => $codeVerif));
		
		$view->assign("validationUrl", $validationUrl);
		$view->assign("user", $user);
		
		// Génération du html
		$content = $view->renderInnerTpl("emailvalid.tpl");
					
		// Remise du chemin des templates d'origines
		$view->setScriptPath($path);
			
		$mail->setBodyHtml($content)
		->setFrom(EMAIL_FROM, EMAIL_SIGN)
		->addTo($user->email, $user->email)
		->setSubject('Confirmez votre inscription à ' . EMAIL_SIGN)
		->send();
	}
	
	/**
	 * Envoie d'un mail d'information à l'admin
	 */
	public function notifyAdmin($user, $email){
			
		$mail = new Zend_Mail('UTF-8');
	
		// Récupération de Smarty
		$view = Zend_Layout::getMvcInstance()->getView();
		// Récupération du chemin actuel des templates
		$path = $view->getScriptPaths();
		// Changement de chemin des templates pour selectionner celui des mails
		$view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render/emails');

		$view->assign("user", $user);
	
		// Génération du html
		$content = $view->renderInnerTpl("notifyNewAccount.tpl");
			
		// Remise du chemin des templates d'origines
		$view->setScriptPath($path);
			
		$mail->setBodyHtml($content)
		->setFrom(EMAIL_FROM, EMAIL_SIGN)
		->addTo($email, $email)
		->setSubject('Nouveau membre ' . EMAIL_SIGN)
		->send();
	}
	
	/**
	 * Mot de passe oublié
	 */
	public function forgotPasswordAction() {
		
		if( empty($this->config['pageMiddleOffice']) )
			throw new Exception(_t('Page not found'), 404);
		
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
			return $this->_redirect($this->config['pageMiddleOffice']);
		
		$form = new Users_Form_ForgotPassword();
		$form->setAction($this->_helper->route->short("forgot-password"));
		
		if( $this->getRequest()->isPost() ) {
			if( $form->isValid($_POST) ) {
				
				$email 	= $form->getValue('email');
				$user 	= reset(Users_Object_User::get(array('email' => $email)));
				
				if( $user && empty($user->id_facebook) ) {
					$codeVerifPassword = Users_Lib_Manager::generateCodeVerif();
					$user->metas->codeVerifPassword = $codeVerifPassword;
					$user->save();
					
					$user->sendMailForgotPassword($codeVerifPassword);
				}
				
				_message(_t('Mail sent to your email'));
				return $this->_redirect($this->_helper->route->short('login'));
			}
		}
		
		$this->view->form = $form;
	}
	
	/**
	 * Modifier le mot de passe
	 */
	public function forgotPasswordConfirmAction() {
		
		if( empty($this->config['pageMiddleOffice']) )
			throw new Exception(_t('Page not found'), 404);
		
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity())
			return $this->_redirect($this->config['pageMiddleOffice']);
		
		$code = $this->getRequest()->getParam('page');
		
		if( $code ) {
			$id = Users_Lib_Manager::getUserIDFromCodeVerifPassword($code)->user_id;
			
			if( $id ) {
				$user = new Users_Object_User($id);
				
				$form = new Users_Form_PasswordForm();
				$form->setAction($this->_helper->route->short("forgot-password-confirm", array('page' => $code)));
				$_POST['oldPassword'] = 'empty';
				
				if ( $this->getRequest()->isPost() ) {
					if ( $form->isValid($_POST) ) {
						$model = new Users_Model_DbTable_Users();
						$model->updatePassword($user->id, $form->getValue('password'));
						
						$user->metas->codeVerifPassword = "";
						$user->save();
						
						_message(_t('Password changed successfully'));
						
						return $this->_redirect($this->_helper->route->short('login'));
					}
					else {
						$form->populate($_POST);
					}
				}
				
				$this->view->form = $form;
			}
			else {
				_error(_t('Incorrect verification code'));
				return $this->_redirect($this->_helper->route->short('login'));
			}
		}
	}
	
// 	/**
// 	 * Envoie d'un mail de changement de mot de passe d'un compte membre
// 	 */
// 	public function sendMailForgotPassword($user, $codeVerif){
		
// 		$mail = new Zend_Mail('UTF-8');
		
// 		// Récupération de Smarty
// 		$view = Zend_Layout::getMvcInstance()->getView();
// 		// Récupération du chemin actuel des templates
// 		$path = $view->getScriptPaths();
// 		// Changement de chemin des templates pour selectionner celui des mails
// 		$view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render/emails');
		
// 		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
// 		$url = $helper->full('users', array('action'=>"forgot-password-confirm", 'page' => $codeVerif));
		
// 		$view->assign("url", $url);
// 		$view->assign("user", $user);
		
// 		// Génération du html
// 		$content = $view->renderInnerTpl("forgot-password.tpl");
		
// 		// Remise du chemin des templates d'origines
// 		$view->setScriptPath($path);
			
// 		$mail->setBodyHtml($content)
// 		->setFrom(EMAIL_FROM, EMAIL_SIGN)
// 		->addTo($user->email, $user->email)
// 		->setSubject('Modifier votre mot de passe de ' . EMAIL_SIGN)
// 		->send();
// 	}
}
