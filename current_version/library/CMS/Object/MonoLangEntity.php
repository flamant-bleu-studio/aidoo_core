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

abstract class CMS_Object_MonoLangEntity extends CMS_Object_Abstract {
	
	public function __construct($data = null) {
		parent::__construct($data, "all");
	}
	
	/**
	 * Retourne des instances d'objets selon des critères de recherche
	 * @param array $where filtre les résultats
	 * @param mixed $order ordonne les résultats
	 * @param mixed $limit limite les résultats
	 */
	public static function get($where = array(), $order = null, $limit = null) {
		return parent::get($where, $order, $limit, "all");
	}

}