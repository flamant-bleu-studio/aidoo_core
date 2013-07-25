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

class Advertising_BackController extends CMS_Controller_Action
{
	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_advertising", "view"))
		{
			$campains = Advertising_Object_Campaign::get();
			
			$activeCampaigns = array();
			$archivedCampaigns = array();
			
			$now = time();
			
			if( count($campains) > 0 )
			{
				foreach ($campains as $campain)
				{					
					if ( (!$campain->limited) || ((strtotime($campain->date_start)<$now)&&((strtotime($campain->date_end)>$now))) )
						$activeCampaigns[] = $campain; 
					else
						$archivedCampaigns[] = $campain;
				}
			}
			
			$this->view->activeCampaigns = $activeCampaigns;
			$this->view->archivedCampaigns = $archivedCampaigns;
			
			if($backAcl->hasPermission("mod_advertising", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_advertising");
				$formAcl->setAction($this->_helper->route->short('updateAcl'));
				$formAcl->addSubmit(_t("Submit"));
				
		    	$this->view->formAcl = $formAcl;
			}			
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function createAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_advertising", "create"))
		{
			$form_campaign = new Advertising_Form_Campaign();
			$form_campaign->setAction($this->_helper->route->short('create'));
			
			$form_advert = new Advertising_Form_Advert(array("id" => "formAdvert"));
			
			if($this->getRequest()->isPost())
			{
				if($form_campaign->isValid($_POST))
				{
					$campaign = new Advertising_Object_Campaign();
					
					$campaign->title 	= $form_campaign->getValue("title");
					$campaign->limited 	= $form_campaign->getValue("limited");
					$campaign->enable 	= 0;
					
					if( $form_campaign->getValue("date_start") != "" && $form_campaign->getValue("limited"))
						$campaign->date_start = CMS_Application_Tools::_convertDateTimePickerToUs($form_campaign->getValue("date_start"));
					else
						$campaign->date_start = null;
					if( $_POST["date_start"] != "" && $_POST["limited"])
						$campaign->date_end = CMS_Application_Tools::_convertDateTimePickerToUs($form_campaign->getValue("date_end"));
					else
						$campaign->date_end = null;
					
					$datas = array();
					$datas['nodes'] = Advertising_Lib_Manager::createArrayAdvert($form_campaign->getValue("datas"));
					
					$campaign->fromArray($datas);
					
					/** Enregistrement **/
					$id = $campaign->save();
					
					/** Permissions **/
					if($_POST['ACL'])
		            	$backAcl->addPermissionsFromAclForm("mod_advertising-".$id, $_POST['ACL']);
					else 
						$backAcl->addPermissionsFromDefaultAcl("mod_advertising-".$id, "mod_advertising-default");
					
					_message(_t('Campaign of advert created'));
					
					if($_POST['submitandquit'])
						$this->_redirect($this->_helper->route->short('index'));
					
					$this->_redirect($this->_helper->route->short('edit', array('id' => $id)));

				}
				else
					_error(_t('invalid form'));
			}
			
			if($backAcl->hasPermission("mod_advertising", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_advertising");
				$form_campaign->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
			
			$this->view->formCampaign = $form_campaign;
			$this->view->formAdvert = $form_advert;
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function editAction()
	{
		$id = intval($this->_request->getParam('id'));
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_advertising-".$id, "edit"))
		{
			$campaign = new Advertising_Object_Campaign($id);
			$datas = $campaign->toArray();
			
			$form_campaign = new Advertising_Form_Campaign();
			
			$form_advert = new Advertising_Form_Advert(array("id" => "formAdvert"));
			
			if($this->getRequest()->isPost()) {
				if($form_campaign->isValid($_POST)) {					

					$campaign->title 	= $form_campaign->getValue("title");
					$campaign->limited 	= $form_campaign->getValue("limited");
					
					if( $form_campaign->getValue("date_start") != "" && $form_campaign->getValue("limited"))
						$campaign->date_start = CMS_Application_Tools::_convertDateTimePickerToUs($form_campaign->getValue("date_start"));
					else
						$campaign->date_start = null;
					if( $form_campaign->getValue("date_start") != "" && $form_campaign->getValue("limited"))
						$campaign->date_end = CMS_Application_Tools::_convertDateTimePickerToUs($form_campaign->getValue("date_end"));
					else
						$campaign->date_end = null;
					
					$datas = array();
					$datas["nodes"] = Advertising_Lib_Manager::createArrayAdvert($form_campaign->getValue("datas"));

					$campaign->fromArray($datas);
					
					/** Enregistrement **/
					$campaign->save();
					
					_message(_t('Campaign of advert saved'));
					
					if($_POST['submitandquit'])
						$this->_redirect($this->_helper->route->short('index'));
					
					$this->_redirect($this->_helper->route->short('edit', array('id' => $id)));
				}
				else
					_error(_t('invalid form'));
			}
			
			if($backAcl->hasPermission("mod_advertising", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_advertising");
				$form_campaign->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
			
			if ( count($campaign->nodes) > 0 )
			{
				$decoded = array();
				$i = 0;
				foreach ( $campaign->nodes as  $advert )
				{
					$temp_advert = json_decode($advert->datas, true);
					$i++;
					$decoded[$i]["image_path"] 			= $temp_advert["image_path"];
					$decoded[$i]["image_path_thumb"] 	= $temp_advert["image_path_thumb"];
					$decoded[$i]["image_width"] 		= $temp_advert["image_width"];
					$decoded[$i]["image_height"] 		= $temp_advert["image_height"];
					$decoded[$i]["image_thumb_width"] 	= $temp_advert["image_thumb_width"];
					$decoded[$i]["image_thumb_height"] 	= $temp_advert["image_thumb_height"];
					$decoded[$i]["window"] 				= ($temp_advert["window"]) ? "1" : "";
					$decoded[$i]["link_type"] 			= $temp_advert["link_type"];
					$decoded[$i]["external_page"] 		= $temp_advert["external_page"];
					$decoded[$i]["page_link"] 			= $temp_advert["page_link"];
					$decoded[$i]["weight"] 				= $temp_advert["weight"];
					$decoded[$i]["addtext"] 			= $temp_advert["addtext"];
					$decoded[$i]["text"] 				= $temp_advert["text"];
				}
				unset($datas["datas"]);
				$datas["datas"] = json_encode($decoded);
			}
			
			if( $datas["limited"] && $datas["date_start"])
				$datas["date_start"] = CMS_Application_Tools::_convertDateTimeUsToPicker($datas["date_start"]);
			if( $datas["limited"] && $datas["date_end"])
				$datas["date_end"] = CMS_Application_Tools::_convertDateTimeUsToPicker($datas["date_end"]);

			$form_campaign->setAction($this->_helper->route->short('edit', array('id'=>$id)));
			$form_campaign->populate((array)$datas);
			
			$this->view->formCampaign = $form_campaign;
			$this->view->formAdvert = $form_advert;
			
			$this->view->decoded = $decoded;
			$this->view->limitedCampaign = $datas["limited"];
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function deleteAction()
	{
		$id = intval($this->_request->getParam('id'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_advertising-".$id, "delete"))
		{
			$campaign = new Advertising_Object_Campaign($id);
			$campaign->delete();
			
			$backAcl->deletePermissions("mod_advertising-".$id);
			
			_message(_t('Compaign of advert deleted'));
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function enableAction()
	{
		$id = intval($this->_request->getParam('id'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_advertising-".$id, "edit"))
		{
			$campaign = new Advertising_Object_Campaign($id);
			
			$campaign->enable = 1;
			$campaign->nodes;
			$campaign->save();
			
			_message(_t('Compaign of advert is enable'));
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function disableAction()
	{
		$id = intval($this->_request->getParam('id'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_advertising-".$id, "edit"))
		{
			$campaign = new Advertising_Object_Campaign($id);
			
			$campaign->enable = 0;
			$campaign->nodes;
			$campaign->save();
			
			_message(_t('Compaign of advert is disable'));
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function updateaclAction()
	{
		if($this->getRequest()->isPost()) 
		{
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->updatePermissionsFromAclForm("mod_advertising", $_POST['ACL']))
				_message(_t("Rights updated"));
			else 
				_error(_t("Insufficient rights"));
		}
		return $this->_redirect( $this->_helper->route->short('index'));
	}
}