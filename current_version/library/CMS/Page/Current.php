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

class CMS_Page_Current  {
	
	/**
	 * @var CMS_Page_Object
	 */
	private static $_instance;
	
	private static $_originalPage;
		
	public static $id_lang;
	public static $code_lang;
	public static $uri;

	public static function getOriginalPage() {
		if(isset(self::$_originalPage)) {
			return self::$_originalPage;
		}
		
		return null;
	}
	
	public static function getInstance($uri = null) {	
		
		if(defined("DISABLE_CORE_PAGE") && DISABLE_CORE_PAGE === true || defined("NOTFOUND"))
			return null;
		
		if(!isset(self::$_instance)) {
			
			if(!$uri)
				throw new Exception(_t("URL required to get current page instance"));

			if(is_string($uri)){
				self::$uri = $uri;
				$page = CMS_Page_Object::getPageFromUri($uri);
			}
			else if(is_int($uri))
				$page = CMS_Page_Object::getPageFromID($uri);
				
			if($page){
				self::$_originalPage = clone $page;
				self::$_instance = clone $page;
				
				self::extractLangFromRewrite();
			}
			else {
				self::$_instance = null;
				return null;
			}
		}
		
		return self::$_instance;
	}
	
	private function __construct(){}
	
	
	private function extractLangFromRewrite() {
		if(is_array(self::$_instance->url_rewrite)){
			
			$config	= CMS_Application_Config::getInstance();
			$availableLangs = json_decode($config->get("availableFrontLang"), true);
			
			if (self::$_instance->isWildcard())
				$uri = str_replace(self::$_instance->getUrlParams(), '', self::$uri);
			else 
				$uri = self::$uri;
			
			self::$id_lang 	= array_search($uri, self::$_instance->url_rewrite);
			self::$code_lang = $availableLangs[self::$id_lang];
		}
	}
	
	public static function pageInstanceToMonoLang(){

		$class = new Zend_Reflection_Class(self::$_instance);
		
		$properties = $class->getProperties();
		
		foreach ($properties as $property) {
			
			$propertyName = $property->getName();

			if($property->isPublic() && is_array(self::$_instance->{$propertyName}))
				self::$_instance->$propertyName = self::$_instance->{$propertyName}[CURRENT_LANG_ID];
		}

		return self::$_instance;
	}

}