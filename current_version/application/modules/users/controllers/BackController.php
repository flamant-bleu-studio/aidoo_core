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

class Users_BackController extends CMS_Controller_Action
{	
	public function usersAction()
	{
		$this->redirectIfNoRights('mod_users', 'view');
		
		// Current user
		$user = Zend_Registry::get('user');
		$this->view->userId = $user->id;
		$this->view->groupId = $user->group->id;
		
		// Get all users
		$users = Users_Object_User::get();
		
		// inferior users cannot see/edit superior users
		$backAcl = CMS_Acl_Back::getInstance();
		
		$return = array();
		foreach($users as $user)
		{
			if($backAcl->hasPowerOn($user->group->id))
				array_push($return, $user);
		}
		
		$this->view->users = $return;
	}
	
	public function groupsAction()
	{
		$this->redirectIfNoRights('mod_users', 'view');
		
		// Current user
		$user = Zend_Registry::get('user');
		$this->view->userId = $user->id;
		$this->view->groupId = $user->group->id;
		
		// Get all groups
		$groups = Users_Lib_Manager::getAllGroups();
		
		// Identation
		foreach($groups as $group)
		{
			$n = '';
			for($i=0; $i < $group->level; $i++)
				$n .= '<span style="color:grey;">&brvbar;&#8211;</span>';
				
			$group->level = $n;
		}
		$this->view->groups = $groups;
	}
	
	public function accessAction()
	{
		$this->redirectIfNoRights('mod_users', 'view');
		
		$viewAccessModel = new CMS_Acl_DbTable_ViewAccess();
		$viewAccess = $viewAccessModel->getAllViewAccess();
		$this->view->viewAccess = $viewAccess;
	}
	
	public function createUserAction()
	{
		$userForm = new Users_Form_UserForm();

		if($this->getRequest()->isPost()) {
			if($userForm->isValid($_POST)) {

				$user = new Users_Object_User();
				
				$user->civility 	= $userForm->getValue("civility");
				$user->firstname 	= $userForm->getValue("firstname");
				$user->lastname 	= $userForm->getValue("lastname");
				$user->email 		= $userForm->getValue("email");
				
				$user->group 		= (int)$userForm->getValue("group");
				$user->isActive 	= $userForm->getValue("isActive");
				$user->isConfirm 	= 1;
				
				$user->save();
				
    			_message(_t('User created'));
    			
				return $this->_redirect($this->_helper->route->short('index'));
			}
			else {
				_error(_t('invalid form'));
			}
		  
		}
		$userForm->setAction( $this->_helper->route->short('create-user') );
		$this->view->form = $userForm;
	}
	
	public function createGroupAction()
	{
		$groupForm = new Users_Form_GroupForm();
		
		if($this->getRequest()->isPost()) {
			if($groupForm->isValid($_POST)) {
				$datas = array();
				$datas = $groupForm->getValues();
				$datas["parent"] = 2;
				
				Users_Lib_Manager::addGroup($datas);
				
    			_message(_t('Group created'));
    			
				return $this->_redirect($this->_helper->route->short('index'));
			}
			else {
				_error(_t('invalid form'));
			}
		  
		}
		
		$groupForm->setAction( $this->_helper->route->short('create-group') );
		$groupForm->getElement("submit")->setLabel(_t("Create group"));
		
		$this->view->form = $groupForm;

	}
	
	public function createViewaccessAction()
	{
		$this->redirectIfNoRights('mod_users', 'manage');
		
		$viewAccessForm = new Users_Form_ViewAccessForm();
		
		if($this->getRequest()->isPost()) {
			if($viewAccessForm->isValid($_POST)) {
				
				$datas = $viewAccessForm->getValues();
				
				$groupModel = new Users_Model_DbTable_Group();
				$groups = $groupModel->getAllGroups();
				
				$viewAccessGroups = array();
				foreach ($groups as $group)
				{
					if($datas[$group->id] == '1')
						$viewAccessGroups[] = (int)$group->id;
				}
		
				$viewAccessModel = new CMS_Acl_DbTable_ViewAccess();
				$viewAccessModel->addViewAccess($datas['name'], $viewAccessGroups);
				
    			_message(_t('View Access created'));
    			
				return $this->_redirect($this->_helper->route->short('index'));
			}
			else {
				_error(_t('invalid form'));
			}
		  
		}
		$viewAccessForm->setAction( $this->_helper->route->short('create-viewaccess') );
		$viewAccessForm->addElement('submit', 'submit', array('label' => _t("Create view-access")));
		
		$this->view->form = $viewAccessForm;
	}
	
