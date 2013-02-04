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

class Search_Lib_Search {
	
	private $_index;
	private $_indexName;
	private $_indexPath;
	
	public function __construct($indexName){
		
		setlocale(LC_CTYPE, 'fr_FR.UTF-8');
		
		$this->_indexName = $indexName;
		$this->_indexPath = CMS_PATH . '/tmp/searchindexes/';
		
		$this->getIndex();
	}
	
	private function getIndex(){
		
		$analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive();
		Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
		
		if(file_exists($this->_indexPath.$this->_indexName))
			$this->_openIndex();
		else {
			$this->_createIndex()->_updateIndex();
		}
		
		return $this->_index;
	}
	
	private function _createIndex(){
		$this->_index = Zend_Search_Lucene::create($this->_indexPath.$this->_indexName);
		return $this;
	}
	private function _openIndex(){
		$this->_index = Zend_Search_Lucene::open($this->_indexPath.$this->_indexName);
		return $this;
	}
	
	private function _updateIndex(){
		
		$hooks = CMS_Application_Hook::getInstance();
		
		$contents = $hooks->apply_filters("listSearchableContent", array());

		foreach($contents as &$data){

			$doc = new Zend_Search_Lucene_Document();

			$doc->addField(Zend_Search_Lucene_Field::text("content_search", self::cleanString(self::commonCleanString($data["title"])).' '.self::cleanString(self::commonCleanString($data["content"])), 'utf-8'));
			
			$doc->addField(Zend_Search_Lucene_Field::unIndexed("title_item", self::commonCleanString($data["title"])), 'utf-8');
			$doc->addField(Zend_Search_Lucene_Field::unIndexed("content_item", self::truncateText(self::commonCleanString($data["content"]))), 'utf-8');
			$doc->addField(Zend_Search_Lucene_Field::unIndexed("route_item", json_encode($data["route"])), 'utf-8');
			
			$this->_index->addDocument($doc);
		}
		
		$this->_index->optimize();
	}
	
	public function updateIndex(){
		$this->_createIndex();
		$this->_updateIndex();
	} 
	
	public function search($search){

		$search = explode(' ', self::cleanString($search));
		$query = new Zend_Search_Lucene_Search_Query_Boolean();
			
		// Boucle sur tous les mots
		foreach ($search as $q) {
								
			// Si le mot fait moins de 3 lettres
			if (strlen($q) < 3) {
				$queryTerm = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($q, 'content_search'));
				$query->addSubquery($queryTerm, true);
			}
			// Si le mot fait 3 lettres ou plus
			else {
				// Ajout du terme avec joker
				$pattern = new Zend_Search_Lucene_Index_Term($q.'*', 'content_search');
			    $subquery = new Zend_Search_Lucene_Search_Query_Wildcard($pattern);
			    $query->addSubquery($subquery, true);
			}
		}

		$hits = $this->_index->find($query);
		
		return $hits;
	}
	
	public static function cleanString($string){ 
		
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	
		// Accents + tolower + trim
		$string = strtolower(str_replace($a, $b, $string));
		
		// Symboles + mot de moins de 2 lettres
		// $var = preg_replace("#([^a-z0-9]+)|(\b[^\s]{1,2}\b)#i", " " , $var);
		$string = preg_replace("#([^a-z0-9]+)#i", " " , $string);
		
		return $string;
	}
	
	public static function commonCleanString($string){

		// Scripts JS (flag is : insensitive + le point = caractère et/ou newline)
		$string = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $string);
		
		$string = html_entity_decode(strip_tags($string), ENT_QUOTES, 'UTF-8');
				
		// multi space
		$string = trim(preg_replace("#([\s]+)#i", " " , $string));
		
		return $string;	
	}
		
	public static function truncateText($text, $count = 200){
		
		if (strlen($text) > $count) {
			$text = substr($text, 0, $count);
			$last_space = strrpos($text, " ");
			$text = substr($text, 0, $last_space);
			$text .= '...';
		}
		
		return $text;
	}
}