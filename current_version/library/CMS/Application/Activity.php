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

class CMS_Application_Activity {
	
	private static $_instance;
	
	/**
	 * Model's object instance
	 * @var CMS_Application_DbTable_Activities
	 */
	private static $_model;
	private static $_modelClass = "CMS_Application_DbTable_Activities";
	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Application_Activity
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Application_Activity();
		}
		return self::$_instance;
	}
	
	private function __construct(){		
		$hooks = CMS_Application_Hook::getInstance();
		$hooks->add('objectEntityAdded', 'CMS_Application_Activity::addActivity');
		$hooks->add('objectEntityDeleted', 'CMS_Application_Activity::deleteActivity');
	}
	
	public function getActivities($filters = null){
		
		self::getModel();
		
		$results = self::$_model->getActivities($filters);
		
		$activities = array();
		
		foreach($results as $result)
		{
			
			if(class_exists(self::$_modelClass)) {
				$activities[] = new $result->activityClass($result->activityId);
			}
			
		}
		
		return $activities;
		
	}
	
	public static function addActivity($classObject, $idObject){
		
		if(!$classObject || !$idObject)
			throw new Zend_Exception(_t('Missing parameter'));
		
		self::getModel();
		
		self::$_model->addActivity(array("class" => $classObject, "id" => $idObject));
		
	}
	public static function deleteActivity($classObject, $idObject){
		
		if(!$classObject || !$idObject)
			throw new Zend_Exception(_t('Missing parameter'));
		
		self::getModel();
		
		self::$_model->deleteActivity(array("class" => $classObject, "id" => $idObject));
		
	}
	
	protected static function getModel()
	{
		if (empty(self::$_model) && class_exists(self::$_modelClass)) {
			self::$_model = new self::$_modelClass();
			return;
		}
	}
}