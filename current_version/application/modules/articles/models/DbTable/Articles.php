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

class Articles_Model_DbTable_Articles extends CMS_Model_MultiLang {
	
	protected $_name = 'articles';

	protected $_primaryKey 			= "id_article";
	protected $_values 				= array("type", "template", "readmore", "image", "isPermanent", "author", "status", "isSubmitted" , "access", "date_start", "date_end", "fb_comments_active");
	protected $_translatedValues 	= array("title", "chapeau");
	
	
	public function migrationMultiLang() {
		
		/*
		 * A FAIRE :
		 * 	- Nommer la table déjà existante '1_articles' en '1_articles_back'
		 * 	- Nommer la table déjà existante '1_articles_nodes' en '1_articles_nodes_back'
		 *  - Créer la nouvelle table '1_articles' avec sa nouvelle structure multi-langue
		 *  - Créer la nouvelle table '1_articles_nodes' avec sa nouvelle structure multi-langue
		 *  - Executer ce script
		 *  /!\ les tables '1_articles', '1_articles_lang', '1_articles_nodes' et '1_articles_nodes_lang' doivent être vides !
		 */
		
		$db = $this->getAdapter();
		
		$articles 		= $db->query('SELECT * FROM 1_articles_back')->fetchAll(PDO::FETCH_OBJ);
		$nodes_back 	= $db->query('SELECT * FROM 1_articles_nodes_back')->fetchAll(PDO::FETCH_OBJ);
		
		// Organisation des nodes
		$nodes = array();
		foreach ($nodes_back as $node) {
			$nodes[$node->parent_id][] = $node;
		}
		
		foreach ($articles as $article) {
			
			$id_article = $article->id;
			
			/*
			 * Article
			 */
			$datas_common = array(
				'id_article' 	=> $id_article,
				'category' 		=> $article->category,
				'type' 			=> $article->type,
				'template' 		=> $article->template,
				'readmore' 		=> $article->readmore,
				'image' 		=> $article->image,
				'isPermanent' 	=> $article->isPermanent,
				'author' 		=> $article->author,
				'status'		=> $article->status,
				'access'		=> $article->access,
				'date_start' 	=> $article->date_start,
				'date_end' 		=> $article->date_end,
				'date_add' => date('Y-m-d H:i:s'),
				'date_upd' => date('Y-m-d H:i:s'),
			);
			
			// Insère les données communes
			$db->insert('1_articles', $datas_common);
			
			// LANG
			$datas_lang = array(
				'id_article' 	=> $id_article,
				'title' 		=> $article->title,
				'chapeau' 		=> $article->chapeau,
			);
			
			$datas_lang_fr = $datas_lang;
			$datas_lang_fr['id_lang'] = 1;
			
			$datas_lang_en = $datas_lang;
			$datas_lang_en['id_lang'] = 2;
			
			$db->insert('1_articles_lang', $datas_lang_fr); // Insère les données traduisibles FR
			//$db->insert('1_articles_lang', $datas_lang_en); // Insère les données traduisibles EN
			
			/*
			 * Nodes
			 */
			if( empty($nodes[$id_article]))
				continue;
			
			foreach ($nodes[$id_article] as $node) {
				
				$id_node = $node->id;
				
				// données communes
				$datas_nodes_common = array(
					'id_node' 		=> $id_node,
					'id_article' 	=> $id_article,
					'name'			=> $node->name,
				);
				
				$db->insert('1_articles_nodes', $datas_nodes_common);
				
				// lang
				$datas_nodes_lang = array(
					'id_node' 		=> $id_node,
					'value' 		=> $node->value,
				);
				
				$datas_nodes_lang_fr = $datas_nodes_lang;
				$datas_nodes_lang_fr['id_lang'] = 1;
				
				$datas_nodes_lang_en = $datas_nodes_lang;
				$datas_nodes_lang_en['id_lang'] = 2;
				
				$db->insert('1_articles_nodes_lang', $datas_nodes_lang_fr); // Insère les données traduisibles FR
				//$db->insert('1_articles_nodes_lang', $datas_nodes_lang_en); // Insère les données traduisibles EN
			}
		}
	}
}
