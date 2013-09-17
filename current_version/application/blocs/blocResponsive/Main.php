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

class Bloc_BlocResponsive_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface
{
	public $background_color;
	public $text;
	public $background_text;
	public $text_color;
	public $icon;
	public $background_image;
	
	public $link_type;
	public $link_internal;
	public $link_external;
	public $link_target_blank;
	public $load_ajax;
	
	protected $_adminFormClass = "Bloc_BlocResponsive_AdminForm";
	
	protected static $_translatableFields = array();
	protected static $_searchableFields = array();
	
	public function runtimeFront($view)
	{
		$view->background_color = !empty($this->background_color) ? $this->background_color : null;
		$view->text = !empty($this->text) ? $this->text : null;
		$view->text_color = $this->text_color;
		$view->icon = !empty($this->icon) ? $this->icon : null;
		$view->background_image = !empty($this->background_image) ? $this->background_image : null;
		$view->background_text = !empty($this->background_text) ? $this->hex2rgba('#'.$this->background_text, 0.7) : null;
		
		$view->link_target_blank = $this->link_target_blank;
		$view->load_ajax = $this->load_ajax;
		
		if ($this->link_type != 0 && ($this->link_type == 1 || $this->link_type == 2)) {
			if ($this->link_type == 1) {
				$page = new CMS_Page_Object((int)$this->link_internal);
				$view->url = $page->getUrl();
			}
			else if ($this->link_type == 2) {
				$view->url = $this->link_external;
			}
		}
		else {
			$view->url = null;
		}
	}
	
	public function save($post)
	{
		$this->background_color = $post['background_color'];
		$this->text = $post['text'];
		$this->text_color = $post['text_color'];
		$this->icon = $post['icon'];
		$this->background_image = reset($post['background_image']);
		$this->background_text = $post['background_text'];
		
		$this->link_type = $post['link_type'];
		$this->link_internal = $post['link_internal'];
		$this->link_external = $post['link_external'];
		$this->link_target_blank = $post['link_target_blank'];
		
		$this->load_ajax = $post['load_ajax'];
		
		$id = parent::save($post);
		
		return $id;
	}
	
	private function hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
          return $default; 

	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
	}
}