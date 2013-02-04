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

class Bloc_Jobsv2_Jobs extends CMS_Bloc_Abstract implements CMS_Bloc_Interface
{
	public $nb_jobs;
	
	protected $_adminFormClass = "Bloc_Jobsv2_AdminForm";
	
	public function runtimeFront($view)
	{
		$jobs = Jobs_Object_Jobs::get();
		
		$return = array();
		
		for($i = 0 ; $i < $this->nb_jobs ; $i++)
		{
			if( $jobs[$i] )
				$return[] = $jobs[$i];
		}
		
		$view->jobs = $return;
	}
	
	public function save($post)
	{		
		$this->nb_jobs = $post["nb_jobs"];
				
		$id = parent::save($post);
		
		return $id;
	}
}