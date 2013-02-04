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

class CMS_Page_Object extends CMS_Object_MultiLangEntity {
		
	public $id_page;
	
	public $title;
	public $meta_keywords;
	public $meta_description;
	
	public $url_system;
	public $url_rewrite;
	public $rewrite_var;
	
	public $enable;
	public $visible;
	public $type;
	
	public $wildcard;
	public $api;
	public $content_id;

	public $template;
	public $diaporama;
	
	protected $url_params;
	
	private $rewrite_var_object;
	
	/**
	 * Home page ID
	 * @var int
	 */
	const HOME_ID = 1;
	
	protected static $_modelClass = "CMS_Page_Model_Pages";
	protected static $_model;
	
	/**
	 * @var Zend_Cache_Core
	 */
	private static $_cacheObject;
	private static $_cache;
	

	public static function getFromDB($where = array(), $order = null, $limit = null, $id_lang = CURRENT_LANG_ID){
		
		if (isset($where["url_system"]) && is_string($where["url_system"]) && $where["url_system"][0] == '/') {
		    $where["url_system"] = substr($where["url_system"], 1);
		}
		
		return parent::get($where, $order, $limit, $id_lang);
	}
	
	public static function getOneFromDB($where = array(), $order = null, $limit = null, $id_lang = CURRENT_LANG_ID){
		
		$return = self::getFromDB($where, $order, $limit, $id_lang);
		
		if(is_array($return) && !empty($return))
			return reset($return);
		
		return null;
	}
	
	public static function get($param = null) {
		
		if(!$param){
			$pages = self::getCache();
			return $pages["all"];
		}
		else if(is_int($param)){
			$datas = self::getPageFromID($param);
		}
		elseif(is_string($param)){
			$datas = self::getPageFromUri($param);
		}
		elseif(is_array($param) || $param === null)
		{
			self::_getModel();
			$datas = self::$_model->get($param);
		}
		else
			throw new Zend_Exception(_t("Invalid parameter"));
		
		if($datas != null && !is_array($datas)) {
			return clone $datas;
		}
		elseif($datas != null && is_array($datas))
		{
			$return = array();
			if( count($datas) > 0 )
			{
				foreach ($datas as $data) {
					if( is_array($data) ) {
						array_push($return, new static($data));
					}
					else if ( is_object($data) && ($data instanceof CMS_Page_Object)) {
						array_push($return, clone $data);
					}
				}
			}
			return $return;
		}
		else
			return null;
	}
	
	public static function getPageFromUri($uri)
	{
		if ($uri[0] == '/') {
		    $uri = substr($uri, 1);
		}

		if($uri){
			
			$pages = self::getCache();
		
			// URL système ?
			if (isset($pages["url_system"][$uri])) {
				return $pages["all"][$pages["url_system"][$uri]];
			}
			// URL rewrite ?
			else if (isset($pages["url_rewrite"][$uri])) {
				return $pages["all"][$pages["url_rewrite"][$uri]];
			}
			// Test wildcards
			else {
				foreach ($pages["wildcards"] as $url => $id_page) {
					
					// Le wildcard correspond ?
					if(stripos($uri, $url) === 0){
						
						/*
						 * N'est ce pas un faux positif ?
						 * 
						 * explication : 
						 * 
						 * ordre du cache des pages :
						 * - docs/view/223
						 * - docs/view/2
						 * 
						 * url testée : docs/view/227
						 * 
						 * Si on ne teste pas l'existance d'un slash en début de paramètre d'url,
						 * 27 va passer en paramètre d'url de docs/view/2. Ce qui est à exclure.
						 * On break donc la boucle.
						 * 
						 * !! CONCLUSION : Les paramètres d'url d'un wildcard commence forcement par un "/"  !!
						 */
						if(substr($uri, strlen($url), 1) != "/")
							break;

						$page = clone $pages["all"][$id_page];
						
						$page->wildcard = true;
						$page->url_params = substr($uri, strlen($url));
						
						return $page;
					}
				}
			}
		}

		return null;
	}

	public static function getPageFromID($id) {
		$cachePages = self::getCache();
		return $cachePages["all"][$id];
	}
	
