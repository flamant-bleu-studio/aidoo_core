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

class CMS_Acl_User {
	
	public $id;
	public $firstname;
	public $lastname;
	public $group;
	public $email;
	public $isActive;
	
	const TYPE_LOGIN_MAIL_PASSWORD 	= 1;
	const TYPE_LOGIN_MAIL_ONLY 		= 2;
	
	public function __construct($id = null){

		if($id){
			
			$userMdl 		= new Users_Model_DbTable_Users();
			$groupMdl 		= new Users_Model_DbTable_Group();

			$user 		= $userMdl->getUser($id);
			$group 		= $groupMdl->getGroupByGroupId($user->group);
			
			$this->id 			= $user->id;
			$this->firstname 	= $user->firstname;
			$this->lastname 	= $user->lastname;
			$this->email 		= $user->email;
			$this->isActive		= $user->isActive;
			$this->group = (object) array('id' => $group->id, 'name' => $group->name);
			
		}
		else {
			
			$groupMdl 	= new Users_Model_DbTable_Group();
			// Set public group to user
			$group 		= $groupMdl->getGroupByGroupId(2);

			$this->group =  (object) array('id' => $group->id, 'name' => $group->name);
		}
	}

	public static function login($email = null, $password = null, $type = self::TYPE_LOGIN_MAIL_PASSWORD, $pwdEncrypt = false){
		
		$auth = Zend_Auth::getInstance();
		
		if($type == self::TYPE_LOGIN_MAIL_PASSWORD) {
			
			if(!isset($email) || !isset($password))
				throw new Zend_Exception('La méthode CMS_Acl_User::login prend deux paramètres ...');
			
			$adapter = new Zend_Auth_Adapter_DbTable();
			
	        $adapter->setTableName('users')
	        			
	        		->setIdentityColumn('email')
	      			->setCredentialColumn('password')
	      			
	        		->setIdentity($email)
	        		->setCredential($pwdEncrypt ? $password : sha1($password))
	        		
	        		// User account must be active
	        		->getDbSelect()->where("isActive = 1")->where("isConfirm = 1");
	
	        $result = $auth->authenticate($adapter);
	        
	        if (!$result->isValid()) {
	        	return false;
	        }
	        else {
	        	$id = $adapter->getResultRowObject()->id;
        	
                //tinyMCE
		        $_SESSION['isLoggedIn']					= 'true';
		        $_SESSION['tinymce_ressource_folder']	= PUBLIC_PATH . UPLOAD_FOLDER;
				$_SESSION['tinymce_public_path']		= PUBLIC_PATH;
	        }
		}
		else if(self::TYPE_LOGIN_MAIL_ONLY){
			
			if(!isset($email))
				throw new Zend_Exception("Le mode d'authentification TYPE_LOGIN_MAIL_ONLY nécessite un email");
			
			$model = new CMS_Acl_DbTable_Users();
			$modelGroup = new Users_Model_DbTable_Group();
			
			$gid = $modelGroup->getGroupByGroupName("Membres")->id;
			
			if(!$gid)
				throw new Exception(_t("group 'Membres' doesn't exists."));
					
			$id = $model->authenticate($gid, $email);

			if(!$id = (int)$id->id) 
	        	return false;
		}

		$auth->clearIdentity();
        $auth->getStorage()->write($id);

        return true;
	}
	
	public function toArray()
	{
		$properties = $this->_getProperties();
		foreach ($properties as $property) {
			$array[$property] = $this->$property;
		}
		return $array;
	}
	
	protected function _getProperties()
	{
		$propertyArray = array();
		$class = new Zend_Reflection_Class($this);
		$properties = $class->getProperties();
		foreach ($properties as $property) {
			if ($property->isPublic()) {
				$propertyArray[] = $property->getName();
			}
		}
		return $propertyArray;
	}
	
}