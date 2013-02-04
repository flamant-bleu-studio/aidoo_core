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

class Users_Lib_Manager
{

	public static function updatePassword($datas)
	{
		$userModel = new Users_Model_DbTable_Users();
		$id = $userModel->updatePassword($datas["id"], $datas["password"]);
	}
	public static function getPassword($id)
	{
		$id = (int)$id;
		$userModel = new Users_Model_DbTable_Users();
		$user = $userModel->getPassword($id);
		return $user->password;
	}
	public static function deleteUser($id)
	{
		$userModel = new Users_Model_DbTable_Users();
		$userModel->deleteUser($id);
	    
	    Users_Lib_Manager::removeUserMetaInfo($id);
	}

	public static function getAllGroups()
	{
		$groupModel = new Users_Model_DbTable_Group();
		return $groupModel->getAllGroups();
	}
	public static function addGroup($datas)
	{
		if($datas['group'])
			throw new Zend_Exception(_t("User must have parent group"));
		
		$groupModel = new Users_Model_DbTable_Group();
		$groupId = $groupModel->add($datas);
		
		return $groupId;
	}
	public static function deleteGroup($id)
	{
		$groupModel 		= new Users_Model_DbTable_Group();
		
		$users = Users_Object_User::get(array("group" => $id));
		
		if($users){
			foreach ($users as $user){
				$user->group = 1;
				$user->save();
			}
		}
		
	    $groupModel->delGroup($id);
	}
	
	public static function addUserMetaInfo($userId, $key, $value)
	{
		$userMeta = new Users_Model_DbTable_UsersMeta();
		$userMeta->addUserMeta($userId, $key, $value);
	}
	public static function getUserMetaInfo($userId, $key = null)
	{
		$userMeta = new Users_Model_DbTable_UsersMeta();
		
		if($key !== null)
			return $userMeta->getUserMeta($userId, $key);
		else 
			return $userMeta->getAllUserMeta($userId);
		

	}
	public static function updateUserMetaInfo($userId, $key, $value)
	{
		$userMeta = new Users_Model_DbTable_UsersMeta();
		$userMeta->updateUserMeta($userId, $key, $value);
	}
	public static function removeUserMetaInfo($userId, $key = null)
	{
		$userMeta = new Users_Model_DbTable_UsersMeta();
		$userMeta->removeUserMeta($userId, $key);
	}
	public static function searchUserMetaInfo($value)
	{
		$userMeta = new Users_Model_DbTable_UsersMeta();
		return $userMeta->searchUserMeta($value);
	}
	public static function generateCodeVerif()
	{
		$chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@";
		$codeVerif = "";
		for($u = 1; $u <= 24; $u++) {
		    $nb = strlen($chaine);
		    $nb = mt_rand(0,($nb-1));
		    $codeVerif.=$chaine[$nb];
		}
		return $codeVerif;
	}
	public static function getUserIDFromCodeVerif($code)
	{
		if(!$code)
			return null;
		
		$userMeta = new Users_Model_DbTable_UsersMeta();
		$userId = $userMeta->getIDFromMetaKey('codeVerif', $code);
		
		return $userId;
	}
	
	public static function getUserIDFromCodeVerifPassword($code)
	{
		if(!$code)
			return null;
		
		$userMeta = new Users_Model_DbTable_UsersMeta();
		$userId = $userMeta->getIDFromMetaKey('codeVerifPassword', $code);
		
		return $userId;
	}
	
	public static function getUserIDFromMeta($key = null, $value = null)
	{
		if(!$key || !$value)
		return false;
	
		$userMeta = new Users_Model_DbTable_UsersMeta();
		$user = $userMeta->getIDFromMetaKey($key, $value);
	
		return $user->user_id;
	}
}