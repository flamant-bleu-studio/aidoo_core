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

class Bloc_GaleriePhotov2_GaleriePhoto extends CMS_Bloc_Abstract  implements CMS_Bloc_Interface {
	
	public $diaporamaId;
	public $diaporamaIdPage;
	public $paginationActive;
	public $typeDiapo;
	protected $_adminFormClass = "Bloc_GaleriePhotov2_AdminForm";
	protected static $_translatableFields = array();
	
	public function runtimeFront($view){
        $page = CMS_Page_Current::getInstance();
		if( $page && ($this->typeDiapo == "diaporamaPage" && $page->diaporama) || ($this->typeDiapo == "diaporama") )
		{
			if( $this->diaporamaId && $this->typeDiapo == "diaporama")
				$id = $this->diaporamaId;
			elseif( $page->diaporama == 0 )
				$id = $this->diaporamaIdPage;
			elseif( $page->diaporama != 0 )
				$id = $page->diaporama;
			elseif( $this->diaporamaIdPage )
				$id = $this->diaporamaIdPage;
			else 
				$id = null;
			if( $id == null ){
				$this->noRenderBloc = true;
				return;
			}
			
			try {
				$galerieObject = new GalerieImage_Object_Galerie((int)$id);			
			}
			catch(Exception $e){
				$this->noRenderBloc = true;
				return;
			}
			
			$galerie = $galerieObject->toArray();
			$items = $galerieObject->nodes;
           // $items = GalerieImage_Lib_Manager::unsetInactiveImage($items);
			$count = count($items);
			if( $count > 0 )
			{
				//$usePagination = $this->paginationActive ? "true" : "false";
				//$processLayout = CMS_Application_ProcessLayout::getInstance();
				//$processLayout->appendJsFile("/lib/slideShow/jquery.slideShowMaison.js");
				//$processLayout->appendJsScript("$('#diaporama').gallery({'usePagination': ".$usePagination."});");
				$return = array();
				foreach ($items as $item)
				{
					$temp = array();
					
					$datas = json_decode($item->datas, true);
					
					$temp["addLink"] = $datas["addLink"] ? 1 : 0;
					
					if( $datas["link_type"] == 1 )
						$temp["url"] = "http://".$datas["external_page"];
					else if ( $datas["link_type"] == 0 )
					{
						$rewrite = CMS_Page_Object::get($datas["page_link"]);
						if( $rewrite->url_rewrite )
							$url = BASE_URL."/".$rewrite->url_rewrite;
						else
							$url = BASE_URL."/".$rewrite->url_system;
						
						$temp["url"] = $url;
					}
					$temp["window"] = $datas["window"] ? 1 : 0;
					$temp["image"] = $item->path;
                    $temp["thumb"] = $item->path_thumb;
					$temp["description"] = urldecode($item->description);
					
					$return[] = $temp;
				}
			}
			else {
				$this->noRenderBloc = true;
			}
			
			$view->diaporamaWidth = $galerie["width"];
			$view->paginationActive = $this->paginationActive;
			$view->items = $return;
			
			$diaporama_size = json_decode(DIAPORAMA_SIZE, true);
			
			$view->diaporamaWidth = $diaporama_size[$galerie["size"]]["width"];
			$view->diaporamaHeight = $diaporama_size[$galerie["size"]]["height"];
		}
		else
			$this->noRenderBloc = true;
	}
	
	public function save($post){
		
		$this->paginationActive = $post["paginationActive"];
		
		$this->typeDiapo = $post["typeDiapo"];
		
		if($post["typeDiapo"] == "diaporama") {
			$this->diaporamaId = $post["diaporamaId"];
			$this->diaporamaIdPage = null;
		}
		elseif($post["typeDiapo"] == "diaporamaPage") {
			$this->diaporamaId = null;
			if( $post["diaporamaIdPage"] == "null" )
				$this->diaporamaIdPage = null;
			else
				$this->diaporamaIdPage = $post["diaporamaIdPage"];
		}
		$id = parent::save($post);
		return $id;
	}
}
