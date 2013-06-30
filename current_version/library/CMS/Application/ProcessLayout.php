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

require_once 'CMS/Minify/Css.php';
require_once 'CMS/Minify/Js.php';

class CMS_Application_ProcessLayout {
	
	private static $_instance;
	
	private $_jsFiles;
	private $_jsScripts;
	private $_jsScriptsBottom;
	private $_cssFiles;
	private $_cssScripts;
	private $_jQueries;
	private $_head;
	private $_nameCache;
	
	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Application_ProcessLayout
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Application_ProcessLayout();
		}
		return self::$_instance;
	}
	
	private function __construct(){
		
		$this->_jQueries = array();
		
		$this->_jsFiles = array();
		$this->_jsScripts = array();
		$this->_jsScriptsBottom = array();
		
		$this->_cssFiles = array();
		$this->_cssScripts = array();

		$this->_head = array();
		
		$this->_tinyMCE = false;
		
		$this->baseUrl = Zend_Layout::getMvcInstance()->getView()->baseUrl();
	}
	
	public function getHTMLJQueryLibs()
	{
		$output = "";
		
		foreach ($this->_jQueries as $jquery) 
		{
			if( !(defined('CACHE_CSS_JS') && (CACHE_CSS_JS)) || !$jquery['cache'])
			{
				if ($jquery['mode']=='inline')
				{
						$output .= '<script type="text/javascript">';
						$output .= $jquery['href']."\n";
						$output .= "</script>\n";
				}
				else
				{
					$output .= '<script type="text/javascript" src="'.$this->baseUrl.$jquery['href'].'">';
					$output .= "</script>\n";
				}
			}
		}
		return $output;
		
	}

	public function getHtmlCssFiles(){
		
		$output = "";
		
		
		foreach ($this->_cssFiles as $stylesheet) 
		{
			if( !(defined('CACHE_CSS_JS') && (CACHE_CSS_JS)) || !$stylesheet['cache'])
			{
				$output .= "<link rel='stylesheet' type='text/css' href='".$this->baseUrl.$stylesheet['src']."' media='".$stylesheet['media']."' />\n";
			}
		}
		
		return $output;
	}
	public function getHtmlCssScripts()
	{
		if(defined('CACHE_CSS_JS') && CACHE_CSS_JS === true || empty($this->_cssScripts))
			return;

		$output = '<style type="text/css">';
		
		foreach ($this->_cssScripts as $stylesheet) {
			$output .= $stylesheet['script'];
		}
		
		$output .= '</style>';
		
		return $output;
	}
	
	public function getHtmlJsFiles(){

		$output = "";
		
		foreach ($this->_jsFiles as $script)  {
			if (isset($script['external'])) {
				$baseUrl = null;
				$output .= "<script type='text/javascript' src='".$baseUrl.$script['src']."'></script>\n";
			}
			else {
				if( !(defined('CACHE_CSS_JS') && (CACHE_CSS_JS)) || !$script['cache'] )
				{
					$baseUrl = $this->baseUrl;
					$output .= "<script type='text/javascript' src='".$baseUrl.$script['src']."'></script>\n";
				}
			}
		}
		
		return $output;
	}
	public function getHtmlJsScripts()
	{
		if(defined('CACHE_CSS_JS') && CACHE_CSS_JS === true || empty($this->_jsScripts))
			return;
		
		$output = "<script type='text/javascript'>\n";
		$output .= "$(document).ready(function() {\n";
		
		foreach ($this->_jsScripts as $script) {
			$output .= $script['script']."\n";
		}
		
		$output .= "});\n";
		$output .= "</script>\n";
		
		return $output;
	}
	
	public function getHTMLJsScriptsBottom()
	{
		if(empty($this->_jsScriptsBottom))
			return;

		$output = "<script type='text/javascript'>\n";
		
		foreach ($this->_jsScriptsBottom as $script) {
			$output .= $script['script']."\n";
		}
		
		$output .= "</script>\n";
		
		return $output;
	}

	public function getHeadContent(){
	
		if(!empty($this->_head))
			return implode(" ", $this->_head);
	
		return null;
	}
	
	public function appendJQuery($href, $cache = false, $mode='linked'){
		
		$newJQuery['href']=$href;
		$newJQuery['mode']=$mode;
		$cache = (isset($cache) && $cache) ? 1 : 0;
		$newJQuery['cache']=$cache;
		
		if (!in_array($newJQuery,$this->_jQueries))
		{
			array_push($this->_jQueries,$newJQuery);
		}
	}
	
	public function appendCssFile($src, $cache = false, $media = 'all'){
		$cache = (isset($cache) && $cache) ? 1 : 0;
		$new = array(
			'src' => $src,
			'media' => $media,
			'cache' => $cache
		);

		if (!in_array($new, $this->_cssFiles))
			array_push($this->_cssFiles, $new);

	}
	public function appendCssScript($script){
		
		$new['script'] = $script;
		
		if (!in_array($new, $this->_cssScripts))
			array_push($this->_cssScripts, $new);
			
	}

	public function appendJsFile($src, $cache = false, $isExternal = false){
		$cache = (isset($cache) && $cache) ? 1 : 0;
		$new = array(
			'src' => $src,
			'cache' => $cache
		);
		
		if($isExternal)
			$new["external"] = true;
		
		if (!in_array($new, $this->_jsFiles))
			array_push($this->_jsFiles, $new);

	}	
	public function appendJsScript($script, $place = null){
		
		$new = array(
			'script' => $script
		);
		
		if(!$place)
		{
			if (!in_array($new, $this->_jsScripts))
				array_push($this->_jsScripts, $new);
		}
		else if($place == "bottom")
		{
			if (!in_array($new, $this->_jsScriptsBottom))
				array_push($this->_jsScriptsBottom, $new);
		}
	}
	
	public function appendHeadContent($content){
		array_push($this->_head, $content);
	}

	/** Retourne le cache **/
	public function getCacheCssJs()
	{
		$this->_nameCache = array('css' => '/cache.css', 'js' => '/cache.js');
		if( (defined('CACHE_CSS_JS') && (CACHE_CSS_JS)) ) // Cache activé 
		{
			if( $this->cacheExist() ) // Cache existe
			{
				if( defined('CACHE_CSS_JS_GENERATE') )
				{
					if ( CACHE_CSS_JS_GENERATE == 'force' ) // Force à re-générer le cache
					{
						$this->createCache();
					}
					else if( CACHE_CSS_JS_GENERATE == 'date' ) // Vérification de la validité du cache
					{
						if( ($this->lastDateModifCss() > filemtime(PUBLIC_PATH.SKIN_URL.$this->_nameCache['css'])) ) // Cache Css pas a jour
						{
							$this->createCache('css');
						}
						if( ($this->lastDateModifJs() > filemtime(PUBLIC_PATH.SKIN_URL.$this->_nameCache['js'])) ) // Cache Js pas a jour
						{
							$this->createCache('js');
						}
					}
				}
			}
			else // Le cache n'existe pas
			{
				$this->createCache();
			}
			
			$cache = "<script type='text/javascript' src='".BASE_URL.SKIN_URL.$this->_nameCache['js'].'?id='.filemtime(PUBLIC_PATH.SKIN_URL.$this->_nameCache['js'])."'></script>\n";
			$cache .= "<link rel='stylesheet' type='text/css' href='".BASE_URL.SKIN_URL.$this->_nameCache['css'].'?id='.filemtime(PUBLIC_PATH.SKIN_URL.$this->_nameCache['css'])."'/>";
		}
		return (isset($cache) ? $cache : '');
	}
	
	/** Retourne si le cache existe ou non **/
	private function cacheExist()
	{
		if(is_array($this->_nameCache))
		{
			foreach ($this->_nameCache as $file)
			{
				if(!file_exists(PUBLIC_PATH.SKIN_URL.$file))
					return false;
			}
		}
		else 
			return false;

		return true;
	}
	
	/** Retourne la date la plus récente de dèrnière modification des fichiers CSS **/
	private function lastDateModifCss()
	{
		$lastDate = 0;
		
		foreach ($this->_cssFiles as $file)
		{
			if(file_exists(PUBLIC_PATH.$file['src']) && $file['cache'])
			{
				$tempDate = filemtime(PUBLIC_PATH.$file['src']);
				if($tempDate > $lastDate)
					$lastDate = $tempDate;
			}
		}
		
		return $lastDate;
	}
	/** Retourne la date la plus récente de dèrnière modification des fichiers JS **/
	private function lastDateModifJs()
	{
		$lastDate = 0;
		
		foreach ($this->_jQueries as $file)
		{
			if(file_exists(PUBLIC_PATH.$file['href']) && $file['cache'])
			{
				if(!$file['mode']=='inline')
				{
					$tempDate = filemtime(PUBLIC_PATH.$file['href']);
					if($tempDate > $lastDate)
						$lastDate = $tempDate;
				}
			}
		}

		foreach ($this->_jsFiles as $file)
		{
			if(file_exists(PUBLIC_PATH.$file['src']))
			{
				if(!$file['external'])
				{
					$tempDate = filemtime(PUBLIC_PATH.$file['src']);
					if($tempDate > $lastDate)
						$lastDate = $tempDate;
				}
			}
		}
		
		return $lastDate;
	}
	
	/** Création du nouveau cache **/
	private function createCache($type = null)
	{
		if( ($type == null) || ($type == 'css') )
			$temp_cache_css = $this->getCacheCss();
		if( ($type == null) || ($type == 'js') )
			$temp_cache_js 	= $this->getCacheJs();
		
		if( ($type == null) || ($type == 'css') )
			file_put_contents(PUBLIC_PATH.SKIN_URL.$this->_nameCache['css'], $temp_cache_css);
		if( ($type == null) || ($type == 'js') )
			file_put_contents(PUBLIC_PATH.SKIN_URL.$this->_nameCache['js'], $temp_cache_js);
	}
	
	/** Retourne l'url correct pour les images dans les css **/
	private function url($line, $file)
	{
		preg_match('/(url\(\'|url\("|url\()([a-zA-Z-\/\\\._]*)(\'\)|"\)|\))/', $line, $matches);

		if(isset($matches[2]))
		{
			//echo $line.' => ';
			$temp_array = explode('/', $file);
			$temp = str_replace(array_pop($temp_array), '', $file); // retire le nom du fichier
			$line = str_replace($matches[2], '../..'.$temp.$matches[2], $line); // remplace le lien
			//echo $line.'<br/><br/>';
		}
		return $line;
	}
	
	/** Retourne le nouveau cache CSS **/
	private function getCacheCss()
	{
		/** css / récupération **/
		foreach ($this->_cssFiles as $css)
		{
			if(file_exists(PUBLIC_PATH.$css['src']) && $css['cache'])
			{
				//$cache_temp_css .= file_get_contents(PUBLIC_PATH.$file['src'], false);
				
				$file = new SplFileObject(PUBLIC_PATH.$css['src']);
				while (!$file->eof())
				{
    				$line = $file->fgets();
					$line = $this->url($line, $css['src']);
					$cache_temp_css .= $line;
				}
			}
		}

		foreach ($this->_cssScripts as $stylesheet)
		{
			if(!empty($stylesheet['script']))
				$cache_temp_css .= $stylesheet['script'];
		}
		
		/** css / minimisation **/
		if( defined('CACHE_CSS_JS_MINI') && (CACHE_CSS_JS_MINI) ) // Minimisation activé
		{
			$cache_temp_css = CMS_Minify_Css::minify($cache_temp_css);
		}
		
		return $cache_temp_css;
	}
	/** Retourne le nouevau cache JS **/
	private function getCacheJs()
	{
		/** js / récupération **/
		foreach ($this->_jQueries as $file)
		{
			if(file_exists(PUBLIC_PATH.$file['href']) && $file['cache'])
			{
				if( !($file['mode'] == 'inline') )
					$cache_temp_js .= file_get_contents(PUBLIC_PATH.$file['href'], false) ."\n";
			}
		}
		
		foreach ($this->_jsFiles as $file)
		{
			if(file_exists(PUBLIC_PATH.$file['src']) && $file['cache'])
			{
				if(!$file['external'])
					$cache_temp_js .= file_get_contents(PUBLIC_PATH.$file['src'], false)."\n";
			}
		}
		
		foreach ($this->_jsScripts as $script)
		{
			if(!empty($script['script']))
				$cache_temp_js .= "$(document).ready(function() {".$script['script']."});\n";
		}
		
		/** js / minimisation **/
		if( defined('CACHE_CSS_JS_MINI') && (CACHE_CSS_JS_MINI) ) // Minimisation activé
		{
			$cache_temp_js = CMS_Minify_Js::minify($cache_temp_js);
		}
		
		return $cache_temp_js;
	}
	
}