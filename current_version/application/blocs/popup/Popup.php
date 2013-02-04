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

class Bloc_Popup_Popup extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $text;
	public $displayOnce;
	public $timeValid;
	
	protected $_adminFormClass = "Bloc_Popup_AdminForm";
	
	protected static $_translatableFields = array();
	
	public function runtimeFront($view){
		
		if( $this->displayOnce ) {
			$nameCookie = "showPopup".$this->id;
			
			if( isset($_COOKIE[$nameCookie]) ) {
				$this->noRenderBloc = true;
			}
			
			$view->nameCookie = $nameCookie;
		}
		
		
		$view->timeValid  = $this->timeValid;
		$view->text = $this->text;
	}
	
	public function save($post){
		
		$this->text 		= $post["text"];
		$this->displayOnce 	= $post["displayOnce"];
		$this->timeValid 	= $post["timeValid"];
		
		$id = parent::save($post);
		
		return $id;
	}
	
}