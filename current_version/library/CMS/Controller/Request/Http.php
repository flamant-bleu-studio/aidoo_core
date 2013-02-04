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

class CMS_Controller_Request_Http extends Zend_Controller_Request_Http
{

	public function getParamInt($key, $default = null)
	{
		$return = parent::getParam($key, $default);

		if (!is_numeric($return)) 
			throw new CMS_Controller_Request_Exception('Paramètre d\'URL invalide : nombre entier attendu');
		
		return (int) $return;
	}
	
	public function getParamAlphaNum($key, $default = null)
	{
		$return = parent::getParam($key, $default);

		if (!ctype_alnum($return))
			throw new CMS_Controller_Request_Exception('Paramètre d\'URL invalide : chaîne alphanumérique attendue');
		
		return $return;
	}
	
	public function getParam($key, $default = null)
	{
		$return = parent::getParam($key, $default);
		
		if(is_string($return))
			$return = htmlspecialchars($return);
		
		return $return;
	}
	
}
