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

class Bloc_Diaporama_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $mode;
	public $diaporamaId;
	public $diaporamaIdPage;
	public $pagination;
	
	public $bx_type;
	public $pause;
	public $displaySlideQty;
	public $moveSlideQty;
	
	protected $_adminFormClass = "Bloc_Diaporama_AdminForm";
	protected static $_translatableFields = array();
	
	public function runtimeFront($view){
		
		$page = CMS_Page_Current::getInstance();
		
		if( $page && ($this->mode == "byPage" && $page->diaporama) || ($this->mode == "specific") )
		{
			if( $this->diaporamaId && $this->mode == "specific")
				$id = (int)$this->diaporamaId;
			elseif( $page->diaporama == 0 )
				$id = (int)$this->diaporamaIdPage;
			elseif( $page->diaporama != 0 )
				$id = (int)$page->diaporama;
			elseif( $this->diaporamaIdPage )
				$id = (int)$this->diaporamaIdPage;
			else 
				$id = null;
			
			if( $id == null ){
				$this->noRenderBloc = true;
				return;
			}
			
			$diaporama 	= new Diaporama_Object_Diaporama($id);
			$items 			= $diaporama->nodes;
			$count 			= count($items);
			
			if ($count > 0) {
				$usePagination = $this->pagination ? "true" : "false";
				
				if (count($items) > 0) {
					$processLayout = CMS_Application_ProcessLayout::getInstance();
					$processLayout->appendJsFile("/lib/slideShow/jquery.slideShowMaison.js");
					$processLayout->appendJsScript("$('#diaporama.diaporama-".$id."').gallery({'usePagination': ".$usePagination."});");
				}
				
				$view->items = $items;
				
				$diaporama_size = json_decode(DIAPORAMA_SIZE, true);
				
				$view->diaporamaWidth = $diaporama_size[$diaporama->size]["width"];
				$view->diaporamaHeight = $diaporama_size[$diaporama->size]["height"];
				
				$view->datas = $this->toArray();
			}
			else {
				$this->noRenderBloc = true;
			}
		}
		else
		{
			$this->noRenderBloc = true;
			$view->items = array();
		}
	}
	
	public function save($post){
		

		$this->templateFront = 'bxslider';

		$this->fromArray($post);
		
		$id = parent::save($post);
		
		return $id;
	}
}
