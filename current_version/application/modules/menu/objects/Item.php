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

class Menu_Object_Item extends CMS_Object_MultiLangEntity
{
	/** Attributs item **/
	public $id_menu;
	
	public $menu_id;
	public $type;
	public $link;
	public $label;
	public $subtitle;
	public $image;
	public $hidetitle;
	public $access;
	public $active;
	public $tblank;
	public $cssClass;
	public $loadAjax;
	public $lft;
	public $rgt;
	public $level;
	public $parent_id;
	
	public $children = array();
	
	protected $_id_lang;
	private $_processLink;
	
	/**
	 * Type of item
	 */	
	public static $TYPE_SYSTEM					= 0; // ROOT (link=null)
	public static $TYPE_PAGE 					= 1; // Page du CMS
	public static $TYPE_EXTERNAL_LINK 			= 2; // Lien externe
	public static $TYPE_NO_LINK					= 3; // Texte sans lien
	
	public static $TYPE_FOLDER_NO_LINK 			= 4; // Dossier sans lien (link=null)
	public static $TYPE_FOLDER_PAGE 			= 5; // Dossier avec lien vers page du CMS
	public static $TYPE_FOLDER_EXTERNAL 		= 6; // Dossier avec lien externe
	public static $TYPE_FOLDER_LINK_CHILDREN	= 7; // Dossier avec lien du premier enfant (link=null)
	
	public static $TYPE_FOLDER = array( 4, 5, 6, 7 ); // All type of folder
	
	protected static $_model;
	protected static $_modelClass = "Menu_Model_DbTable_Menu";
	
	public function getLink() {
		
		if(!isset($this->_processLink)){
			switch ((int)$this->type) {
				
				case self::$TYPE_PAGE:
				case self::$TYPE_FOLDER_PAGE:
					if(!(int)$this->link){
						$this->_processLink = "#";
						break;
					}
	
					$page = CMS_Page_Object::get((int)$this->link);
					$this->_processLink = ($page) ? BASE_URL . $page->getUrl() : "#" ;
					break;
					
				case self::$TYPE_EXTERNAL_LINK:
				case self::$TYPE_FOLDER_EXTERNAL:
					$this->_processLink = $this->link;
					break;
					
				case self::$TYPE_FOLDER_LINK_CHILDREN:
					if(!$this->_processLink = $this->getFirstUrlChildren($this->children))
						$this->_processLink =  "#";
					break;
					
				default:
					$this->_processLink = "#";
					break;
			}
		}
		
		return $this->_processLink;
	}
	
	private function getFirstUrlChildren($items) {
		
    	$it = new RecursiveArrayIterator($items);
		
		foreach($it as $item) {
						
			// SI c'est un dossier
			if( in_array($item->type, self::$TYPE_FOLDER) ) {
				
				// On regarde si il y a un lien à faire remonter
				if($return = self::getFirstUrlChildren($item->children))
					return $return;
				else 
					continue;
			}
			
			// SI on trouve un lien
			if($item->link)
				return $item->getLink();
		}
		
		return null;
	}
	
    
    /**
     * Delete this item
     */    
    public function delete()
    {
    	if( !$this->id_menu )
			throw new Zend_Exception("Param id not found");
		
    	$model = new Menu_Model_DbTable_Menu();
    	$model->deleteItem($this->id_menu);
    }
    
	public function isEditableContent()
    {
    	if( !$this->id_menu )
			throw new Zend_Exception("Param id not found");
		
		if($this->type != self::$TYPE_PAGE)
			return false;
			
    	$page = CMS_Page_Object::get((int)$this->link);

    	if($page->api && $page->content_id)
    		return true;
    	else
    		return false;
    }
    
	public function isDeletableContent()
    {
    	if( !$this->id_menu )
			throw new Zend_Exception("Param id not found");
		
		if($this->type != self::$TYPE_PAGE)
			return false;
		
    	$page = CMS_Page_Object::get((int)$this->link);

    	if($page->api && $page->content_id)
    		return true;
    	else
    		return false;
    }
	
	protected function _insert() {
		
		$datas = $this->toArray();
		
		unset($datas["level"]);
		unset($datas["children"]);

		$return = self::$_model->addItem($datas);
		
		if( $return )
			$this->setPrimaryKey($return);
		
		return $return;
	}
	
	protected function _update()
	{
		$datas = $this->toArray();
		
		unset($datas["level"]);
		unset($datas["parent_id"]);
		unset($datas["children"]);
		
		self::$_model->update($datas, $this->getPrimaryKey(), false);
		
		return $this->getPrimaryKey();
	}
	
	
	/**
	 * Enable OR Disable item
	 * @param int $id
	 * @param bool $enable (true = enable, false = disable)
	 */
	public function active($enable = true)
	{
		if(empty($this->id_menu))
			throw new Zend_Exception(_t("ID invalid"));
		
		$model = new Menu_Model_DbTable_Menu();
		$model->active($this->id_menu, $enable);
	}
	
}