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

class Bloc_Advertisingv2_Advertising extends CMS_Bloc_Abstract  implements CMS_Bloc_Interface {
	
	public $campaign;
	
	protected $_adminFormClass = "Bloc_Advertisingv2_AdminForm";
	
	protected static $_translatableFields = array();
	
	public function runtimeFront($view) {
		
		try {
			$campaign = new Advertising_Object_Campaign((int)$this->campaign);
			
			if ($campaign) {
				$now = time();
				
				if ($campaign->enable) {
					if ((!$campaign->limited) || ((strtotime($campaign->date_start)<$now) && ((strtotime($campaign->date_end)>$now)))) {
						$items = array();
						
						foreach ($campaign->nodes as $advert)
							$items[] = json_decode($advert->datas,true);
						
						$item = Advertising_Lib_Manager::findRandomWeighted($items);
						
						if (isset($item["type_link"]) && $item["type_link"] == 0) {
							$page = CMS_Page_Object::getOne(array('url_system' => $item["page_link"]));
							
							if($page)
								$item["page_link"] = $page->getUrl();
							else
								$this->noRenderBloc = true;
						}
					}
				}
			}
			
			$view->advert = $item;
		}
		catch (Exception $exc) {
			$view->error = 1;
		}
		
	}
	
	public function save($post) {
		
		$this->campaign = $post["campaign"];
		
		$id = parent::save($post);
		
		return $id;
	}
}