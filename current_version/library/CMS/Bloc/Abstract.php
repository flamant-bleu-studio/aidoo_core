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

abstract class CMS_Bloc_Abstract extends CMS_Object_MultiLangEntity {
	
	public $id_item;
	
	public $designation;
	public $title;
	public $classCss;
	public $theme;
	public $decorator;
	public $templateFront;
	
	protected $type;
	protected $noRenderBloc  = false; /* true : Désactive le render front du bloc */
	protected $cacheLifeTime = 3600; 

	private $nameBloc;
	
	protected static $_model;
	protected static $_modelClass = "Blocs_Model_DbTable_Items";
	
	public function __construct($data = null, $id_lang = CURRENT_LANG_ID){
		
		// Surcharge du constructeur pour vérifier l'implémentation de l'interface
		if (!$this instanceof CMS_Bloc_Interface) 
    		throw new Zend_Exception("Le bloc de class ".get_class($this)." doit implémenter l'interface 'CMS_Bloc_Interface'");
    	
		parent::__construct($data, $id_lang);
	}

	/**
	 * Retourne une instance d'un bloc avec résolution de class à la volée
	 * 
	 * @param int $id ID du bloc
	 * @param int|null $id_lang ID de la langue (langue courante par défaut)
	 */
	public static function getBlocInstance($id, $id_lang = CURRENT_LANG_ID)
	{
		self::_getModel();
		
		$row = self::$_model->getOne((int)$id, (($id_lang != "all") ? $id_lang : null), true);
		
		return ($row) ? new $row["type"]($row, $id_lang) : null;
	}
	
	public static function get($where = array(), $order = null, $limit = null, $id_lang = CURRENT_LANG_ID)
	{
		self::_getModel();
		
		if($id_lang == "all")
			$id_lang = null;
			
		$data_objects = static::$_model->get($where, $order, $limit, $id_lang);

		if($data_objects)
		{
			$return = array();
			foreach ($data_objects as $data_object)
			{
				$return[$data_object["id_item"]] = new $data_object["type"]($data_object, $id_lang);
			}
			return $return;
		}
		else
			return null;
	}
	
	public function save($post)
	{
		if( !$post["title"] )
			throw new Zend_Exception("Le titre du bloc n'est pas définit au moment du save.");
		
		$this->designation 	= $post["designation"];
		$this->title 		= $post["title"];
		$this->classCss 	= $post["classCss"];
		$this->decorator 	= $post["decorator"];
		$this->theme 		= $post["theme"];
		$this->type			= get_class($this);
		
		if( empty($this->templateFront) )
			$this->templateFront = "front";
			
		return parent::save();
	}
	
	protected function _insert()
	{
		$datas = $this->toArrayChildJsonEncoded();
		
		$return = static::$_model->insert($datas);
		
		if( $return ) {
			$this->setPrimaryKey($return);
			$this->generateSearch();
		}
		
		return $return;
	}
	
	protected function _update()
	{
		$datas = $this->toArrayChildJsonEncoded();
		
		$return = static::$_model->update($datas, $this->getPrimaryKey());
		
		if ($return)
			$this->generateSearch();
		
		return $this->getPrimaryKey();
	}
	
	public function delete()
	{		
		$return = self::$_model->deleteEntity($this->id);
		return ($return == 1) ? true : false;
	}
	

	protected function getAbstractProperties(){
		$propertyArray = array();
		$class = new Zend_Reflection_Class(__CLASS__);
		$properties = $class->getProperties();
		foreach ($properties as $property)
		{
			if ($property->isPublic())
				$propertyArray[] = $property->getName();
		}
		return $propertyArray;
	}
	
	/**
	 * retourne un tableau associatif des attributs de la classe parente
	 * @return array valeurs de la classe parente
	 */
	public function getParentArray(){
		
		$datas = parent::toArray();
		
		$properties = $this->getAbstractProperties();
		
		return array_intersect_key($datas, array_flip($properties));
	}
	

	/**
	 * retourne un tableau associatif des attributs de la classe fille
	 * @return array valeurs de la classe fille
	 */
	public function getChildArray(){
		$datas = parent::toArray();
		
		$properties = $this->getAbstractProperties();

		return array_diff_key($datas, array_flip($properties));
	}
	

