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

abstract class CMS_Api_Abstract
{
	protected $content_id;
	
	protected $moduleNamePermission;
	
	protected $isEditMode;
	
	protected $_externalDatas;
	
	/**
	 * View object
	 * @var Zend_View_Interface
	 */
	protected $view;
	
	/**
     * Helper Broker to assist in routing help requests to the proper object
     * @var Zend_Controller_Action_HelperBroker
     */
	protected $_helper = null;
	
	public function __construct($params = null)
	{
		$this->view 	= Zend_Layout::getMvcInstance()->getView();
		$this->_helper 	= Zend_Controller_Action_HelperBroker::getStaticHelper("route");
		
		if(isset($params["content_id"])) {
			$this->setContentID($params["content_id"]);
			$this->isEditMode = true;
		}
	}
	
	/**
	 * Verify permission
	 * @param string $mode (create, edit, delete, manage, ...)
	 * @return bool
	 */
	public function hasPermission($mode)
	{
		if( empty($mode) )
			throw new Zend_Exception(_t("Invalid param"));
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($this->isEditMode === true && $backAcl->hasPermission($this->moduleNamePermission."-".$this->content_id, $mode) 
			|| $this->isEditMode !== true && $backAcl->hasPermission($this->moduleNamePermission, $mode))
			return true;
		else 
			return false;
	}
	
	/**
	 * Set content id to edit
	 * @param int $id
	 */
	public function setContentID($id) {
		$id = (int)$id;
		
		if(!$id)
			throw new Zend_Exception(_t("Invalid ID"));
		
		$this->content_id = $id;
	}
	
	/**
	 * Permet de pre-remplir des informations générique que pourra utiliser l'API
	 * @param array $datas
	 */
	public function setExternalDatas($datas) {
		$this->_externalDatas = $datas; 
	}
}