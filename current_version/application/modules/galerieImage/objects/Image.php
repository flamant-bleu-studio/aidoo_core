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

class GalerieImage_Object_Image extends CMS_Object_MonoLangEntity {
	
	public $id;
	public $parent_id;
	public $description;
	public $isPermanent;
	public $date_start;
	public $date_end;
	public $path;
	public $path_thumb;
	public $path2;
	public $path_thumb2;
	public $bg_color;
	public $datas;
	
	protected static $_modelClass = "GalerieImage_Model_DbTable_Image";
	protected static $_model;
	
}