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

class Bloc_Loginv2_Login extends CMS_Bloc_Abstract implements CMS_Bloc_Interface
{
	public $typeFront;
	
	static $listType = array(0 => "normal", 1 => "slide");
	
	protected static $_translatableFields = array("designation", "title");
	
	protected $_adminFormClass = "Bloc_Loginv2_AdminForm";
		
	public function runtimeFront($view)
	{	
		$user_id = Zend_Registry::get('user')->id;
		
		$view->user = new Users_Object_User($user_id);
		$auth = Zend_Auth::getInstance();
		$view->hasIdentity = ($auth->hasIdentity()) ? true : false;
		$view->type = $this->type;	
		
		$form = new Bloc_Loginv2_FrontForm(null);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		
		$form->setAction($helper->full('users', array("module"=>"users", "controller"=>"users", "action"=>"login")));
		$view->form = $form;
		
	}
	
	public function save($post)
	{
		$this->title = $post["title"];
		$this->typeFront = $post["typeFront"];
				
		$id = parent::save($post);
		
		return $id;
	}
}