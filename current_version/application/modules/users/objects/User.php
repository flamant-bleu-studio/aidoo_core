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

class Users_Object_User 
{
	public $id;
	
	public $id_facebook;
	
	public $email;
	public $password;
	public $password_encrypt = true;
	public $civility;
	public $firstname;
	public $lastname;
	public $username;
	public $metas;
	public $isActive;
	public $isConfirm;
	
	public $date;
	public $date_update;
	
	/*
	 * Type format display name user in front
	 */
	public static $FORMAT_DISPLAY_PSEUDO 			 = 1;
	public static $FORMAT_DISPLAY_FIRSTNAME 		 = 2;
	public static $FORMAT_DISPLAY_FIRSTNAME_LASTNAME = 3;
	
	/*
	 * Lazy Loading
	 */
	private 	$group;
	
	protected static $_usersModelClass = "Users_Model_DbTable_Users";
	protected static $_groupsModelClass = "Users_Model_DbTable_Group";
	
	/**
	 * @var Users_Model_DbTable_Users
	 */
	protected static $_usersModel;
	/**
	 * @var Users_Model_DbTable_Group
	 */
	protected static $_groupsModel;
	
	public function __construct($id = null){
		if($id){
			
			self::_getUsersModel();

			if($user = self::$_usersModel->getUser($id)) {
				
				$this->id 			= $id;
				$this->id_facebook	= (int)$user->id_facebook;
				$this->group		= (int)$user->group;
				$this->email 		= $user->email;
				$this->civility 	= $user->civility;
				$this->firstname 	= $user->firstname;
				$this->lastname 	= $user->lastname;
				$this->username		= $user->username;
				$this->isActive		= $user->isActive;
				$this->isConfirm	= $user->isConfirm;
				$this->date 		= $user->date;
				$this->date_update	= $user->date_update;
				
				$this->metas		= (object)Users_Lib_Manager::getUserMetaInfo($id);
			}
			else 
				return null;
		}
	}
	
	public static function get(array $filters = null)
	{
		self::_getUsersModel();
		
		$ids = self::$_usersModel->get($filters);
	
		if( count($ids) > 0 )
		{
			$return = array();
			foreach ($ids as $id)
			{
				$return[] = new self($id);
			}
			return $return;
		}
		else 
			return null;
			
	}
	
	public static function count($filters)
	{
		self::_getUsersModel();
	
		return self::$_usersModel->count($filters);
		
	}
	
	public function save()
	{
		self::_getUsersModel();
		
		$this->firstname = str_replace('- ','-',ucwords(str_replace('-','- ',$this->firstname)));
		$this->lastname = mb_strtoupper($this->lastname);
		
		if($this->group instanceof stdClass || $this->group instanceof Users_Object_Group)
			$this->group = $this->group->id;
			
		if(isset($this->id))
			$this->_update();
		else
			$this->_insert();
		
		return $this->id;
	}

	protected function _insert()
	{
		$datas = $this->toArray();

		$this->id = static::$_usersModel->createEntity($datas);

		if ($this->metas){
			foreach($this->metas as $key => $value){
				Users_Lib_Manager::addUserMetaInfo($this->id, $key, $value);
			}
		}
		
		return $this->id;
	}
	
	protected function _update()
	{
		$datas = $this->toArray();
		
		static::$_usersModel->updateEntity($this->id, $datas);
		
		if ($this->metas){
			foreach($this->metas as $key => $value){
				if($value != "")
					Users_Lib_Manager::updateUserMetaInfo($this->id, $key, $value);
				else 
					Users_Lib_Manager::removeUserMetaInfo($this->id, $key);
			}
		}
		
		return $this->id;
	}

	public function active(){
		$this->isActive = 1;
	}
	public function desactive(){
		$this->isActive = 0;
	}
	/**
	 * @todo: passer la suppression en zend db table pour les cascades
	 */
	public function delete(){
		
		self::$_usersModel->deleteUser($this->id);
		Users_Lib_Manager::removeUserMetaInfo($this->id);
	}
	
	/**
	 * Préférer la récupération du groupe par l'attribut $user->group (lazy loading)
	 */
	public function getGroup(){
		
		if(is_int($this->group)){
		  	self::_getGroupsModel();
		  	
			$this->group = self::$_groupsModel->getGroupByGroupId($this->group);
		}
		
		return $this->group;
		
	}
	
	/**
	 * Préférer la modification du groupe par l'attribut $user->group (lazy loading)
	 */
	public function setGroup($value){
		if(is_int($value) || $value instanceof Users_Object_Group)
			$this->group = $value;
		else 
			throw new Zend_Exception("Group must be an integer or Users_Object_User instance");
	}

	/*
	* setter du token
	*/
	public function setToken()
	{
		$token = md5(uniqid());
		//$this->token = $token;
		$date = new DateTime();
		
		$expire_duration = defined('token_expire') ? token_expire : 0;
		
		$date->add(new DateInterval('PT' . $expire_duration . 'S'));
		//$this->token_expire = $date->format('Y-m-d h:i:s');
		//$this->save();
		
		// TOKEN
		if (Users_Lib_Manager::getUserMetaInfo($this->id, 'token'))
			Users_Lib_Manager::updateUserMetaInfo($this->id, 'token', $token);
		else 
			Users_Lib_Manager::addUserMetaInfo($this->id, 'token', $token);
		
		// TOKEN EXPIRE
		if (Users_Lib_Manager::getUserMetaInfo($this->id, 'token_expire'))
			Users_Lib_Manager::updateUserMetaInfo($this->id, 'token_expire', $date->format('Y-m-d h:i:s'));
		else
			Users_Lib_Manager::addUserMetaInfo($this->id, 'token_expire', $date->format('Y-m-d h:i:s'));
		
		return $token;
	}
	