	/**
	 * Retourne l'objet sous forme de tableau
	 */
	public function toArray()
	{
		return array_merge($this->getParentArray(), $this->getChildArray());
	}
	
	
	/**
	 * Retourne l'objet sous forme de tableau avec les valeurs de l'enfant json_encodées
	 * @todo A transformer en GetDatasToSave
	 */
	public function toArrayChildJsonEncoded()
	{
		$datas = $this->getParentArray();
		$datas["type"] = get_class($this);
		
		$childArray = $this->getChildArray();
		
		$config = CMS_Application_Config::getInstance();
		$langs = json_decode($config->get("availableFrontLang"), true);
			
		/*
		 * Mise en tableau par langue distincte des valeurs de la classe fille, puis encodage JSON
		 */		
		foreach($langs as $id => $code){
			
			$datas["params"][$id] = array();
			foreach($childArray as $name => $val){
				
				if(in_array($name, static::$_translatableFields)){
					$datas["params"][$id][$name] = $val[$id];
				}else {
					$datas["params"][$id][$name] = $val;
				}
				
			}
			$datas["params"][$id] = json_encode($datas["params"][$id]);
		}
		
		return $datas;
	}
	
	public function getType(){
		return $this->type;	
	}
	
	public function getAdminForm(){
		
		if($class = $this->_adminFormClass)
			return new $class($this->getDecorators());
		else
			throw new Zend_Exception("Il n'y a pas de class déclarée pour le form admin");
		
	}
	
	/**
	 * @todo A transformer en LoadFromBDD
	 */
	public function fromArray(array $propertyLst)
    {
    	// On ne set jamais l'ID avec le fromArray car public
        unset($propertyLst[static::$_model->getPrimaryKeyName()]);
    	
    	foreach ($propertyLst as $property => $value)
    	{
    		if(property_exists($this, $property)){
    			
    			// Si objet chargé en simple langue
    			if($this->_id_lang && is_array($value))
    				$this->$property = $value[$this->_id_lang];
    			else
    				$this->$property = $value;
    		}
    	}
    
    	/*
    	 * Le traitement ci-dessous récupère les champs json_encodés 
		 * des blocs et remplit les attributs correspondants
       	 */
    	
    	// Si récupération en simple langue
    	if($this->_id_lang){
   			$params = json_decode($propertyLst["params"][$this->_id_lang], true);

   			if(is_array($params) && !empty($params)) {
	    	    foreach ($params as $property => $value)
		    	{
		    		if(property_exists($this, $property)){
		    			// Si l'objet n'est chargé qu'en une seule langue
		    			if(in_array($property, static::$_translatableFields) && $this->_id_lang && is_array($value))
		    				$this->$property = $value[$this->_id_lang];
		    			else
		    				$this->$property = $value;
		    		}
		    	}
   			}
    	}
    	// Si récupération en multi langue
   		else {
   			
   			$params = array();
   			foreach($propertyLst["params"] as $id_lang => $param){
   				
   				$param = json_decode($param, true);
   				
	   			if( is_array($param) && !empty($param) ) {
					foreach ($param as $name => $value) {
						
						if(in_array($name, static::$_translatableFields)){
							if(empty($this->$name))
								$this->$name = array($id_lang => $value);
							else
								$this->$name = $this->$name + array($id_lang => $value);
						}
						else {
							$this->$name = $value;
						}
					
					}
				}
   			}
   		}
    }
	
	
	protected function getNameBloc() {
		if( $this->nameBloc )
			return $this->nameBloc;
		else {
			$this->nameBloc = lcfirst(strstr(substr(get_called_class(), 5), '_', true));
			return $this->nameBloc;
		}
	}
	

	final public function renderAdmin() {
		$view = Zend_Layout::getMvcInstance()->getView();
		
		if( method_exists(get_called_class(), "runtimeAdmin") )
			$this->runtimeAdmin($view);
		
		if ( !file_exists(APPLICATION_PATH . '/blocs/' . $this->getNameBloc() . '/' . 'admin.tpl') )
			throw new Zend_Exception(_t("Template admin don't exist to this bloc"));
		
		$view->addScriptPath(APPLICATION_PATH . '/blocs/' . $this->getNameBloc() . '/');		
		return $view->render('admin.tpl');
	}
	
