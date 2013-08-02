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

class Bloc_Menu_Menu extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $idMenu;
	public $idFolder;
	public $levelDisplay;
	public $displayOnlyFolder;
	public $align;
	public $desactiveDeroulant;
	public $separator;
	
	static $_align = array("0" => "Horizontal", "1" => "Vertical");
	
	protected $_adminFormClass = "Bloc_Menu_AdminForm";
	protected $cacheLifeTime   = 0;
	
	private $_activeID = array();
	
	protected static $_translatableFields = array();
	
	public function beforeRuntimeAdmin()
	{
		$menus = Menu_Object_Menu::getAllMenuID();
		
		if (empty($menus)) {
			_error(_('No instance of menu'));
			$this->_helperRedirector->gotoUrl($this->_helperRoute->short('index'));
		}
	}
	
	public function runtimeAdmin($view)
	{
		$view->idMenu = $this->idMenu;
		$view->idFolder = $this->idFolder;
	}
	
	public function runtimeFront($view)
	{
		$menu = new Menu_Object_Menu($this->idMenu);
		$menu->disableInactive();
		$menu->disableNoAccessItems();
		$menu->disableEmptyFolder();
		
		if( $this->idFolder != 0) {
			$menu->setRootFolder($this->idFolder);
		}
		
		$menu->generate();
				
		$view->items = $menu->items;
		$view->levelDisplay = $this->levelDisplay;
		$view->displayOnlyFolder = $this->displayOnlyFolder;
		
		$view->idMenu = $this->idMenu;
		
		$view->align = $this->align;
		$view->desactiveDeroulant = $this->desactiveDeroulant;
		$view->separator = $this->separator;
		
		$view->ref_folders_id = Menu_Object_Item::$TYPE_FOLDER;
		
		$view->activePageId = $this->hasChildrenActive($menu->items);
		
	}
	
	public function hasChildrenActive($items) {
		
		$activeID 	= array();
		$currentID 	= CMS_Page_Current::getInstance()->id_page;

		if(!empty($items)){
			$it = new RecursiveArrayIterator($items);
			
			foreach($it as $item){
				if( ($item->type == Menu_Object_Item::$TYPE_PAGE || $item->type == Menu_Object_Item::$TYPE_FOLDER_PAGE) && $currentID == $item->link ) {
					$activeID[] = $item->id_menu;
				}
				else if( in_array($item->type, Menu_Object_Item::$TYPE_FOLDER) ){
					
					if( count($item->children) > 0 ){
						
						$tmp = $this->hasChildrenActive($item->children);
						
						if(!empty($tmp)){
							$tmp[] = $item->id_menu;
							$activeID = array_merge($tmp, $activeID);
						}
					}
				}
			}
		}
		
		return $activeID;
	}
	
	public function save($post)
	{
		$this->idMenu = $post["idMenu"];
		$this->idFolder = $post["folder_menu_".$this->idMenu];
		$this->levelDisplay = $post["levelDisplay"];
		$this->displayOnlyFolder = $post["displayOnlyFolder"];
		$this->align = $post["align"];
		$this->desactiveDeroulant = $post["desactiveDeroulant"];
		$this->separator = $post["separator"];
		
		$id = parent::save($post);
		
		return $id;
	}	
}
