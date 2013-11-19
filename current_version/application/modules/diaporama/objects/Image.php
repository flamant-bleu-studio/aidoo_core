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

class Diaporama_Object_Image extends CMS_Object_MonoLangEntity {
	
	public $id;
	public $parent_id;
	public $text;
	public $image;
	
	public $link_type; // 0 = no link, 1 = internal link, 2 = external link
	public $link_internal;
	public $link_external;
	public $link_target_blank;
	public $background_color;
	
	public $date_start;
	public $date_end;
	
	protected static $_modelClass = "Diaporama_Model_DbTable_Image";
	protected static $_model;
	
	public function getLink() {
		if ($this->link_type == 1) {
			$page = CMS_Page_Object::get((int)$this->link_internal);
			return ($page) ? BASE_URL . $page->getUrl() : "#";
		}
		else if ($this->link_type == 2) {
			return $this->link_external;
		}
		
		return null;
	}
	
	public function isActive()
	{
		if (empty($this->date_start) && empty($this->date_end))
			return true;
		
		$date_start = null;
		if (!empty($this->date_start))
			$date_start = new DateTime($this->date_start);
		
		$date_end = null;
		if (!empty($this->date_end))
			$date_end = new DateTime($this->date_end);
		
		$now = new DateTime();
		
		if (!empty($this->date_start) && !empty($this->date_end)) {
			if ($date_start < $now && $now < $date_end)
				return true;
			return false;
		}
		
		if (!empty($this->date_start) && empty($this->date_end)) {
			if ($date_start < $now)
				return true;
			return false;
		}
		
		if (empty($this->date_start) && !empty($this->date_end)) {
			if ($now < $date_end)
				return true;
			return false;
		}
	}
}