	public function __set($property, $value) {
		
	   $method = 'set'.ucfirst($property); // setStatus
	   if(method_exists($this, $method))
	   {
	      return $this->$method($value);
	   }
	}
	
	public function __get($property) {
		
	   $method = 'get'.ucfirst($property); // getStatus
	   if(method_exists($this, $method))
	   {
	      return $this->$method();
	   }
	}
	
	public function getPublicName() {
		$config = CMS_Application_Config::getInstance();
		$config_datas = json_decode($config->get("mod_users-options"), true);
		
		if( $config_datas['formatDisplayName'] == Users_Object_User::$FORMAT_DISPLAY_PSEUDO )
			return $this->username;
		else if( $config_datas['formatDisplayName'] == Users_Object_User::$FORMAT_DISPLAY_FIRSTNAME )
			return $this->firstname;
		else if( $config_datas['formatDisplayName'] == Users_Object_User::$FORMAT_DISPLAY_FIRSTNAME_LASTNAME )
			return $this->firstname . " " . $this->lastname;
		
		return _t('Unknow');
	}
	
	protected static function _getGroupsModel()
	{
		if (empty(static::$_groupsModel) && class_exists(static::$_groupsModelClass))
		{
			static::$_groupsModel = new static::$_groupsModelClass();
			return;
		}

		if(!static::$_groupsModel)
			throw new Zend_Exception("Model is not instantiated");
	}
	
	protected static function _getUsersModel()
	{
		if (empty(static::$_usersModel) && class_exists(static::$_usersModelClass))
		{
			static::$_usersModel = new static::$_usersModelClass();
			return;
		}

		if(!static::$_usersModel)
			throw new Zend_Exception("Model is not instantiated");
	}
	
	public function toArray()
	{
		$properties = $this->_getProperties();

		foreach ($properties as $property)
			$array[$property] = $this->$property;
		
		$array["group"] = (int)$this->group;
		
		return $array;
	}
	protected function _getProperties()
	{
		$propertyArray = array();
		$class = new Zend_Reflection_Class($this);
		$properties = $class->getProperties();
		foreach ($properties as $property)
		{
			if ($property->isPublic())
				$propertyArray[] = $property->getName();
		}
		return $propertyArray;
	}
	
	public static function getNext($id, $where) {
		self::_getUsersModel();
		
		try {
			$user_id = (int)self::$_usersModel->getNext($id, $where);
			
			if ( $user_id )
				$user = new self($user_id);
		}
		catch (Exception $e) {}
		
		return $user ? $user : null;
	}
	
	public static function getPrevious($id, $where) {
		self::_getUsersModel();
		
		try {
			$user_id = (int)self::$_usersModel->getPrev($id, $where);
			
			if ( $user_id )
				$user = new self($user_id);
		}
		catch (Exception $e) {}
		
		return $user ? $user : null;
	}
	
	/*
	 * Fonction de check pour savoir si le token existe + si il n'est pas expiré
	 * @param identifiant du token [string]
	 * @return true | false 
	 */
	public static function checkToken($token = null)
	{
		if (!$token || $token == '')
			return false;
		
		$user_id =  Users_Lib_Manager::getUserIDFromMeta('token', $token);
		
		$date = new DateTime();
		
		if (!$user_id)
			return false;
		
		$user = reset(self::get(array('id' => $user_id)));
		
		if ($user->metas->token_expire < $date->format('Y-m-d h:i:s'))
			return false;
			
		return $user_id;
	}
	
	/**
	* Envoie d'un mail de changement de mot de passe d'un compte membre
	*/
	public function sendMailForgotPassword($codeVerif){
	
		$mail = new Zend_Mail('UTF-8');
	
		// Récupération de Smarty
		$view = Zend_Layout::getMvcInstance()->getView();
		// Récupération du chemin actuel des templates
		$path = $view->getScriptPaths();
		// Changement de chemin des templates pour selectionner celui des mails
		$view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render/emails');
	
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		$url = $helper->full('users', array('action'=>"forgot-password-confirm", 'page' => $codeVerif));
	
		$view->assign("url", $url);
		$view->assign("user", $this);
	
		// Génération du html
		$content = $view->renderInnerTpl("forgot-password.tpl");
	
		// Remise du chemin des templates d'origines
		$view->setScriptPath($path);
			
		$mail->setBodyHtml($content)
		->setFrom(EMAIL_FROM, EMAIL_SIGN)
		->addTo($this->email, $this->email)
		->setSubject('Modifier votre mot de passe de ' . EMAIL_SIGN)
		->send();
	}
	
	/**
	* Envoie d'un mail d'activation d'un compte membre
	*/
	public function sendMailActivation($codeVerif){
			
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
		$view->assign("user", $this);
	
		// Génération du html
		$content = $view->renderInnerTpl("emailvalid.tpl");
			
		// Remise du chemin des templates d'origines
		$view->setScriptPath($path);
			
		$mail->setBodyHtml($content)
		->setFrom(EMAIL_FROM, EMAIL_SIGN)
		->addTo($this->email, $this->email)
		->setSubject('Confirmez votre inscription à ' . EMAIL_SIGN)
		->send();
	}
}