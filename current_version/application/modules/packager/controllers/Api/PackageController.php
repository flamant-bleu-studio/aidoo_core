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

class Packager_Api_PackageController extends CMS_Controller_Api{

	protected $_actionToken = array();
	protected static $_rights 	= array('changeStatPlugin' => array('mod_packager' => 'edit'));
		
		
	public function indexAction() {}
	
	public function getAction() {}
	
	public function postAction()	{}
	
	public function putAction()	{}
	
	public function deleteAction()	{}

	public function headAction() {}
	
	public function changeStatPluginAction()
	{
		$config = CMS_Application_Config::getInstance();
			
		if(!$params = CMS_Application_Tools::checkPOST(array('pluginFile', 'type')))
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
		
		$configPackageList = json_decode($config->get("activePlugins"),true);
		
		if (!$configPackageList || !array_key_exists($params['pluginFile'], $configPackageList)) {
			$configPackageList[$params['pluginFile']] = $params['type'];
			$this->view->actualStat = 'enable';
		} else {
			unset($configPackageList[$params['pluginFile']]);
			$this->view->actualStat = 'disable';
		}
			
		$this->view->pluginFile = $params['pluginFile'];
		
		$config->set("activePlugins", json_encode($configPackageList));
	}
	
	public function changeStatModuleAction() {
		if(!$params = CMS_Application_Tools::checkPOST(array('name')))
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
		 
		$zendConfig = CMS_Application_Config::getInstance();
		$activeModule = $zendConfig->get("activeModule");
		$activeModule = json_decode($activeModule,true);
		 
		if ($activeModule && (($key = array_search($params['name'], $activeModule)) !== false)) {
			unset($activeModule[$key]);
			$this->view->actualStat = 'disable';
		} else {
			$activeModule[] = $params['name'];
			$this->view->actualStat = 'enable';
		}
		 
		$this->view->moduleName = $params['name'];
		
		$zendConfig->set('activeModule', json_encode($activeModule));
	}	
	
	public function changeStatBlocAction() {
		if(!$params = CMS_Application_Tools::checkPOST(array('name')))
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
			
		$zendConfig = CMS_Application_Config::getInstance();
		$activeBloc = $zendConfig->get("activeBloc");
		$activeBloc = json_decode($activeBloc,true);
			
		if ($activeBloc && (($key = array_search($params['name'], $activeBloc)) !== false)) {
			unset($activeBloc[$key]);
			$this->view->actualStat = 'disable';
		} else {
			$activeBloc[] = $params['name'];
			$this->view->actualStat = 'enable';
		}
			
		$this->view->blocName = $params['name'];
	
		$zendConfig->set('activeBloc', json_encode($activeBloc));
	}
}
