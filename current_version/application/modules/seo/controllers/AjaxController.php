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

class Seo_AjaxController extends Zend_Controller_Action
{
	public function updatepageAction(){

		$result = array();

		try{
				
			$result['error'] = false;
			$pageForm = new Seo_Form_Page();

			if($pageForm->isValid($_POST)){
				 
				$values = $pageForm->getValues();
				$page = CMS_Page_Object::get((int)$values["id"]);
				 
				$mdl = new CMS_Page_Model();
				if( ($mdl->getFromUri($values["url_rewrite"]) != null) && ($values["url_rewrite"] != $page->url_rewrite) && (trim($values["url_rewrite"]) != "") )
				{
					$result['error'] 	= true;
					$result['field'] 	= "url_rewrite";
					$result['message'] 	= _t("This URI already exist");
				}
				else
				{
					$page->title = (trim($values["title"]) != "") ? $values["title"] : null;
					$page->url_rewrite = (trim($values["url_rewrite"]) != "") ? $values["url_rewrite"] : null;
					$page->meta_keywords = (trim($values["meta_keywords"]) != "") ? $values["meta_keywords"] : null;
					$page->meta_description = (trim($values["meta_description"]) != "") ? $values["meta_description"] : null;

					$page->save();
				}
			}
			 
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}	
		
		echo json_encode($result);
	}
	public function enablepageAction(){

		$result = array();
		
		try{
	    	$page = CMS_Page_Object::get((int)$_POST["id"]);
	    	$page->enable();

	    	$result['error'] = false;
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}	
		
		echo json_encode($result);
	}
	public function disablepageAction(){

		$result = array();
		
		try{
	    	$page = CMS_Page_Object::get((int)$_POST["id"]);
	    	$page->disable();
		    
			$result['error'] = false;
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}	
		
		echo json_encode($result);
	}
	
	public function updatetemplateAction(){
		
		$result = array();
		
		try
		{
			$type 	= (int)$_POST["id"];
			$tpl_id = (int)$_POST["tpl_id"];
			
			if(!isset($type) || !isset($tpl_id) || $type === 0)
				throw new Zend_Exception(_t('Missing parameter'));
				
			if(!$tpl_id)
				$tpl_id = null;

			$type = new CMS_Page_Type($type);
			$type->default_tpl = $tpl_id;
			$type->save();
			
			$result['error'] = false;
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}	
		
		echo json_encode($result);
	}
	
	public function updatediaporamaAction() {
		$result = array();
		
		try
		{
			$id = (int)substr($_POST["id"], 5);
			
			$page = CMS_Page_Object::get($id);
			
			if( $_POST["diapo_id"] == "null" )
				$page->diaporama = null; // Aucun
			else 
				$page->diaporama = $_POST["diapo_id"]; // 0 => par default du bloc ; X => id diaporama
			
			$page->save();
			
			$result['error'] = false;
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}	
		
		echo json_encode($result);
	}

    public function getexternallistlinkAction()
    {       
    	$auth = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()) 
			die();
				
    	//$types = CMS_Page_Object::getAllPagesByType(array("enable" => "1", "visible" => "1", "sort"=>"title"));
		$pages = CMS_Page_Object::get(array("enable" => "1", "visible" => "1"));
		$types = (array)CMS_Page_Type::get();
		
		$tmp = array();
		foreach($types as $type){
			$tmp[$type->type] = $type->toArray();
		}
		$types = $tmp;
		
		// Remplissage de chaque type avec leurs pages
		if( $pages ) {
			foreach($pages as $page) {
				if(!$types[$page->type])
				continue;
				else
				$types[$page->type]["pages"][] = $page;
			}
		}
		
		// Make output a real JavaScript file!
		header('Content-type: text/javascript'); // browser will now recognize the file as a valid JS file
		
		// prevent browser from caching
		header('pragma: no-cache');
		header('expires: 0'); // i.e. contents have already expired
		
		echo "var tinyMCELinkList = new Array(";
		
		$start = true;

		foreach($types as $key => $type)
		{
			if (isset($type["pages"])){
				foreach($type["pages"] as $page)		
				{
					if(!$start)
						echo ",";
					else
						$start = false;
					
					echo '["['.$key.'] '.addslashes($page->title).'" , "'.BASE_URL.$page->getUrl().'"]'."\n";
				}
			}
		}
		
		echo ");";
    }
}

