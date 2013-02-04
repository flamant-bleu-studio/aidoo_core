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

class Bloc_Calendar_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $id_calendar;
	public $display;
	
	protected $_adminFormClass = 'Bloc_Calendar_AdminForm';
	
	protected static $_translatableFields = array();
	
	public function runtimeFront($view) {
		if ($this->display == 1) {
			$this->templateFront = 'month';
		}
		
		$view->calendar = Calendar_Lib_Render::frontBlocMonth(new DateTime(), $this->id_calendar);
	}
	
	public function save($post) {
		
		$this->id_calendar 	= $post['id_calendar'];
		$this->display 		= $post['display'];
		
		$id = parent::save($post);
		
		return $id;
	}
}
