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

class CMS_Page_Model_Pages extends CMS_Model_MultiLang {
	
	protected $_name = 'core_pages';
		
	protected $_primaryKey 			= "id_page";
	
	protected $_values 				= array("url_system", "rewrite_var", "enable", "visible", "wildcard", "type", "api", "content_id", "template", "diaporama");
	protected $_translatedValues 	= array("title", "url_rewrite", "meta_keywords", "meta_description");
		
	
	public function setDefaultTplByTplID($id){
		$where = array("template" => (int)$id);
		$this->appendWhereClause($sql, $where, false, false);
		return $this->getAdapter()->update($this->_name, array("template" => null), $sql);
	}
	
	/*
	 * Surcharge de l'update pour la remise à NULL du template et du diapo
	 */
	public function update($datas, $where, $autoDate = true){

		if(isset($datas['diaporama']) && $datas['template'] == '')
			$datas['template'] = null;
			
		if(isset($datas['diaporama']) && $datas['diaporama'] == 'null')
			$datas['diaporama'] = null;
			
		parent::update($datas, $where, $autoDate);
	}
	
	public function migrationMultiLang() {
		
		/*
		 * A FAIRE :
		 * 	- Nommer la table déjà existante '1_core_pages' en '1_core_pages_back'
		 *  - Créer la nouvelle table '1_core_pages' et '1_core_pages_lang' avec sa nouvelle structure multi-langue
		 *  - Executer ce script
		 */
		
		$db = $this->getAdapter();
		
		$pages = $db->query('SELECT * FROM 1_core_pages_back')->fetchAll(PDO::FETCH_OBJ);
		
		foreach ($pages as $page) {
			
			$datas_common = array(
				'id_page'		=> $page->id,
				'url_system'	=> $page->url_system,
				'enable'		=> $page->enable,
				'visible'		=> $page->visible,
				'wildcard'		=> $page->wildcard,
				'type'			=> $page->type,
				'api'			=> $page->api,
				'content_id'	=> $page->content_id,
				'template'		=> $page->template,
				'diaporama'		=> $page->diaporama,
				'date_add'		=> date('Y-m-d H:i:s'),
				'date_upd'		=> date('Y-m-d H:i:s')
			);
			
			$datas_lang = array(
				'id_page' 			=> $page->id,
				'title'	  			=> $page->title,
				'url_rewrite' 		=> $page->url_rewrite,
				'meta_keywords' 	=> $page->meta_keywords,
				'meta_description' 	=> $page->meta_description
			);
			
			$datas_lang_fr = $datas_lang;
			$datas_lang_fr['id_lang'] = 1;
			
			$datas_lang_en = $datas_lang;
			$datas_lang_en['id_lang'] = 2;
			$datas_lang_en['url_rewrite'] = null;
			
			$db->insert('1_core_pages', $datas_common);
			$db->insert('1_core_pages_lang', $datas_lang_fr);
			//$db->insert('1_core_pages_lang', $datas_lang_en);
		}
	}
}
	