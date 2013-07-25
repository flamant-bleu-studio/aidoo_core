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

class Menu_Object_Menu extends CMS_Object_MultiLangEntity
{
	/** Attributs menu **/
	public $id_menu;
	
	public $menu_id;
	public $type;
	public $link;
	public $label;
	public $subtitle;
	public $access;
	public $active;
	public $cssClass;
	public $lft;
	public $rgt;
	
	/**
	 * Array of multiple Menu_Object_Item
	 * /!\ Load after call generate method
	 * @var array
	 */
	public $items;
	
	/**
	 * Original array return by model
	 * Array of multiple Menu_Object_Item
	 * /!\ Load after call generate method
	 * @var array
	 */
	private $originalItems;
	
	private $_isGenerate;
	
	/** Params render menu **/
	private $_disableItems			= false; // Ne pas charger l'attribut 'items' (lazy-loading), seulement le menu (objet courant)
	private $_disableInactive 		= false; // Ne pas récupérer les items désactivé
	private $_disableRenderNested 	= false; // Ne pas faire de rendu du type nested tree
	private $_disableEmptyFolder 	= false; // Ne pas récupérer les dossiers vides
	private $_disableDisplayItems	= false; // Ne pas afficher les items, seulemnt les dossiers
	private $_disableNoAccessItems  = false; // Ne pas récupérer les items n'étant pas accessible par l'utilisateur courant
	
	private $_rootFolder; // Id présent si le root devient un dossier
	
	protected $_id_lang;
	
	protected static $_model;
	protected static $_modelClass = "Menu_Model_DbTable_Menu";
	
	/**
	 * Create new object menu
	 * @param int $id
	 * @throws Zend_Exception
	 */
	public function __construct($id = null, $id_lang = CURRENT_LANG_ID)
	{
		self::_getModel();
		
		if($id != null){
			$id = (int)$id;
			
			$this->_id_lang = ($id_lang != "all") ? $id_lang : null ;
			
			if(!$id)
				throw new Zend_Exception(_t("ID invalid"));
			
			$this->id_menu  = $id;
		}
	}
	
	public static function getAllMenuID(){
		self::_getModel();
		
		return self::$_model->getMenus();
	}
	
	/**
	 * Disable load attribut items
	 */
	public function disableItems() {
		$this->_disableItems = true;
		$this->_isGenerate = false;
	}
	
	/**
	 * Disable recovery inactive items
	 */
	public function disableInactive() {
		$this->_disableInactive = true;
		$this->_isGenerate = false;
	}
	
	/**
	 * Disable render on nested tree
	 */
	public function disableRenderNested() {
		$this->_disableRenderNested = true;
		$this->_isGenerate = false;
	}
	
	/**
	 * Disable display folder empty (no children)
	 */
	public function disableEmptyFolder() {
		$this->_disableEmptyFolder = true;
		$this->_isGenerate = false;
	}
	
	/**
	 * Disable display items (display only folder)
	 */
	public function disableDisplayItems() {
		$this->_disableDisplayItems = true;
		$this->_isGenerate = false;
	}
	
	/**
	 * Disable display items of current user don't have access
	 */
	public function disableNoAccessItems() {
		$this->_disableNoAccessItems = true;
		$this->_isGenerate = false;
	}
	
	/**
	 * Define the root of menu is a folder
	 * @var int $id_folder
	 */
	public function setRootFolder($id_folder) {
		$this->_rootFolder = (int)$id_folder;
	}
	
	/**
	 * Generate list of items with params of class
	 * Load attribut 'items'
	 */
	public function generate()
	{
		if( $this->_isGenerate ) // Already generate for this params
			return;
			
		self::_getModel();
				
		if( !$this->_disableItems ) // Charger les items, pas seulement le menu
		{
			if( empty($this->_rootFolder) ){
				$items = self::$_model->getItemsByMenu($this->id_menu);
			}
			else
				$items = self::$_model->getItemsByFolder($this->id_menu, $this->_rootFolder);
			
			$this->originalItems = $items;
			
			if(is_array($items) && !empty($items)){
				$this->fromArray(reset($items)); // Load values params menu
				unset($items[key($items)]);
			}
			
			$array = array();
			
			if( $items )
				foreach ($items as $item)
					$array[] = new Menu_Object_Item($item);
			
			if( !$this->_disableRenderNested ) {
				$array = $this->nestedTreeArray($array);
			}
			
			if( $this->_disableEmptyFolder ) {
				$array = $this->deleteEmptyFolder($array);
			}
			
			$this->items = $array;
			$this->_isGenerate = true;
		}
		else // charger seulement les éléments du menu (pas ses items)
		{
			$datas = self::$_model->getOne((int)$this->id_menu);
			
			$this->originalItems = $datas;
			$this->fromArray( $datas );
		}
		
		return $this;
	}
	