	public function editUserAction()
	{
		$id = (int)$this->_request->getParam('id');
			
		$user = new Users_Object_User($id);
		$userCurrent = Zend_Registry::get('user');
		
		$selfEdit = $userCurrent->id == $id;
		
		// Soit on édite son propre compte, soit on a le droit d'accès
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("mod_users", "manage") || $selfEdit )
		{
			$userForm 			= new Users_Form_UserForm();
			$userPasswordForm 	= new Users_Form_PasswordForm();
			
			// Si édition d'un membre, on ne demande pas l'ancien password
			// Si édition de son propre compte, on ne peut pas changer son groupe
			if( $selfEdit ){
				$userForm->removeElement("group");
				$userForm->removeElement("isActive");
			}
			else
				$userPasswordForm->removeElement('oldPassword');
			
			if($this->getRequest()->isPost())
			{	
				if (isset($_POST["submit"]))
				{
					if($userForm->isValid($_POST))
					{
						
						$user->civility 	= $userForm->getValue("civility");
						$user->firstname 	= $userForm->getValue("firstname");
						$user->lastname 	= $userForm->getValue("lastname");
						$user->email 		= $userForm->getValue("email");
												
						if( !$selfEdit ){
							$user->group 		= (int)$userForm->getValue("group");
							$user->isActive 	= $userForm->getValue("isActive");
							$user->isConfirm 	= 1;
						}
						$user->save();
						
						_message(_t("user saved"));
	    				return $this->_redirect( $this->_helper->route->short('index'));
					}
					else
						_error(_t("Invalid form"));
				}
				elseif (isset($_POST["submitPassword"]))
				{
					if($userPasswordForm->isValid($_POST))
					{
						$datas = $userPasswordForm->getValues();
						$datas["id"] = $id;
						if($selfEdit)
						{
							if( Users_Lib_Manager::getPassword($id) == sha1($_POST["oldPassword"]))
								Users_Lib_Manager::updatePassword($datas);
							else
							{
								_error(_t("Invalid old password"));
								return $this->_redirect($this->_helper->route->short('edit-user', array('id'=>$id)));
							}
						}
						else
							Users_Lib_Manager::updatePassword($datas);
						_message(_t("user saved"));
	    				return $this->_redirect($this->_helper->route->short('edit-user', array('id'=>$id)));
					}
					else
						_error(_t("Invalid form"));
				}
			}
			
			$userForm->getElement("submit")->setLabel(_t("Update"));
			$userForm->setAction( $this->_helper->route->short('edit-user', array('id' => $id)));
			$userForm->populate($user->toArray());	
			
			$userPasswordForm->setAction( $this->_helper->route->short('edit-user', array('id' => $id)));
			
			$this->view->form = $userForm;
			$this->view->formPassword = $userPasswordForm;
		}
		else 
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	public function editViewaccessAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		$viewAccessForm = new Users_Form_ViewAccessForm();
		$viewAccessForm->setAction( $this->_helper->route->short('edit-viewaccess', array('id'=>$id)) );
		
		$viewAccessModel = new CMS_Acl_DbTable_ViewAccess();
		
		if($this->getRequest()->isPost()) 
		{
			if($viewAccessForm->isValid($_POST)) {
				 
				$datas = $viewAccessForm->getValues();
				
				$groupModel = new Users_Model_DbTable_Group();
				$groups = $groupModel->getAllGroups();
				
				$viewAccessGroups = array();
				foreach ($groups as $group)
				{
					if($datas[$group->id] == '1')
						$viewAccessGroups[] = (int)$group->id;
				}

				$viewAccessModel = new CMS_Acl_DbTable_ViewAccess();
				$viewAccessModel->updateViewAccess($id, $datas['name'], $viewAccessGroups);
				
    			_message(_t("View Access saved"));
    			
    			return $this->_redirect($this->_helper->route->short('index'));
			}
			else {
				_error(_t('invalid form'));
			}
		}
		else
		{
			$viewAccess = $viewAccessModel->getViewAccess($id);

			$viewAccessForm->presetCheckbox($viewAccess->groups);
			unset($viewAccess->groups);
			
			$viewAccessForm->populate((array) $viewAccess);
		}
		
