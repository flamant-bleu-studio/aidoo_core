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

class Users_MiddleController extends Zend_Controller_Action
{
	public function editProfilAction() {
		
		$auth = Zend_Auth::getInstance();
		
		if( !defined('CMS_MIDDLE_INDEX') || !$auth->hasIdentity()) {
			throw new Exception(_t('Page not found'), 404);
		}
		
		$user_id = Zend_Registry::get('user')->id;
		
		$user = reset(Users_Object_User::get(array("id" => $user_id)));
		$form = new Users_Form_Edit();
		$form->setAction($this->_helper->route->short('edit-profil'));
		
		/** Formulaire XML pour les métas **/
		$fileXmlMetas = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/users/config.xml';
		if( file_exists($fileXmlMetas) ) {
			// Ouverture du fichier xml
			$xml 		= new Zend_Config_Xml($fileXmlMetas);
			// Création du formulaire à partir des éléments du xml
			$form_metas = new CMS_Form_Default(array("xml" => $xml->nodes));
			
			// Ajout du formulaire des metas au formulaire principale
			$form->addSubForm($form_metas, 'metas');
		}
		
		$user = new Users_Object_User($user_id);
		if( $this->getRequest()->isPost() ) {
			if( $form->isValid($_POST) ) {
				
				/** Gestion des metas **/
				$metas = array();
				if( $xml->nodes->elements ) {
					foreach ($xml->nodes->elements as $element => $infos) {
						
						if($element == 'images'){
							$metas[$element] = reset($_POST[$element]);
						}
						else
							$metas[$element] = $_POST[$element];
					}
				}
				
				$metas["mustEditProfil"] = null; // N'est plus redirigé vers la page d'édition profil lors du login
				
				/** Enregistrement **/
				$user->username		= $form->getValue("username");
				$user->civility 	= $form->getValue("civility");
				
				if( !empty($metas) )
					$user->metas = $metas;
				
				$user->save();
				
				_message(_t('Profile successfully changed'));
				$this->_redirect(CMS_MIDDLE_INDEX);
			}
			else {
				$form->populate($_POST);
			}
		}
		else {
			/** Informations génériques **/
			$datas = array(
				'username'  => $user->username,
				'email' 	=> $user->email,
				'civility' 	=> $user->civility,
				'firstname' => $user->firstname,
				'lastname' 	=> $user->lastname
			);
			
			/** Informations autres (metas) **/
			$metas = $user->metas;
			if( $metas ) {
				foreach ($metas as $key => $value) {
					if($key == 'images'){
						$datas[$key] = array($value);
					}
					else
						$datas[$key] = $value;
				}
			}
			
			$form->populate($datas);
		}
		
		$this->view->info_profil = array(
			'username'  => $user->username,
			'email' 	=> $user->email,
			'civility' 	=> $user->civility,
			'firstname' => $user->firstname,
			'lastname' 	=> $user->lastname
		);
		
		$form->submit->setAttrib("readonly", "readonly");
		$form->submit->setValue("Enregistrer");
		
		$this->view->form = $form;
		$this->view->isUserFacebook = $user->id_facebook ? 1 : 0;
	}
	
	public function editPasswordAction() {
		$auth = Zend_Auth::getInstance();
		
		if( !defined('CMS_MIDDLE_INDEX') || !$auth->hasIdentity()) {
			throw new Exception(_t('Page not found'), 404);
		}
		
		$user_id = Zend_Registry::get('user')->id;
		
		$user = reset(Users_Object_User::get(array("id" => $user_id)));
		
		if( $user->id_facebook ) {
			_error('Un utilisateur connecté avec Facebook ne peut pas modifier son mot de passe.');
			$this->_redirect(CMS_MIDDLE_INDEX);
		}
		
		$form = new Users_Form_PasswordForm();
		$form->setAction($this->_helper->route->short('edit-password'));
		
		if( $this->getRequest()->isPost() ) {
			if( $form->isValid($_POST) ) {
				
				$model = new Users_Model_DbTable_Users();
				
				// Le mot de passe courant est correct
				if( $model->getPassword($user_id) == $form->getValue('oldPassword') ) {
					
					$model->updatePassword($user_id, $form->getValue('password'));
					
					_message(_t('Password changed'));
					$this->_redirect(CMS_MIDDLE_INDEX);
				}
				else {
					$form->getElement('oldPassword')->setErrors(array(_t('Invalid password')));
				}
			}
			else {
				_error(_t('Invalid form'));
			}
		}
		
		$this->view->form = $form;
	}
}