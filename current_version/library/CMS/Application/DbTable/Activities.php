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

class CMS_Application_DbTable_Activities {
	
	public function getActivities($filters = null)
	{
		global $multi_site_prefix;
		
		$sql = "SELECT activityClass, activityId FROM ".$multi_site_prefix."activities ORDER BY date DESC ";
		
		if($filters['limit'])
			$sql .= "LIMIT 0, ".$filters['limit'];
		
		$db = Zend_Registry::get('db');

		$results = $db->query($sql);
		return $results->fetchAll(Zend_Db::FETCH_OBJ);
		
	}
	
	public function addActivity($datas = null)
	{
		if(!$datas["class"] || !$datas["id"])
			throw new Zend_Exception(_t('Missing parameter'));
		
		global $multi_site_prefix;
		
		$datas["date"] = date('Y-m-d H:i:s');
		
		$db = Zend_Registry::get('db');
		$db->insert(
			$multi_site_prefix."activities",
			array( 	
				'activityClass' 	=> $datas["class"], 
				'activityId'		=> $datas["id"],
				'date'				=> $datas["date"]
			)
		);
	}
	
	public function deleteActivity($datas = null)
	{
		if(!$datas["class"] || !$datas["id"])
			throw new Zend_Exception(_t('Missing parameter'));
		
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
		$db->delete($multi_site_prefix."activities", 'activityClass = "'. $datas["class"].'" AND activityId = '.$datas["id"]);
	}
	
}