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

class Blocs_AjaxController extends Zend_Controller_Action
{
	public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    
	public function updatetemplateAction(){

		$result = array();

		try
		{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()) 
			{
				$backAcl = CMS_Acl_Back::getInstance();
				if ($backAcl->hasPermission("mod_bloc", "editTemplates"))
				{		
					if( (int)$_POST['tpl_id'] ){
						
					    $template = new Blocs_Object_Template((int)$_POST["tpl_id"]);
						
					    $positions = array();
					    if( $_POST['datas']['data'] ) {
						    foreach($_POST['datas']['data'] as $type => $placeholders){
						    	foreach($placeholders as $ph => $blocs){
							    	foreach($blocs as $bloc){
							    		$positions[$type][$ph][] = $bloc['id'];
							    	}
						    	}
						    }
					    }
					    
					    $template->setItemsPosition($positions);
						
					    $template->save();
					}
				    
					$result['error'] = false;
				}
				else
					throw new Zend_Exception(_t("Insufficient rights"));
			}
			else
				throw new Zend_Exception(_t("You are not logged. Please retry this page and log in."));
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}	
		
		echo json_encode($result);
	}
	
	public function newtemplateAction(){

		$result = array();

		try
		{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()) 
			{
				$backAcl = CMS_Acl_Back::getInstance();
				if ($backAcl->hasPermission("mod_bloc", "createTemplates"))
				{
					$template = new Blocs_Object_Template();
					
					$template->title = htmlspecialchars($_POST["title"]);
		
					$id = $template->save();
					
					if( $_POST["duplicate"] == "checked" && $_POST["template_id_duplicate"] ) {
						$template->importFromOtherTemplate((int)$_POST["template_id_duplicate"]);
					}
				
					$result['id'] = $id;
					$result['error'] = false;
				}
				else
					throw new Zend_Exception(_t("Insufficient rights"));
			}
			else
				throw new Zend_Exception(_t("You are not logged. Please retry this page and log in."));
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}
		
		echo json_encode($result);
	}
	
	public function changedefaultAction()
	{

		$result = array();

		try
		{
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()) 
			{
				$backAcl = CMS_Acl_Back::getInstance();
				if ($backAcl->hasPermission("mod_bloc", "editTemplates"))
				{
					$id = (int)$_POST["tpl_id"];
					
					$return = Blocs_Object_Template::setDefault($id);
					
					$result['id'] = $id;
					$result['error'] = false;
				}
				else 
					throw new Zend_Exception(_t("Insufficient rights"));
			}
			else
				throw new Zend_Exception(_t("You are not logged. Please reload this page and log in."));
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}
		
		echo json_encode($result);
	}
	
}

