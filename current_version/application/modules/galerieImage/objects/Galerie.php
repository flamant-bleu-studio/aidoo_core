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

class GalerieImage_Object_Galerie extends CMS_Object_MonoLangEntityWithNodes
{
	public $id;
	public $title;
	public $type;
	public $nb_image;
	public $access;
	public $bg_color;
	public $size;
	public $style;
	public $transition;
	public $controls_position;
	public $controls_style;
	public $autostart;
	
	protected $nodes;
	
	const TYPE_GALERIE = 1;
	const TYPE_DIAPORAMA = 2;
	
	protected static $_modelClass = "GalerieImage_Model_DbTable_Galerie";
	protected static $_model;
	
	protected static $_nodes = array(
		"nodes" => "GalerieImage_Object_Image"
	);


}