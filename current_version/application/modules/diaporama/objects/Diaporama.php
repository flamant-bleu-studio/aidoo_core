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

class Diaporama_Object_Diaporama extends CMS_Object_MonoLangEntityWithNodes
{
	public $id;
	public $title;
	public $size;
	
	protected $nodes;
	
	protected static $_modelClass = "Diaporama_Model_DbTable_Diaporama";
	protected static $_model;
	
	protected static $_nodes = array(
		"nodes" => "Diaporama_Object_Image"
	);
	
	public function existByName($image)
	{
		$nodes = parent::__get('nodes');
		
		if (!empty($nodes)) {
			foreach ($nodes as $i) {
				if ($image === $i->image)
					return $i;
			}
		}
		
		return false;
	}
}