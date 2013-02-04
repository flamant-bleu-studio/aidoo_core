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

class Admin_Form_ThumbsConfig extends CMS_Form_Default {
	
	public function init() {
		
		
		$item = new Zend_Form_Element_Text("name");
        $item->setLabel(_t('Name'));
        $item->setRequired(true);
        $this->addElement($item);
        
		$item = new Zend_Form_Element_Radio('adaptiveResize');
    	$item->setLabel(_t('Adaptive resize'));
        $item->setDescription(_t("Force size (crop from center)"));
        $item->setMultiOptions(array( 
        	"0" => _t('No') , 
        	"1" => _t('yes')
        ));
       	$item->setRequired(true);
		$this->addElement($item);
        
        $item = new Zend_Form_Element_Text("width");
        $item->setLabel(_t('Width'));
        $item->setRequired(true);
        $this->addElement($item);
        
        $item = new Zend_Form_Element_Text("height");
        $item->setLabel(_t('Height'));
        $item->setRequired(true);
        $this->addElement($item);
        
	}
}