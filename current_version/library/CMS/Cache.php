<?php

class CMS_Cache
{
	private static $_cacheObject;
	private static $_cache;
	
	private static $_cacheName;
	
	private static $_instance;
	
	/**
	 * @return CMS_Cache
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Cache();
		}
		return self::$_instance;
	}
	
	private function __construct()
	{
		$this->_cacheName = 'CMS_Cache_'.MULTI_SITE_ID.'_'.UNIQUE_ID;
	}
	
	public static function setCacheObject($object)
	{
		self::$_cacheObject = $object;
	}
	
	private function getCache()
	{
		if(!self::$_cache) {
			if (!self::$_cache = self::$_cacheObject->load($this->_cacheName)) {
				self::$_cache = self::createCache();
			}
		}
		
		return self::$_cache;
	}
	
	private function createCache()
	{
		self::$_cacheObject->save(array(), $this->_cacheName);
	}
	
	public function get($identifier)
	{
		self::getCache();
		
		return self::$_cache[$identifier];
	}
	
	public function set($identifier, $value)
	{
		self::getCache();
		
		self::$_cache[$identifier] = $value;
		self::_saveCache();
	}
	
	private function _saveCache()
	{
		self::$_cacheObject->save(self::$_cache, $this->_cacheName);
	}
	
	public function exist($identifier)
	{
		self::getCache();
		
		if (!defined('APPLICATION_ENV') || (defined('APPLICATION_ENV') && APPLICATION_ENV === 'development'))
			return false;
		
		return self::$_cache[$identifier] ? true : false;
	}
	
	public function delete()
	{
		self::$_cacheObject->remove($this->_cacheName);
	}
}