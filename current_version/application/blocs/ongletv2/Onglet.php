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

class Bloc_Ongletv2_Onglet extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $onglet;
	
	protected $_adminFormClass = "Bloc_Ongletv2_AdminForm";
	
	public function runtimeAdmin($view){
		
		/** Get all bloc **/
		$blocs = CMS_Bloc_Abstract::get();
		$allBlocs = array();
		foreach ($blocs as $bloc )
			$allBlocs[$bloc->id] = $bloc->titleBloc;
		
		/** Get onglet width blocs deleted **/
		$ongletWithBlocDeleted = array();
		
		if( count($this->onglet) > 0 )
		{
			foreach ($this->onglet as $onglet)
			{
				if(count($onglet["blocs"]) > 0){
					foreach ($onglet["blocs"] as $bloc)
					{
						if( !CMS_Bloc_Abstract::getBlocInstance($bloc) )
							$ongletWithBlocDeleted[] = $onglet["title"];
					}
				}
			}
		}
		
		/** var smarty **/
		$view->allBlocsTpl = $allBlocs;
		$view->onglets = $this->onglet;
		$view->ongletWithBlocDeleted = $ongletWithBlocDeleted;
	}
	
	public function runtimeFront($view){
		
		$onglets = array();
		
		if( count($this->onglet) > 0 )
		{
			$i = 0;
			foreach ($this->onglet as $onglet)
			{
				$onglets[$i]["title"] = $onglet["title"];
				$onglets[$i]["css"] = $onglet["css"];
				
				if( count($onglet["blocs"]) > 0)
				{
					foreach ($onglet["blocs"] as $bloc)
					{
						$b = CMS_Bloc_Abstract::getBlocInstance($bloc);
						
						if( $b )
						{
							$onglets[$i]["render"][] = $b->renderFront();
						}
						unset($b);
					}
					
					$i++;
				}
			}
		}
		
		$view->id			= $this->id;
		$view->titleBloc 	= $this->titleBloc;
		$view->classCssBloc = $this->classCssBloc;
		$view->nameBloc = $this->getNameBloc();
		$view->onglets = $onglets;
	}
	
	public function save($post){
		$this->onglet = $post["onglet"];
		
		$id = parent::save($post);
		
		return $id;
	}
	
}