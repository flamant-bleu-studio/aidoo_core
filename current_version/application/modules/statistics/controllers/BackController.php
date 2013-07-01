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

class Statistics_BackController extends Zend_Controller_Action
{
	public function indexAction()
	{

		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if($backAcl->hasPermission("mod_statistics", "view"))
		{
			$config = CMS_Application_Config::getInstance();
			$campaign = json_decode($config->get("social"));
			
			if(isset($campaign->googleaccount) && isset($campaign->googlepassword) && isset($campaign->googleprofile)) 
			{
				/* defaults */
				$month = date('n');
				$year = date('Y');
				
				/* submission? */
				if($_GET['month'] || $_GET['year']) {
				  /* cleanse lookups */
				  $month = (int) $_GET['month']; if(!$month) { $month = 1; }
				  $year = (int) $_GET['year']; if(!$year) { $year = date('Y'); }
				}
				
				$tStats = Statistics_Lib_Manager::renderStats($campaign->googleaccount, $campaign->googlepassword, $campaign->googleprofile, $month, $year);
				
				$this->view->site = $tStats['site'];	  
				$this->view->flot_data_visits = $tStats['flot_data_visits'];
				$this->view->flot_data_views = $tStats['flot_data_views'];
	
				$this->view->visits = $tStats['visits'];
				$this->view->pageviews = $tStats['pageviews'];
				$this->view->pageviewsPerVisit = $tStats['pageviewsPerVisit'];
				$this->view->timeOnSite = $tStats['timeOnSite'];
				$this->view->start = $tStats['start'];
				$this->view->end = $tStats['end'];
				$this->view->month = $month;
				$this->view->year = $year;
			}
			
			if($backAcl->hasPermission("mod_statistics", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_statistics");
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
	
	public function updateaclAction()
	{
		if($this->getRequest()->isPost()) 
		{
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->updatePermissionsFromAclForm("mod_statistics", $_POST['ACL']))
				_message(_t("Rights updated"));
			else 
				_error(_t("Insufficient rights"));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
}