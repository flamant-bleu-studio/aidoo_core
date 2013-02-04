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

class Documents_Model_DbTable_Documents extends CMS_Model_MultiLang {
	
	protected $_name = 'documents';

	protected $_primaryKey 			= "id_document";
	protected $_values 				= array("type", "template", "author", "status", "access");
	protected $_translatedValues 	= array("title");
	
public function migrationMultiLang() {
		
		/*
		 * A FAIRE :
		 *  - Créer la nouvelle table '1_documents' avec sa nouvelle structure multi-langue
		 *  - Créer la nouvelle table '1_documents_nodes' avec sa nouvelle structure multi-langue
		 *  - Executer ce script
		 *  /!\ les tables '1_documents', '1_documents_lang', '1_documents_nodes' et '1_documents_nodes_lang' doivent être vides !
		 */
		
		$db = $this->getAdapter();
		
		$documents 		= $db->query('SELECT * FROM 1_contents')->fetchAll(PDO::FETCH_OBJ);
		$nodes_back 	= $db->query('SELECT * FROM 1_nodes')->fetchAll(PDO::FETCH_OBJ);
		
		// Organisation des nodes
		$nodes = array();
		foreach ($nodes_back as $node) {
			$nodes[$node->content_id][] = $node;
		}
		
		foreach ($documents as $document) {
			
			$id_document = $document->id;
			
			$status = ($document->status == 'publish') ? 1 : 0;
			
			/*
			 * Article
			 */
			$datas_common = array(
				'id_document' 	=> $id_document,
				'type' 			=> $document->type,
				'template' 		=> $document->template,
				'author' 		=> $document->author,
				'status'		=> $status,
				'access'		=> $document->access,
				'date_add' => date('Y-m-d H:i:s'),
				'date_upd' => date('Y-m-d H:i:s'),
			);
			
			// Insère les données communes
			$db->insert('1_documents', $datas_common);
			
			// LANG
			$datas_lang = array(
				'id_document' 	=> $id_document,
				'title' 		=> $document->title,
			);
			
			$datas_lang_fr = $datas_lang;
			$datas_lang_fr['id_lang'] = 1;
			
			$datas_lang_en = $datas_lang;
			$datas_lang_en['id_lang'] = 2;
			
			$db->insert('1_documents_lang', $datas_lang_fr); // Insère les données traduisibles FR
			//$db->insert('1_documents_lang', $datas_lang_en); // Insère les données traduisibles EN
			
			/*
			 * Nodes
			 */
			if( empty($nodes[$id_document]))
				continue;
			
			foreach ($nodes[$id_document] as $node) {
				
				$id_node = $node->id;
				
				// données communes
				$datas_nodes_common = array(
					'id_node' 		=> $id_node,
					'id_document' 	=> $id_document,
					'name'			=> $node->name,
				);
				
				$db->insert('1_documents_nodes', $datas_nodes_common);
				
				// lang
				$datas_nodes_lang = array(
					'id_node' 		=> $id_node,
					'value' 		=> $node->value,
				);
				
				$datas_nodes_lang_fr = $datas_nodes_lang;
				$datas_nodes_lang_fr['id_lang'] = 1;
				
				$datas_nodes_lang_en = $datas_nodes_lang;
				$datas_nodes_lang_en['id_lang'] = 2;
				
				$db->insert('1_documents_nodes_lang', $datas_nodes_lang_fr); // Insère les données traduisibles FR
				//$db->insert('1_documents_nodes_lang', $datas_nodes_lang_en); // Insère les données traduisibles EN
			}
		}
	}
}