		$viewAccessForm->addElement('submit', 'submit', array('label' => _t("Update")));
		$this->view->form = $viewAccessForm;
	}
	
	public function deleteUserAction ()
	{
	    $id = $this->_request->getParam('id');
	    $user = Zend_Registry::get('user');
	    
	    if($id != $user->id)
	    {
		     Users_Lib_Manager::deleteUser($id);
	
			_message(_t('user deleted'));
	    }
	    else 
	    {
	    	_error(_t("Are you crazy ? Are you sure to delete yourself ?!"));
	    }
	    
	    return $this->_redirect( $this->_helper->route->short('index'));
	    
	}
	public function deleteGroupAction ()
	{
	    $id = (int) $this->_request->getParam('id');
	    
	    Users_Lib_Manager::deleteGroup($id);
	    
		_message(_t('group deleted'));

	    return $this->_redirect( $this->_helper->route->short('index'));
	    
	}
	public function deleteViewaccessAction ()
	{
	    $id = (int) $this->_request->getParam('id');
	    
	    $viewAccessModel = new CMS_Acl_DbTable_ViewAccess();
	    $viewAccessModel->delViewAccess($id);
	    
		_message(_t('group deleted'));

	    return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function activeUserAction()
	{
		$id = (int)$this->_request->getParam('id');
		$user = new Users_Object_User($id);
		$user->active();
		$user->save();
		
		_message(vsprintf(_t('user %s %s actived'), array($row->firstname, $row->lastname)));

		return $this->_redirect( $this->_helper->route->short('index'));
	}
	public function deactiveUserAction()
	{
		$id = (int)$this->_request->getParam('id');
		$user_id = (int)Zend_Registry::get('user')->id;
	    
	    if($id != $user_id)
	    {
			$user = new Users_Object_User($id);
			$user->desactive();
			$user->save();
		
			_message(_t('user desactived'));
	    }
	    
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function exportAction() {

		$form = new Users_Form_Export();
		
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {
				
				// Désactivation du rendu
				$this->_helper->viewRenderer->setNoRender(true);
				$this->_helper->layout->disableLayout();
				
				// Récupération des users
				$where = array('group' => $form->getValue('groupList'));
				$users = Users_Object_User::get($where);
				
				/*
				 * Construction du CSV
				 */
				
				$rows 	= array('id', 'civility', 'firstname', 'lastname', 'username', 'isActive', 'date', 'group_id', 'group_name');
				$metas 	= array();
				$return = array();
				
				// Récupération de la liste exhaustive des metas dispo
				foreach($users as $user)
					foreach($user->metas as $meta => $value)
						if(!in_array($meta, $metas))
							$metas[] = $meta;
				
				// Récupération des données
				foreach($users as $user) {
					
					$user_infos = array();
					
					// Données générales
					foreach($rows as $name){
						if($name == 'group_id')
							$user_infos[] = $user->group->id;
						else if($name == 'group_name')
							$user_infos[] = $user->group->name;
						else
							$user_infos[] = $user->{$name};
					}

					// Données metas
					foreach($metas as $meta){
						$user_infos[] = $user->metas->{$meta};
					}
					
					$return[] = $user_infos;
					
				}
				
				// Ouverture d'un fichier "echo"
				$out = fopen('php://output', 'w');
				
				// Entête du fichier
				fputcsv($out, array_merge($rows, $metas), ',');
				
				// Données
				foreach($return as $row){
					fputcsv($out, $row);
				}
				fclose($out);
				
				// Header download CSV
				$this->getResponse()->setHeader('Content-Type', 'application/csv-tab-delimited-table', true);
				$this->getResponse()->setHeader('Content-Transfer-Encoding', 'binary', true);
				$this->getResponse()->setHeader('Content-disposition', 'attachment; filename="test.csv"', true);
				
			}
		}
		
		$form->setAction($this->_helper->route->short('export'));
		$this->view->form = $form;
	}
	
	public function optionsAction()
	{
		$this->redirectIfNoRights('mod_users', 'manage');
		
		$form = new Users_Form_OptionsUsers();
		
		$config = CMS_Application_Config::getInstance();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$config->set("mod_users-options", json_encode($form->getValues()));
				$this->_redirectCurrentPage();
			}
		}
		else {
			$options = json_decode($config->get("mod_users-options"), true);
			
			if($options)
				$form->populate($options);
		}
		
		$this->view->form = $form;
	}
	
	public function permissionsAction()
	{
		$this->redirectIfNoRights('mod_users', 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$formAcl = new CMS_Acl_Form_BackAclForm("mod_users");
		
		if ($this->getRequest()->isPost()) {
			if ($formAcl->isValid($_POST)) {
				$backAcl->updatePermissionsFromAclForm("mod_users", $_POST['ACL']);
				$this->_redirectCurrentPage();
			}
		}
		
		$this->view->formAcl = $formAcl;
	}
}