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

class CMS_Log extends Zend_Log
{
	public function log($message, $priority, $extras = null)
	{
		// Surcharge qui empêche l'affichage d'une exception si pas de configuration pour les logs
		try {
			parent::log($message, $priority, $extras);
		}
		catch (Exception $e) { }
	}
	
    public static function emerg($message)
    {
    	self::getInstance()->log($message, self::EMERG);
    }
    
	public static function alert($message)
    {
    	self::getInstance()->log($message, self::ALERT);
    }
    
	public static function crit($message)
    {
    	self::getInstance()->log($message, self::CRIT);
    }
    
	public static function err($message)
    {
    	self::getInstance()->log($message, self::ERR);
    }
    
	public static function warn($message)
    {
    	self::getInstance()->log($message, self::WARN);
    }
    
	public static function notice($message)
    {
    	self::getInstance()->log($message, self::NOTICE);
    }
    
	public static function info($message)
	{
		self::getInstance()->log($message, self::INFO);
	}
	
	public static function debug($message)
    {
    	self::getInstance()->log($message, self::DEBUG);
    }
	
	/**
	 * @return CMS_Log
	 */
	public static function getInstance()
	{
		return Zend_Registry::get('log');
	}
}