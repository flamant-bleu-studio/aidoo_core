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

class Faq_Model_DbTable_Question extends CMS_Model_MultiLang 
{
	
	protected $_name = 'faq_items';

	protected $_primaryKey 			= "id_faq_item";
	protected $_values 				= array("question_order", 'parent_id');
	protected $_translatedValues 	= array("question", "answer");
	
	protected $_disableAutoDate = true;
	
	/**
	 * 
	 * Fonction permettant de récupérer lors de l'insertion le question_order correspondant a la derniere FAQ
	 * @return <int> plus grand question_order + 1
	 */
	public function getLastOrder(){
		// Requete de suppression multi-table
		$sql = "SELECT IFNULL(MAX(question_order), 0) + 1  FROM 1_faq_items";
		
		$return = $this->getAdapter()->fetchOne($sql);
				
		return (int)$return;
	}
}
