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

class CMS_Page_PersistentObject extends CMS_Page_Object {

	/**
	 * Retourn un tableau de types contenant chacun une case "pages" avec toutes les pages affiliées
	 * @param array $filters filtre pour les pages
	 */
	public static function getAllPagesByType($filters = null){
		
		$pages = self::get($filters);
		$types = (array)CMS_Page_Type::get();

		// Génération d'un tableau associatif : type => array object
		$tmp = array();
		foreach($types as $type){
			$tmp[$type->type] = $type->toArray();
		}
		$types = $tmp;
			
		// Remplissage de chaque type avec leurs pages
		foreach($pages as $page) {
			if(!$types[$page->type])
				continue;
			else
				$types[$page->type]["pages"][] = $page;
		}

		// On vite les types n'ayant aucune page
		foreach($types as &$type){
			if(!isset($type["pages"]))
				unset($types[$type["type"]]);
		}
		
		return $types;
	} 
	

	public static function setDefaultTplByTplID($id){
		self::_getModel();
		self::$_model->setDefaultTplByTplID($id);
		parent::updateCache();
	}
	
	public function save() {
		// Ajout de la langue dans le rewrite + verification qu'il soit UNIQUE
		if (is_array($this->url_rewrite)){
			$langs = json_decode(CMS_Application_Config::getInstance()->get("availableFrontLang"), true); // langs de l'appli
			$sanitize = new CMS_Filter_Sanitize(); 
			foreach ($this->url_rewrite as $lang_id => $rewrite) {
				if (strlen($rewrite) <= 2) {
					unset($this->url_rewrite[$lang_id]);
				} elseif ($rewrite){
					// Ajout de la langue avant le rewrite si elle n'est pas celle de défaut et qu'elle ne contient pas deja le [lang]/ devant son url
					if ($lang_id != DEFAULT_LANG_ID && substr($this->url_rewrite[$lang_id], 0, 3) != $langs[$lang_id].'/' )
						$this->url_rewrite[$lang_id] = $rewrite = $langs[$lang_id] . '/' . $rewrite;
						
					// Boucle tant que le rewrite n'est PAS UNIQUE
					$i = 1;
					do {
						if ($i != 1)
							$rewrite = $this->url_rewrite[$lang_id].'-'.$i;

						$sameRewite = CMS_Page_PersistentObject::getOneFromDB( array('url_rewrite' => $rewrite, array('A.id_page != ?', $this->id_page)), null, null, "all" );
						$i++;
					} while ($sameRewite);
					
					// On réassigne le nouveau rewrite UNIQUE
					$this->url_rewrite[$lang_id] = $rewrite;
				}
			}
		}
		
		parent::save();
		parent::updateCache();
	}
	
	protected function _insert(){
		
		// Suppression du slash en début d'URL
		if ($this->url_system[0] == '/') {
		    $this->url_system = substr($this->url_system, 1);
		}
		
		// Défauts
		if(!isset($this->enable))
			$this->enable = 1;
		if(!isset($this->visible))
			$this->visible = 1;
		if(!isset($this->wildcard))
			$this->wildcard = 0;
			
		parent::_insert();
	}
	
	public function delete() {
		self::$_model->delete($this->id_page);
		
		parent::updateCache();
	}
		
	public function enable()
	{	
		self::$_model->enable($this->id_page);
		
		parent::updateCache();
	}
	
	public function disable()
	{
		self::$_model->disable($this->id_page);
		
		parent::updateCache();
	}
	
}