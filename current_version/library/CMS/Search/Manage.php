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

abstract class CMS_Search_Manage
{
	protected $_indexName;
	private $_indexPath;
	private $_isTemp = false;
	
	/**
	 * @var Zend_Search_Lucene
	 */
	private $_index;
	
	public function __construct($indexName)
	{
		$this->_indexName 	= $indexName;
		$this->_indexPath	= CMS_PATH . '/tmp/searchindexes/';
		
		$this->init();
	}
	
	/**
	 * Singleton
	 */
	public static function getInstance() {
		if (empty(static::$_instance))
			static::$_instance = new static(UNIQUE_ID . '_' . static::$_suffixIndex);
		return static::$_instance;
	}
	
	/**
	 * Initialisation de la classe
	 */
	private function init()
	{
		setLocale(LC_COLLATE, 'fr_FR.utf8');
	}
	
	/**
	 * Initialisation de la recherche dans l'index
	 */
	private function initSearch()
	{
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
	}
	
	/**
	 * Execute une recherche
	 */
	public function search($stringSearch, $page = null, $count = null)
	{
		$this->getOrCreateIndex();
		
		// Query principale
		$rootQuery = new Zend_Search_Lucene_Search_Query_Boolean();
		
		// Découpe la chaine de caractère
		$words = explode(" ", self::sanitizeString($stringSearch));
		
		/**
		 * Etape 1
		 * - Cherche avec chacun des termes (mots)
		 */
		
		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		
		$multiTerm = null;
		
		foreach ($words as $word) {
			if (strlen($word) < 3) {
				if(!$multiTerm)
					$multiTerm = new Zend_Search_Lucene_Search_Query_MultiTerm();
				
				$multiTerm->addTerm(new Zend_Search_Lucene_Index_Term($word, 'title_' . CURRENT_LANG_CODE, true));
				$multiTerm->addTerm(new Zend_Search_Lucene_Index_Term($word, 'content_' . CURRENT_LANG_CODE, true));
			}
			else {
				$pattern = new Zend_Search_Lucene_Index_Term($word.'*', 'title_' . CURRENT_LANG_CODE);
			    $subquery = new Zend_Search_Lucene_Search_Query_Wildcard($pattern);
			    
			    $pattern2 = new Zend_Search_Lucene_Index_Term($word.'*', 'content_' . CURRENT_LANG_CODE);
			    $subquery2 = new Zend_Search_Lucene_Search_Query_Wildcard($pattern2);
			    
			    $query->addSubquery($subquery);
			    $query->addSubquery($subquery2);
			}
		}
		
		if(!empty($multiTerm))
			$query->addSubquery($multiTerm, true);
		
		$rootQuery->addSubquery($query);
		
		/**
		 * Etape 2
		 * - Cherche avec la totalité des termes (mots) concaténés
		 */
		
		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		
		$subquery1 = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term(str_replace(" ", "", self::sanitizeString($stringSearch)), 'content_' . CURRENT_LANG_CODE));
		$subquery2 = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term(str_replace(" ", "", self::sanitizeString($stringSearch)), 'item_' . CURRENT_LANG_CODE));
		
		$query->addSubquery($subquery1, true);
		$query->addSubquery($subquery2, true);
		
		$rootQuery->addSubquery($query);
		
		/**
		 * Resultat
		 */
		$return = array();
		
		$hits = $this->_index->find($rootQuery, "score", SORT_NUMERIC, SORT_DESC, "score");
		
		$hits = new ArrayObject($hits);
		
		$nbResult = count($hits);
		$return['total_count'] = $nbResult;
		
		if (isset($page) && isset($count)) {
			// Pagination
			$page = ($page < 1) ? 1 : $page;
			
			$offset = ($page - 1) * $count;
			
			if (!$nbResult)
				return null;
			
			if($offset > $nbResult && $nbResult > 0)
				throw new Zend_Controller_Action_Exception("Page not found", 404);
			
			// Récupération des résultats en fonction de la page souhaitée
			$hits = new LimitIterator($hits->getIterator(), $offset, $count);
		}
		
		// HightLight
		/*foreach ($hits as &$hit) {
			$return = $rootQuery->highlightMatches($hit->{content.'_'.CURRENT_LANG_CODE});
			
			if ($return)
				$hit->{content.'_'.CURRENT_LANG_CODE} = $return;
			
			$return = $rootQuery->highlightMatches($hit->{title.'_'.CURRENT_LANG_CODE});
			
			if ($return)
				$hit->{title.'_'.CURRENT_LANG_CODE} = $return;
		}*/
		
		$return['hits'] = $hits;
		
		return $return;
	}
	
	/**
	 * Création d'un nouvel index
	 */
	public function createIndex()
	{
		$analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
		Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
		
		$this->_index = Zend_Search_Lucene::create($this->_indexPath.$this->_indexName);
	}
	
	/**
	 * Création d'un index temporaire
	 */
	public function generateIndexTemp()
	{
		$analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
		Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
		
		$this->_isTemp = true;
		$this->_index = Zend_Search_Lucene::create($this->_indexPath.$this->_indexName.'_temp');
	}
	
	/**
	 * Récupère un index existant
	 */
	private function openIndex()
	{
		$analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
		Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
		
		$this->_index = Zend_Search_Lucene::open($this->_indexPath.$this->_indexName);
	}
	
	/**
	 * Récupère un index si il existe sinon le créé
	 */
	private function getOrCreateIndex()
	{
		if (!empty($this->_index))
			return;
		
		if (file_exists($this->_indexPath.$this->_indexName))
			$this->openIndex();
		else
			$this->createIndex();
	}
	
	/**
	 * Ajoute plusieurs items
	 */
	public function addItems(array $items) {
		foreach ($items as $item)
			$this->addItem($item);
	}
	
	/**
	 * Ajouter un item dans l'index
	 */
	public function addItem(array $datas)
	{
		if (!$this->_isTemp)
			$this->getOrCreateIndex();
		
		if (!$this->_isTemp && $this->existItem($this->sanitizeString($datas['item_id']))) {
			$this->updateItem($datas);
			return;
		}
		
		$document = new Zend_Search_Lucene_Document();
		
		// Création de l'index du nouvel item
		$document->addField(Zend_Search_Lucene_Field::keyword('item_id', $this->sanitizeString($datas['item_id'])));
		
		// Récupération du continu pouvant être cherché dans l'index
		$langs = json_decode(CMS_Application_Config::getInstance()->get('availableFrontLang'), true);
		
		$content = array();
		
		if ($datas['content']) {
			foreach ($langs as $lang_id => $lang_code) {
				foreach ($datas['content'] as $c) {
					if (is_array($c) && !empty($c[$lang_id]))
						$content[$lang_code] .= $c[$lang_id] . ' ';
					elseif (is_array($c) && !empty($c[DEFAULT_LANG_ID]))
						$content[$lang_code] .= $c[DEFAULT_LANG_ID] . ' ';
					else if (!is_array($c))
						$content[$lang_code] .= $c . ' ';
				}
			}
		}
		
		foreach ($langs as $lang_id => $lang_code)
			$document->addField(Zend_Search_Lucene_Field::text('content_'.$lang_code, $this->sanitizeString($content[$lang_code]), 'utf8'));
		
		// Type de contenu
		$document->addField(Zend_Search_Lucene_Field::unIndexed('type', $datas['type'], "utf-8"));
		
		// Création du contenu ne pouvant pas être cherché dans l'index
		if ($datas['datas']) {
			foreach ($langs as $lang_id => $lang_code) {
				foreach ($datas['datas'] as $key => $c) {
					// Données qui ne peuvent pas être cherchées
					if ($key != "title") {
						if (is_array($c)) {
							$field = Zend_Search_Lucene_Field::unIndexed($key.'_'.$lang_code, $this->sanitizeString($c[$lang_id]), 'utf-8');
							$filed->boost = 1;
							$document->addField($field);
						}
						else {
							$field = Zend_Search_Lucene_Field::unIndexed($key.'_'.$lang_code, $this->sanitizeString($c), 'utf-8');
							$filed->boost = 1;
							$document->addField($field);
						}
					}
					// Données qui peuvent être cherchées
					else {
						if (is_array($c)) {
							$field = Zend_Search_Lucene_Field::text($key.'_'.$lang_code, $this->sanitizeString($c[$lang_id]), 'utf-8');
							$field->boost = 1.5;
							$document->addField($field);
						}
						else {
							$field = Zend_Search_Lucene_Field::text($key.'_'.$lang_code, $this->sanitizeString($c), 'utf-8');
							$field->boost = 1.5;
							$document->addField($field);
						}
					}
				}
			}
		}
		
		if($datas['url_front'])
			$document->addField(Zend_Search_Lucene_Field::unIndexed('url_front', json_encode($datas['url_front']), 'utf-8'));
		
		if($datas['url_back'])
			$document->addField(Zend_Search_Lucene_Field::unIndexed('url_back', json_encode($datas['url_back']), 'utf-8'));
		
		// Ajout de l'item
		$this->_index->addDocument($document);
		$this->_index->commit();
		$this->_index->optimize();
	}
	
	/**
	 * Met à jour un item
	 */
	public function updateItem(array $datas)
	{
		if ($this->existItem($datas['item_id']))
			$this->deleteItem($datas['item_id']);
		
		$this->addItem($datas);
	}
	
	/**
	 * L'item specifié est il existant dans l'index ?
	 * @return bool
	 */
	public function existItem($item_id)
	{
		$this->getOrCreateIndex();
		
		$hits = $this->_index->find('item_id:' . $this->sanitizeString($item_id));
		
		return (!empty($hits) ? true : false);
	}
	
	/**
	 * Supprimer un item de l'index
	 */
	public function deleteItem($item_id)
	{
		$this->openIndex();
		
		$hits = $this->_index->find('item_id:' . $this->sanitizeString($item_id));
		
		foreach ($hits as $hit)
		    $this->_index->delete($hit->id);
		
		$this->_index->commit();
	}
	
	/**
	 * Supprime l'index
	 */
	public function deleteIndex()
	{
		if (file_exists($this->_indexPath.$this->_indexName)) {
			$folder = new RecursiveDirectoryIterator($this->_indexPath.$this->_indexName);
			$files 	= new RecursiveIteratorIterator($folder, RecursiveIteratorIterator::CHILD_FIRST);
			
			foreach($files as $file)
				if ($file->isFile())
					@unlink($file->getRealPath());
			
			@rmdir($this->_indexPath.$this->_indexName);
		}
	}
	
	/**
	 * L'index temporaire remplace l'index final
	 */
	public function finishIndexTemp()
	{
		if(!$this->_isTemp)
			return;
		
		if (!file_exists($this->_indexPath.$this->_indexName.'_temp'))
			return;
		
		$this->_index->commit();
		$this->_index->optimize();
		
		try {
			$this->deleteIndex();
			
			@rename($this->_indexPath.$this->_indexName.'_temp', $this->_indexPath.$this->_indexName);
		}
		catch(Exception $e) {
			CMS_Log::getInstance()->err($e->getMessage());
		}
	}
	
	/**
	 * Prépare une chaine de caractère pour sa mise en place dans l'index
	 */
	private function sanitizeString($string)
	{
		$a = array(' et ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
	    $b = array(' ', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		
	    // Remplace les accents
	    $string = str_replace($a, $b, $string);
		// Met la chaine en miniscule
 	    $string = strtolower($string);
		// Retire les doubles espaces
		$string = trim(preg_replace("#([\s]{2,})#i"," " , $string));
		// Retire le html / php
		$string = strip_tags($string);
		
		return $string;
	}
}