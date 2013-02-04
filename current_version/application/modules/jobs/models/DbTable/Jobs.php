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

class Jobs_Model_DbTable_Jobs extends CMS_Model_MonoLang {   
	
	protected $_name = "jobs";
	
	protected $_primaryKey 	= "id";
	protected $_values 		= array("job_title", "contract_type", "sector", "domain", "description", "contact");
	
	protected $_disableAutoDate = true;
	
	public function getCriteria($crit) {
		
		$availableCrits = array('contract_type', 'sector', 'domain');
		
		if (!in_array($crit, $availableCrits))
			return array();
		
		$sql = "SELECT DISTINCT(".$crit.") FROM 1_jobs";
		
		$return = $this->getAdapter()->fetchAll($sql, array(), PDO::FETCH_COLUMN);
		
		return $return;
	}
}
