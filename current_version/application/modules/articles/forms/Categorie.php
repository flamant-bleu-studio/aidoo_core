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

class Articles_Form_Categorie extends CMS_Form_Default
{
	public function init()
	{
		$categories = Articles_Object_Categorie::get();
		
		$cats = array(0 => _t('None'));
		if($categories) {
			foreach ($categories as $categorie) {
				$cats[$categorie->id_categorie] = $categorie->title;
			}
		}
		
		$item = new Zend_Form_Element_Select('parent');
		$item->setLabel(_t('Parent category'));
		$item->addMultiOptions($cats);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('title');
		$item->setLabel(_t('Title'));
		$item->setRequired(true);
		$item->setTranslatable(true);
		$this->addElement($item);
		
		/*-- image --*/
		$item = new CMS_Form_Element_MultiUpload('image');
		$item->setMaxNumberOfFiles(1);
		$item->setAutoUpload(true);
		$item->setAllowedExtensions(array("jpg", "jpeg", "png", "gif", "bmp"));
		$item->setUploadPath("articles");
		$item->setLabel(_t("Image"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_TinyMCE("description");
		$item->setLabel(_t("Description"));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('countByPage');
		$item->setLabel(_t('Number of article per page'));
		$item->setRequired(true);
		$item->setValue("5");
		$this->addElement($item);
		
		/* Type de visualisation */
		$typeView[null] = 'Liste';
		$typeView[1] = 'Mosaique';
		
		$item = new Zend_Form_Element_Select('typeView');
		$item->setLabel(_t('Type de visualisation'));
		$item->addMultiOptions($typeView);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('fb_comments_number_show');
		$item->setLabel(_t('Show number comment'));
		$item->setDescription(_t('Display number comment to all articles'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Save'));
		$item->setLabel(_t('Save'));
		$this->addElement($item);
	}
}