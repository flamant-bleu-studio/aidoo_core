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

class Articles_Object_Article extends CMS_Object_MultiLangEntityWithNodes
{
	public $id_article;
	public $type;
	public $template;

	public $isPermanent;
	public $author;
	public $status;
	public $isSubmitted;
	public $access;
	
	public $title;
	public $chapeau;
	public $readmore;
	
	public $image;
	
	public $date_add;
	public $date_upd;
	
	public $date_start;
	public $date_end;
	
	public $fb_comments_active;
	
	protected $categories;
	
	protected static $_modelMapClass = "Articles_Model_DbTable_Map";
	protected static $_modelMap;
	
	/*
	 * protected pour le LazyLoading
	 */
	protected $nodes;
	
	protected static $_model;
	protected static $_modelClass = "Articles_Model_DbTable_Articles";
	
	protected static $_nodes = array(
		"nodes" => array(
			"modelClass" 	=> "Articles_Model_DbTable_Nodes"
		)
	);
	
	const STATUS_PUBLISH = 1; 	// Publié
	const STATUS_DRAFT	 = 0;	// Brouillon
	
	public static function get($where = array(), $order = null, $limit = null, $id_lang = CURRENT_LANG_ID) {
		
		if(isset($where["categories"])){
			self::_getMapModel();
			
			
			
			if (is_array($where["categories"])){
				
				$cats = array(str_repeat(" id_categorie = ? OR", count($where["categories"])). " 0");
				foreach($where["categories"] as $map)
					$cats[] = $map;
				
				$mapLst = self::$_modelMap->get(array($cats));
			}
			else
				$mapLst = self::$_modelMap->get(array("id_categorie" => $where["categories"]));
			
			$ids = array();
			
			if(is_array($mapLst) && !empty($mapLst)){
				
				$ids[] = str_repeat(" A.id_article = ? OR", count($mapLst)). " 0";
				foreach($mapLst as $map){
					$ids[] = $map["id_article"];
				}
			
				unset($where["categories"]);
				$where["id_article"] = $ids;
			}
			else {
				return null;
			} 
			
		}
		
		//$where = array_merge($where, array('status' => 1));
		
		return parent::get($where, $order, $limit, $id_lang);
	}
	
	public static function getOne($where = array(), $order = null, $limit = null, $id_lang = CURRENT_LANG_ID) {
		$return = self::get($where, $order, $limit, $id_lang);
		
		if(is_array($return) && !empty($return))
		return reset($return);
		
		return null;
	}
	
	public static function count($where = array()) {
		
		if(isset($where["categories"])){
			self::_getMapModel();
			
			$mapLst = self::$_modelMap->get(array("id_categorie" => $where["categories"]));
			$ids = array();
			
			if(is_array($mapLst) && !empty($mapLst)){
				
				$ids[] = str_repeat(" id_article = ? OR", count($mapLst)). " 0";
				foreach($mapLst as $map){
					$ids[] = $map["id_article"];
				}
			
				unset($where["categories"]);
				$where["id_article"] = $ids;
			}
			else {
				return 0;
			} 
			
		}
		
		return parent::count($where);
	}
	
	private function appendWhereCategories($where){

	}
	
	public function getPrevArticle()
	{
		$cats = $this->getCategories();
		foreach ($cats as $cat) {
			$arrayCat[] = $cat->id_categorie;
		}
		
		$where = array(
						'categories' => $arrayCat, 
						'status' 	=> Articles_Object_Article::STATUS_PUBLISH,
						array('((A.date_start = ? AND A.id_article < ?) OR (A.date_start < ?)) AND (isPermanent = ? AND date_start < ? OR date_start < ? AND date_end > ?)', 
									$this->date_start, $this->id_article, $this->date_start,  1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'))
		);
		
		return self::getOne($where, array('A.date_start' => 'DESC', 'A.id_article' => 'DESC'), 1);
	}
	 
	public function getNextArticle()
	{
		$cats = $this->getCategories();
		foreach ($cats as $cat) {
			$arrayCat[] = $cat->id_categorie;
		}
		
		$where = array(
								'categories' => $arrayCat, 
								'status' 	=> Articles_Object_Article::STATUS_PUBLISH,
								array('((A.date_start = ? AND A.id_article > ?) OR (A.date_start > ?)) AND (isPermanent = ? AND date_start < ? OR date_start < ? AND date_end > ?)',
											$this->date_start, $this->id_article, $this->date_start,  1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'))
		);
		
		return self::getOne($where, array('A.date_start' => 'ASC', 'A.id_article' => 'ASC'), 1);
	}
	
	public function getArticleInCat()
	{
		$cats = $this->getCategories();
		foreach ($cats as $cat) {
			$arrayCat[] = $cat->id_categorie;
		}
		
		$where = array(
						'categories' => $arrayCat, 
						'status' 	=> Articles_Object_Article::STATUS_PUBLISH,
						array('isPermanent = ? AND date_start < ? OR date_start < ? AND date_end > ?', 1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'))
		);
		
		return self::get($where, array('A.date_start' => 'ASC', 'A.id_article' => 'ASC'));
	}
	
	public function getCategories() {
		
		if(empty($this->categories)) {
			if( !isset(self::$_modelMap) )
				self::_getMapModel();
			
			$rows = self::$_modelMap->get(array("id_article" => $this->id_article));
			
			$this->categories = array();
			if( !empty($rows) ) {
				foreach ($rows as $row) {
					$this->categories[] = new Articles_Object_Categorie((int)$row['id_categorie']);
				}
			}
		}
		
		return $this->categories;
	}
	
	public function setCategories($datas) {
		$this->categories = array();
		foreach ($datas as $data) {
			$this->categories[] = $data;
		}
	}
	
	public function save() {
		
		/*
		 * Set options defaults
		 */
		
		// Facebook
		$config = CMS_Application_Config::getInstance();
		$options = json_decode($config->get("mod_articles-options"), true);
		$this->fb_comments_active 	= ($this->fb_comments_active != null) ? $this->fb_comments_active : $options['fb_comments_active'];
		
		// Article soumis
		$this->isSubmitted = isset($this->isSubmitted) ? $this->isSubmitted : 0;
		
		// Si article validé, il n'est plus considéré comme proposé
		if($this->status)
			$this->isSubmitted 	= 0;
		
		parent::save();
		
		self::_getMapModel();

		if( $this->id_article )
			self::$_modelMap->delete(array('id_article' => $this->id_article));
		
		foreach ($this->categories as $categorie) {
			self::$_modelMap->insert( array(
				'id_article' 	=> $this->id_article,
				'id_categorie' 	=> ($categorie instanceof Articles_Object_Categorie) ? $categorie->id_categorie : $categorie
			), false);
		}
		
		return $this->id_article;
	}
	
	public static function deleteFromPrimaryKey($id) {
		parent::deleteFromPrimaryKey($id);
		
		self::_getMapModel();
		
		self::$_modelMap->delete(array('id_article' => $id));
	}
	
	protected static function _getMapModel() {
		if (empty(static::$_modelMap) && class_exists(static::$_modelMapClass)) {
			static::$_modelMap = new static::$_modelMapClass();
			return;
		}
	}
}