	public function getUrl($lang_code = CURRENT_LANG_CODE) {
		
		// Objet dans une unique langue
		if (isset($this->_id_lang)) {
			if (isset($this->url_rewrite)) {
				$url = '/' . $this->url_rewrite;
			}
			else {
				// Si on cherche à changer de langue => exception car objet en mono langue
				if($lang_code != CURRENT_LANG_CODE)
					throw new Exception('Changement de langue impossible');
				else if(CURRENT_LANG_CODE == DEFAULT_LANG_CODE)
					$url = '/' . $this->url_system;
				else
					$url = '/' . CURRENT_LANG_CODE . '/' . $this->url_system;
			}
		}
		// Objet avec toutes ses langues
		else {
			
			$config	= CMS_Application_Config::getInstance();
			$availableLangs = json_decode($config->get("availableFrontLang"), true);
			
			$lang_id = array_search($lang_code, $availableLangs);

			if (isset($this->url_rewrite[$lang_id]))
				$url = '/' . $this->url_rewrite[$lang_id];
			else if($lang_code == DEFAULT_LANG_CODE)
				$url = '/' . $this->url_system;
			else
				$url = '/' . $lang_code . '/' . $this->url_system;
			
		}

		if($this->isWildcard()) {
			$url .= $this->getUrlParams();
			
			// Ajout des variables d'url a la fin de celle ci si c'est un wildcard, si les variables sont déclarée, et si l'objet est passé 
			if ($this->rewrite_var && $this->rewrite_var_object) {
				$rewrite_var = $this->rewrite_var;
				foreach (get_object_vars($this->rewrite_var_object) as $attr_name => $attr_val) {
					$rewrite_var = str_ireplace('%'.$attr_name.'%', $attr_val, $rewrite_var);
				}
				$rewrite_var = str_replace(array(' ', '/'), '-', $rewrite_var);
				$url = $url . '/' . $rewrite_var;
			}
		}
		
		return $url;
	}
	
	public function isWildcard(){
		return ($this->wildcard) ? true : false ;
	}
	
	public function getUrlParams(){
		return $this->url_params;
	}
	
	
	public static function setCache(Zend_Cache_Core $cache)
	{
		self::$_cacheObject = $cache; 
	}

	public static function getCache(){
		
		if(!self::$_cache) {
			if(!self::$_cache = self::$_cacheObject->load("CMS_Page_Base_".UNIQUE_ID.MULTI_SITE_ID)){
				self::$_cache = self::createCache();
			}	
		}
		
		return self::$_cache;
	}
	
	protected static function createCache()
	{
		$all 			= array();
		$url_system 	= array();
		$url_rewrite 	= array();
		$url_wildcard 	= array();
		
		// Récupération de toutes les pages du site
		$pages = parent::get(null, null, null, 'all');	
		
		if(!empty($pages)) {
			foreach ($pages as $page) {
				
				// Toutes les infos des pages
				$all[$page->id_page] = $page;
				
				// URL système
				$url_system[$page->url_system] = $page->id_page;
				
				// URL système si wildcard
				if( $page->wildcard )
					$url_wildcard[$page->url_system] = $page->id_page;
					
				// Toutes les URL rewrite
				if( isset($page->url_rewrite) ){
					foreach($page->url_rewrite as $rewrite){
						$url_rewrite[$rewrite] = $page->id_page;
						
						// Rewrite si wildcard
						if($page->wildcard )
							$url_wildcard[$rewrite] = $page->id_page;
					}
				}
					
				
			}
		}
		
		/*
		 * Fonction de tri naturel inversé 
		 */
		if(!function_exists("strnatcmp_inverse")){
			function strnatcmp_inverse($a, $b){
				return -strnatcmp($a, $b);
			}
		}
		
		/*
		 * Tri des clé du plus complexe au plus simple
		 * Évite que des URL complexes match une wildcard trop simple
		 */ 
		uksort($url_system, 	'strnatcmp_inverse');
		uksort($url_rewrite, 	'strnatcmp_inverse');
		uksort($url_wildcard, 	'strnatcmp_inverse');
		
		$pages = array(
			"all" 			=> $all,
			"url_rewrite"	=> $url_rewrite,
			"url_system" 	=> $url_system,
			"wildcards" 	=> $url_wildcard
		);

		self::$_cacheObject->save($pages, "CMS_Page_Base_".UNIQUE_ID.MULTI_SITE_ID);
		
		return $pages;
	}
	
	public static function updateCache()
	{
		self::deleteCache();
		self::createCache();
	}
	
	protected static function deleteCache()
	{
		self::$_cacheObject->remove("CMS_Page_Base_".UNIQUE_ID.MULTI_SITE_ID);
	}
	
	public function setRewriteVarObject($object = null) {
		if ($object) {
			if (!is_object($object))
				throw new Exception('Page object : rewrite_var_object must be an object');
			
			$this->rewrite_var_object = $object;
		}
	}
	
	function __get($name)
	{
		if ( ($return_value = property_exists($this, $name)) === TRUE) $return_value = $this->{$name};
		return $return_value;
	}
	
}