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

class Users_LoginController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$pageForm = new Users_Form_LoginForm();
		 
		if($this->getRequest()->isPost() && $pageForm->isValid($_POST)) {

			$id 	= $this->_request->getPost('id');
			$pass 	= $this->_request->getPost('pass');

			if(!CMS_Acl_User::login($id, $pass)){
				$pageForm->getElement('pass')->setErrors(array(_t('Invalid login')));
			}
			else {
				if(!isset($_SESSION['lasturl'])){
					$this->_redirect($this->_helper->route->full('admin'));
				}
				else{
					$url = $_SESSION['lasturl'];
					unset($_SESSION['lasturl']);
					$this->_redirect($url);
				}
			}
		}
		
		$pageForm->setAction('/administration/login');		
		$this->view->form = $pageForm;
	}
	
	public function loginfrontAction()
	{
		$pageForm = new Users_Form_LoginForm();
		 
		if($this->getRequest()->isPost()) {

				$id 	= $this->_request->getPost('email_user');
				$pass 	= $this->_request->getPost('password_user');

				if( $id == '' || $pass == ''){
					_error(_t('email & password null'));
    				
					$this->_redirect($_SERVER['HTTP_REFERER']);
					
				}
				else {
					if(CMS_Acl_User::login($id, $pass)){
						
						if(isset($_SERVER['HTTP_REFERER'])){
							$this->_redirect($_SERVER['HTTP_REFERER']);
						}
						else{
							$this->_redirect($this->_helper->route->full('front'));
						}
					}
					else {
						_error(_t('wrong email or password'));
						$this->_redirect($_SERVER['HTTP_REFERER']
						);
					}
				}
		}
	}
	
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_redirect($this->_helper->route->full('admin_login'));

	}
	
	public function logoutfrontAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect($_SERVER['HTTP_REFERER']);

	}
}

