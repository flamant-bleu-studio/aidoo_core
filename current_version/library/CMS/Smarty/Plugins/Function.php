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

class CMS_Smarty_Plugins_Function {
	
	public static function routeShort($params, $content){
		
		$action = $params['action'];
		unset($params['action']);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		
		return $helper->getFrontController()->getBaseUrl().$helper->short($action, $params);
	}	
	
	public static function routeFull($params, $content){
		
		$routeName = $params['route'];
		unset($params['route']);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		return $helper->getFrontController()->getBaseUrl().$helper->full($routeName, $params);
	}

	/**
	 * Sample : {image folder='articles' name='test.jpg', size='default'}
	 */
	public static function image($params, $content)
	{
		return CMS_Image::getLink($params['folder'], $params['name'], $params['size']);
	}
}

