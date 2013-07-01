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

class Blocs_Object_Template extends CMS_Object_MonoLangEntity
{
	public $id_template;

	public $title;
	public $defaut;
	public $theme;
	public $classCss;
	
	public $bgType;
	public $bgColor1;
	public $bgColor2;
	public $bgGradient;
	public $bgPicture;
	public $bgRepeat;
	
	protected $items_position;
	
	protected static $_placeholdersAvailable = array("header1", "header2", "header3", "sideleft1", "sideright1", "contenttop", "contentleft", "contentmore", "contentright", "contentbottom", "footer1", "footer2", "footer3");
	
	protected static $_modelClass = "Blocs_Model_DbTable_Templates";
	protected static $_model;
	
	protected static $_modelMapClass = "Blocs_Model_DbTable_Map";
	protected static $_modelMap;
	
	private static $_types = array(1 => 'classic', 2 => 'mobile', 3 => 'tablet');
	
	/**
	 * Retourne un tableau d'ID de blocs
	 * @param string $placeholder (optionnel) nom du placeholder
	 */
	public function getItemsPosition() {

		if(!isset($this->items_position)){
			self::_getMapModel();
				
			$rows = self::$_modelMap->get(array("template_id" => $this->id_template), array("id_template_map"));
	
			if ( $rows ) {
				foreach($rows as $row){
					$this->items_position[self::$_types[$row['template_type']]][$row['placeholder']][] = $row['item_id'];
				}
			}
		}

		return $this->items_position;
	}
	
	public function getPlaceholder($template_type, $placeholder) {
	
		$pos = $this->getItemsPosition();
		
		if(isset($pos[$template_type][$placeholder]))
			return $pos[$template_type][$placeholder];
		else
			return null;
	}
	
	public function setItemsPosition($itemsPosition = null) {
		
		if(is_array($itemsPosition) && !empty($itemsPosition) ){
			
			// Tous les types du template
			foreach($itemsPosition as $type => $placeholders){
				
				if(!in_array($type, self::$_types))
					throw new Exception(_t("Invalid template type"));
				
				// Tous les placeholders du type
				foreach($placeholders as $ph => $idLst){
				
					if(!in_array($ph, self::$_placeholdersAvailable))
						throw new Exception(_t("Invalid placeholder name"));
						
					// Tous les blocs du placeholder
					foreach($idLst as $id_bloc){
						$id_bloc = (int)$id_bloc;
						
						if(!$id_bloc)
							throw new Zend_Exception(_t("Invalid block ID"));
					}
				}
			}
		}
		
		$this->items_position = $itemsPosition;
	}

	protected function _insert() {
		if(!isset($this->defaut))
			$this->defaut = 0;
			
		return parent::_insert();
	}
	protected function _update() {
		
		parent::_update();

		self::_getMapModel();
			
		$rows = self::$_modelMap->delete(array("template_id" => $this->id_template));
	
		if($this->items_position){
			
			$types = array_flip(self::$_types);
			
			foreach($this->items_position as $type => $placeholders){
				foreach($placeholders as $placeholder => $itemIdLst){
					
					if(is_array($itemIdLst) && !empty($itemIdLst)){
						foreach($itemIdLst as $itemId){
							
							self::$_modelMap->insert( array(
								'item_id' 		=> $itemId,
								'template_id' 	=> $this->id_template,
								'template_type' => $types[$type],
								'placeholder' 	=> $placeholder
							), false);
						}
					}
				}
			}
		}
		
		return $this->id;
	}

	public function importFromOtherTemplate($id){
		
		$other = new self($id);

		$this->theme 	= $other->theme;
		$this->classCss = $other->classCss;
		$this->defaut 	= 0;

		$this->setItemsPosition($other->getItemsPosition());
		
		$this->save();
	}
	
	public static function setDefault($id = null)
	{
		self::_getModel();
		
		return self::$_model->setDefault($id);
	}
	
	protected static function _getMapModel() {
		if (empty(static::$_modelMap) && class_exists(static::$_modelMapClass)) {
			static::$_modelMap = new static::$_modelMapClass();
			return;
		}
	}
	
}
