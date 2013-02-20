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

class CMS_Application_Hit {
	
	/**
	 * Retrieve hit
	 *
	 * @return int hit
	 */
	public function getHit($type = null, $cle = null)
	{
		if(!$type || !$cle)
			throw new Zend_Exception(_t('Missing parameter'));
			
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT hits FROM " . DB_TABLE_PREFIX . $multi_site_prefix."hits WHERE type = ? AND cle = ?", array($type, $cle));

	    $hit = $results->fetch(Zend_Db::FETCH_COLUMN);
	    
	    if($hit)
	    	return $hit;
	    else 
	        return 0; 
	}
	
	/**
	 * Reset hit
	 *
	 * @param $type hit type
	 * @param $cle hit key
	 */
	public function resetHit($type = null, $cle = null)
	{
		if(!$type || !$cle)
			throw new Zend_Exception(_t('Missing parameter'));
			
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
		$db->update(DB_TABLE_PREFIX . $multi_site_prefix."hits", array('hits' => '0'), array('type = "'.$type.'"', 'cle = "'.$cle.'"'));

	}
	
	/**
	 * increment hit
	 */
	public function incrementHit($type = null, $cle = null)
	{
		if(!$type || !$cle)
			throw new Zend_Exception(_t('Missing parameter'));
		
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
		$nb = $db->update(DB_TABLE_PREFIX . $multi_site_prefix."hits", array('hits' => new Zend_Db_Expr('hits+1')), array('type = "'.$type.'"', 'cle = "'.$cle.'"'));
		
		if($nb < 1)
			$db->insert(DB_TABLE_PREFIX . $multi_site_prefix."hits", array('type' => $type, 'cle' => $cle, 'hits' => 1));
	}
	
	/**
	 * decrement hit
	 */
	public function decrementHit($type = null, $cle = null)
	{
		if(!$type || !$cle)
			throw new Zend_Exception(_t('Missing parameter'));
		
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
		$db->query("UPDATE " . DB_TABLE_PREFIX . $multi_site_prefix."hits SET hits = hits-1 WHERE type = ? AND cle = ?", array($type, $cle));
	}
	
	/**
	 * Get max hit by type
	 *
	 * @return int hit
	 */
	public function getMaxHitByType($type = null, $count = 10)
	{
		if(!$type || !$count)
			throw new Zend_Exception(_t('Missing parameter'));
			
		global $multi_site_prefix;
		
		$count = (int) $count;
		
		if($count < 1)
			$count = 10;
		
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT type, cle, hits FROM " . DB_TABLE_PREFIX . $multi_site_prefix."hits WHERE type = ? ORDER BY hits DESC LIMIT 0, ".$count, array($type));

	    $hits = $results->fetchAll();
	    
	    $return = array();
	    
	    foreach ($hits as $hit)
			$return[$hit["type"]."_".$hit["cle"]] = $hit;

	    return $return;
	}

}