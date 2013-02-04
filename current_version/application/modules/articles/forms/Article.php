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

class Articles_Form_Article extends CMS_Form_Default {
	
	public function init() {
		
		/**
		 * Status
		 */
		$item = new Zend_Form_Element_Select('status');
		$item->setLabel(_t('Status'));
		$item->addMultiOptions(array('0'=>_t('Drafted'),'1'=>_t('Published')));
		$this->addElement($item);
		
		/**
		 * Parent Category
		 * 
		 * Traduction : _t("Choose")
		 */
		$cats = Articles_Object_Categorie::get();
		$backAcl = CMS_Acl_Back::getInstance();
		
		// Tri des catégories en fonction de leur mère
		
		$temp = array();
		if( $cats ) {
			foreach ($cats as &$cat) {
				if(!$cat->parent_id )
					$temp[_t('Others')][$cat->id_categorie] = $cat->title;
				else 
					$temp[(int)$cat->parent_id][$cat->id_categorie] = $cat->title;
			}
		}
		
		foreach($temp as $id => $cat) {
			// Si la clé est un entier, alors ce n'est pas "Autres" !
			if(is_int($id)){
				// Remplacement de la clé entière par le nom de cette catégorie
				$temp[$temp[_t('Others')][$id]] = $temp[$id];
				unset($temp[$id]);
			}
		}
		
		$item = new CMS_Form_Element_AdvancedMultiSelect('category');
		$item->setLabel(_t('Categories'));
		$item->setRequired(true);
		$item->addMultiOptions($temp);
		$this->addElement($item);
		
		
		/**
		 * View Access
		 */
		$item = new CMS_Acl_Form_ElementViewAccessSelect("access");
		$item->setLabel(_t('Access'));
		$item->setRequired(true);
		$this->addElement($item);
		
		/**
		 * Dates
		 */
		$item = new CMS_Form_Element_DatePicker('date_start');
		$item->setLabel(_t('Activation date'));
		$item->setAttrib('size',20);
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('isPermanent');
		$item->setLabel(_t("Article permanent ?"));
		$item->setDescription(_t("Check to activate"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_DatePicker('date_end');
		$item->setLabel(_t('Deactivation date'));
		$item->setAttrib('size',20);
		$this->addElement($item);
		
		/**
		 * Content Title
		 */
		$item = new CMS_Form_Element_Text('title');
		$item->setLabel(_t('Title'));
		$item->setRequired(true);
		$item->setTranslatable(true);
		$item->setAttrib('size',40);
		$this->addElement($item);

		/*-- image --*/
		$item = new CMS_Form_Element_MultiUpload('image');
		$item->setMaxNumberOfFiles(1);
		$item->setAutoUpload(true);
		$item->setAllowedExtensions(array("jpg", "jpeg", "png", "gif", "bmp"));
		$item->setUploadPath("articles");
		$item->setLabel(_t("Image"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Textarea('chapeau');
		$item->setLabel(_t("Headline"));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('readmore');
		$item->setAttrib('id', 'readmore');
		$item->setLabel(_t("Link read more"));
		$item->setDescription(_t("Check to activate"));
		$this->addElement($item);
		
		/**
		 * Facebook comments
		 */
		$item = new Zend_Form_Element_Checkbox('fb_comments_active');
		$item->setLabel(_t('Show comments'));
		$item->setDescription(_t("Display facebook comments in article"));
		$this->addElement($item);
		
	}
}
