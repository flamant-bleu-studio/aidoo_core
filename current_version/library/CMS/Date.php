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

class CMS_Date
{	
	
	public static $date = array();
	
	public static function getMonth ($date, $lang) 
	{
		self::getTranslatedMonth($lang);
		
		return self::$date['month'][$lang][$date->format('m')];
	}
	
	public static function getTranslatedMonth($lang){
		self::$date['month']['fr'] = array(
			"01" => 'janvier',
			"02" => 'février',
			"03" => 'mars',
			"04" => 'avril',
			"05" => 'mai',
			"06" => 'juin',
			"07" => 'juillet',
			"08" => 'août',
			"09" => 'septembre',
			"10" => 'octobre',
			"11" => 'novembre',
			"12" => 'décembre'
		);
	}
}