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

class Diaporama_Model_DbTable_Diaporama extends CMS_Model_MonoLang
{
	protected $_name = "diaporamas";
	
	protected $_primaryKey 	= "id";
	protected $_values 		= array("title", "size");
	
	public function migrate()
	{
		$db = $this->getAdapter();
		
		$oldDiaporamas = $db->query('INSERT 1_diaporamas (id,title,size, date_add, date_upd) SELECT id, title, size,date_add, date_upd FROM 1_galeries WHERE type=2');
		
		$images = $db->query('SELECT * FROM 1_galeries_images as i LEFT JOIN 1_galeries as g ON (g.id=i.parent_id AND g.type=2)')->fetchAll(PDO::FETCH_OBJ);
		
		foreach ($images as $i) {
			
			$datas = json_decode($i->datas, true);
			
			$params = array();
			$params[] = $i->parent_id;
			$params[] = $i->description;
			$params[] = $i->bg_color;
			$params[] = $i->path;
			$params[] = $datas['link_type'];
			$params[] = $datas['page_link'];
			$params[] = $datas['external_page'];
			$params[] = $datas['window'];
			
			$db->query('INSERT 1_diaporamas_images (parent_id,text,background_color,image,link_type,link_internal,link_external,link_target_blank) VALUES(?,?,?,?,?,?,?,?)', $params);
		}
	}
}