	private function nestedTreeArray($items)
	{
		$frontAcl = CMS_Acl_Front::getInstance();
		
	 	$nested = array();
	    $depths = array();
	    
	    $inactiveFolders = array();
	    $isChildrenOfFolderInactive = false;
	    
	    foreach( $items as $key => $item ) {  
	    	
	    	// Si afficher seulement les dossier
	    	if( $this->_disableDisplayItems && !(in_array($item->type, Menu_Object_Item::$TYPE_FOLDER)) )
	    		continue;
	    	
	    	// Si ne pas afficher les éléments inactif
	    	if( $this->_disableInactive && !$item->active) {
	    		if( in_array($item->type, Menu_Object_Item::$TYPE_FOLDER) ) {
	    			$inactiveFolders[] = array(
	    				"lft" => $item->lft,
	    				"rgt" => $item->rgt
	    			);
	    		}
	    		
	        	continue;
	    	}
	    	
	    	/**
	    	 * Verifie si l'item courant appartient à un dossier inactif
	    	 */
	    	if ( count($inactiveFolders) > 0 ) {
		    	foreach ($inactiveFolders as $inactiveFolder) {
		    		
		    		if( $item->lft > $inactiveFolder["lft"] && $item->rgt < $inactiveFolder["rgt"] )
		    		{
		    			$isChildrenOfFolderInactive = true;
		    			break;
		    		}
		    	}
		    	
		    	// Si l'item appartient à un dossier inactif
		    	if (isset($isChildrenOfFolderInactive) && $isChildrenOfFolderInactive === true) {
		    		unset($isChildrenOfFolderInactive);
		    		continue;
		    	}
	    	}
	    	
	        // Si ne pas afficher les items non accessible par l'utilisateur courant (viewaccess)
	        if( $this->_disableNoAccessItems && !$frontAcl->hasPermission($item->access) )
	        	continue;
	        
	        /**
	         * Algo de création du array nested
	         */
	        if( ($item->level-1) == 0 ) {
	        	
	            $nested[$key] = $item;
	            $depths[($item->level-1) + 1] = $key;
	            
	        }
	        else {
	            $parent = &$nested;
	            
	            for( $i = 1; $i <= ( ($item->level-1) ); $i++ ) {

	            	if(($item->level-1) != $i)
	                	$parent = &$parent[$depths[$i]]->children;
	                else 
	                	$parent = &$parent[$depths[$i]];
	            }
	            
	            if (!empty($parent) && is_object($parent)) {
		            $parent->children[$key] = $item;
		            $depths[($item->level-1) + 1] = $key;
	            }
	        }
	    }
	    
	    return $nested;
	}
	
	private function deleteEmptyFolder($items)
	{
		foreach ($items as $key => $item)
		{
			if( in_array($item->type, Menu_Object_Item::$TYPE_FOLDER)) {
				
				if( count($item->children) > 0 ) {
					$items[$key]->children = $this->deleteEmptyFolder($item->children);
					
					if( count($item->children) == 0 ) {
						unset($items[$key]);
					}
					
				}
				else {
					unset($items[$key]);
				}
			}
		}
		
		return $items;
	}
	
	
	/**
	 * Load content menu
	 */
	public function loadContent($datas = array())
	{
		if( !empty($datas) )
			$this->fromArray($datas);
		else
		{
			if( !$this->id_menu )
				new Zend_Exception(_t("ID invalid"));
			
			$datas = self::$_model->getItem($this->id_menu);
			
			$this->fromArray($datas);
		}
	}
		
	protected function _insert() {

		$datas = array(
			"label" 	=> $this->label,
			"subtitle"  => $this->subtitle,
			"type"		=> $this->type
		);
		
		return self::$_model->addmenu($datas);
	}
	
	protected function _update()
	{
		$datas = array(
			"label" 	=> $this->label,
			"subtitle"  => $this->subtitle,
			"type"		=> $this->type
		);
		
		$id = self::$_model->updateItem($this->id_menu, $datas);
		
		return $this->id_menu;
	}
	
	public function delete()
	{
		if( !$this->id_menu )
			throw new Zend_Exception("Param id not found");
		
		self::$_model->deleteMenu($this->id_menu);
	}
}