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

class Blocs_Model_DbTable_Items extends CMS_Model_MultiLang {
	
	protected $_name = 'core_templates_items';
	protected $_dependentTables = array('Blocs_Model_DbTable_Map');
	
	protected $_primaryKey 			= "id_item";
	protected $_values 				= array("active", "templateFront", "decorator", "classCss", "theme", "type", "sizeBloc");
	protected $_translatedValues 	= array("designation", "title", "params");
	
	
	public function migrationBlocsMultiLang() {
		
		/*
		 * A FAIRE :
		 * 	- Nommer la table déjà existante '1_core_templates_items' en '1_core_templates_items_back'
		 *  - Créer la nouvelle table '1_core_templates_items' avec sa nouvelle structure multi-langue
		 *  - Executer ce script
		 *  /!\ les tables '1_core_templates_items' et '1_core_templates_items_lang' doivent être vides !
		 */
		
		$db = $this->getAdapter();
		
		$stmt = $db->query('SELECT * FROM 1_core_templates_items_back');
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		foreach ($rows as $r) {
			
			// TYPE (certains blocs ont changé de nom !)
			if( $r->type == "Bloc_StaticHtmlv2_StaticHtml" ) {
				$type = "Bloc_StaticHtml_Main";
			}
			elseif( $r->type == "Bloc_Articles_Article" ) {
				$type = "Bloc_Articles_Main";
			}
			else {
				$type = $r->type;
			}
			
			// COMMON
			$datas_common = array(
				'id_item'		=> $r->id,
				'templateFront' => $r->templateFront,
				'decorator' 	=> $r->decorator,
				'classCss' 		=> $r->classCss,
				'theme' 		=> $r->theme,
				'type' 			=> $type,
				'date_add' 		=> date('Y-m-d H:i:s'),
				'date_upd' 		=> date('Y-m-d H:i:s'),
			);
			
			// Insère les données communes
			$db->insert('1_core_templates_items', $datas_common);
			
			// Récupération de l'id enregistré
			$id_item = $r->id;
			
			// LANG
			$datas_lang = array(
				'id_item' 		=> $id_item,
				'designation' 	=> $r->title,
				'title' 		=> $r->title,
				'params' 		=> $r->params,
			);
			
			$datas_lang_fr = $datas_lang;
			$datas_lang_fr['id_lang'] = 1;
			
			/*$datas_lang_en = $datas_lang;
			$datas_lang_en['id_lang'] = 2;*/
			
			$db->insert('1_core_templates_items_lang', $datas_lang_fr); // Insère les données traduisibles FR
			//$db->insert('1_core_templates_items_lang', $datas_lang_en); // Insère les données traduisibles EN
		}
	}
}
