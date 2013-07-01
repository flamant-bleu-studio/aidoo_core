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

class CMS_Acl_Plugins_CheckRights extends Zend_Controller_Plugin_Abstract {
	
  	public function preDispatch(Zend_Controller_Request_Abstract $request) {

  		if(!$this->_accessValid($request)) {
  			
  			$link_to_redirect = "/";
  			
  			$_SESSION['lasturl'] = str_replace(Zend_Layout::getMvcInstance()->getView()->baseUrl(),'',$_SERVER['REQUEST_URI']);
			
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$route = Zend_Controller_Action_HelperBroker::getStaticHelper('route');
			
			if ($request->getParam('_isAdmin') == true)
				$link_to_redirect = "/administration/login/";
			else if ($request->getParam('_isMiddle') == true && !defined('CMS_LOGIN_PAGE'))
				$link_to_redirect = $route->full('users', array('action' => 'login'));
			else if ($request->getParam('_isMiddle') == true && defined('CMS_LOGIN_PAGE')) {
				$options = json_decode(CMS_LOGIN_PAGE, true);
				$link_to_redirect = $route->full($options['route'], array('action' => $options['action']));
			}
			
			$redirector->gotoUrl($link_to_redirect);
		}
	}
	
	private function _accessValid(Zend_Controller_Request_Abstract $request) {
		
		$auth = Zend_Auth::getInstance();
		
		try {
			if($auth->hasIdentity())
				$user = new CMS_Acl_User($auth->getIdentity());
			else
				$user = new CMS_Acl_User();
		}catch(Exception $e){
			
			$request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('error');

            defined('ERROR_FROM_PLUGIN') || define('ERROR_FROM_PLUGIN', true);
            
            // Set up the error handler
            $error = new Zend_Controller_Plugin_ErrorHandler();
            $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
            $error->request = clone($request);
            $error->exception = $e;
            $request->setParam('error_handler', $error);
		}
		

		Zend_Registry::set('user', $user);
		
		$backAcl = CMS_Acl_Back::getInstance();
				
		if($request->getParam('_isAdmin') == true)
			return $backAcl->hasPermission('admin', 'login');
		else if($request->getParam('_isMiddle') == true)
			return $backAcl->hasPermission('admin', 'login_middle');
		else
			return true;
	}
	
}