	final public function renderFront()
	{		

		if( $this->noRenderBloc !== false)
			return "";

		/* Instance Smarty */
		$view = Zend_Layout::getMvcInstance()->getView();
		$smarty = $view->getEngine();
		
		
		/* Variables de vue */
		$view->id			= $this->id_item;
		$view->titleBloc 	= $this->title;
		$view->classCssBloc = $this->classCss . (($this->theme) ? ' ' . $this->theme : '');;
		$view->nameBloc 	= $this->getNameBloc();
		
		/* Réglage du cache */
		$smarty->cache_id   	= 'bloc-' . $this->id_item . '-' . CURRENT_LANG_CODE;
		$smarty->compile_id 	= 'bloc-' . $this->id_item . '-' . CURRENT_LANG_CODE;
		$smarty->cache_lifetime	= (int) $this->cacheLifeTime;
		
		// Le cache du bloc est disponible ?
		$isCached = $smarty->isCached(PUBLIC_PATH . '/skins/'.SKIN_NAME.'/core_features/tpls_override/blocs/decorators/' .$this->decorator. '.tpl');
		
		// Appel du runtime PHP uniquement si cache désactivé ou expiré
		if ($this->cacheLifeTime === 0 || !$isCached) {
			$this->runtimeFront($view);	
		}
		
		// Définition du chemin des vues et surcharge + initialisation
		$path = APPLICATION_PATH . '/blocs/' . $this->getNameBloc() . '/';
		$override = PUBLIC_PATH . '/skins/'.SKIN_NAME.'/core_features/tpls_override/blocs/templates/'.$this->getNameBloc().'/';
		$view->initViewAndOverride($path, $override, $this->templateFront);
			
		// render HTML placé dans une variable de vue
		$view->contentBloc = $view->renderByViewName($this->templateFront);
			
		// Définition du chemin des décorateurs et surcharge + initialisation
		$path = PUBLIC_PATH . '/skins/'.SKIN_NAME.'/core_features/tpls_override/blocs/decorators/';
		$override = PUBLIC_PATH . '/skins/'.SKIN_NAME.'/core_features/tpls_override/blocs/decorators/'.$this->getNameBloc().'/';
		$view->initViewAndOverride($path, $override, $this->decorator);
		
		// render HTML du bloc entier
		$html = $view->renderByViewName($this->decorator);
		
		$smarty->cache_lifetime = 3600;
		
		return $html;

	}
	
	final protected function getDecorators()
	{		
		/** Name Skin Front **/
		$config = CMS_Application_Config::getInstance();
		$skinFront = $config->get("skinfront");
		
		$decorators = array();
		
		/** Decorators général **/
		if ( file_exists(PUBLIC_PATH . '/skins/'.$skinFront.'/core_features/tpls_override/blocs/decorators/') ) {
			
			$filesDirectoryDecorators = new DirectoryIterator(PUBLIC_PATH . '/skins/'.$skinFront.'/core_features/tpls_override/blocs/decorators/');
			
			foreach ($filesDirectoryDecorators as $file) {
				if ( !$file->isDir() ) {
					$name = substr($file->getFileName(), 0, strlen($file->getFileName())-4);
					$decorators["general"][$name] = $name;
				}
			}
		}
		
		/** Decorators du bloc **/
		if ( file_exists(PUBLIC_PATH . '/skins/'.$skinFront.'/core_features/tpls_override/blocs/decorators/'.$this->getNameBloc().'/') ) {
			
			$filesDirectoryDecorators = new DirectoryIterator(PUBLIC_PATH . '/skins/'.$skinFront.'/core_features/tpls_override/blocs/decorators/'.$this->getNameBloc().'/');
			
			foreach ($filesDirectoryDecorators as $file) {
				if ( !$file->isDir() ) {
					$name = substr($file->getFileName(), 0, strlen($file->getFileName())-4);
					$decorators["bloc"][$name] = $name;
				}
			}
		}
		
		return $decorators;
	}
	
	public function generateSearch()
	{
		$datas = array();
		
		$datas['item_id'] 	= 'Bloc-' . $this->getPrimaryKey();
		$datas['type'] 		= 'Bloc';
		$datas['datas']['typeName'] 	= 'Bloc';
		
		$datas['content'][] = $this->title;
		
		if (!empty(static::$_searchableFields)) {
			foreach (static::$_searchableFields as $field) {
				$datas['content'][] = $this->{$field};
			}
		}
		
		$datas['datas']['title'] = $this->designation;
		$datas['datas']['isVisible'] = 0;
		
		$datas['datas']['picture']			= null;
		$datas['datas']['picture_folder']	= null;
		
		$datas['url_front'] = null;
		$datas['url_back'] = array("route" => "blocs_back", "params" => array("module" => "blocs", "controller" => "back", "action" => "edit", "id" => $this->getPrimaryKey()));
		
		CMS_Search_Back::getInstance()->addItem($datas);
	}
}
