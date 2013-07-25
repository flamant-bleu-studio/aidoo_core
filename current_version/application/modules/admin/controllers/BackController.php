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

class Admin_BackController extends CMS_Controller_Action
{

    public function indexAction()
    {
		//Statistiques sur le tableau de bord		
		$config = CMS_Application_Config::getInstance();
		$campaign = json_decode($config->get("social"));
		
		$account = $campaign->googleaccount;
		$password = $campaign->googlepassword;
		//$profile = $campaign->googleprofile;
		$ua = $campaign->googleanalytics;
			
		if($account && $password && $ua){
				
			$todayDate = date("Y-m-d");// current date
			$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($todayDate)) . "-1 month"));
			
			try {
				$ga = new Statistics_Lib_Gapi($account, $password);

				
				$ga->requestAccountData();
			
				$accountId = null;
				$profileSet = null;
				foreach($ga->getResults() as $result)
				{		  		
				  if($result->getWebPropertyId() == $ua){
				  	$profileSet = $result;
				    $accountId = $result->getProfileId();
				 	break;
				  }
				}
				
				if(!$accountId)
					$this->view->account = false;
				else{
					/**
					 * @todo Revoir ce code ;)
					 */
					
					$this->view->account = true;
					$this->view->site = $profileSet->getTitle();	  
					
					$ga->requestReportData($accountId, null, array('pageviews','visits','pageviewsPerVisit','avgTimeOnSite','visitBounceRate','newVisits'), null, null, $date, $todayDate);
					
					
					$tabTemps = array("j" => 86400,
					"h" => 3600,
					"min" => 60,
					"sec" => 1);
					
					$result = "";
					$time = 0;
					
					foreach($tabTemps as $uniteTemps => $nombreSecondesDansUnite) {
						$$uniteTemps = floor($time/$nombreSecondesDansUnite);
						$time = $ga->getAvgTimeOnSite()%$nombreSecondesDansUnite;
					
						if($$uniteTemps > 0 || !empty($result))
							$result .= $$uniteTemps." $uniteTemps ";
					}
					
					$this->view->visits = $ga->getVisits();
					$this->view->pageviews = $ga->getPageViews();
					$this->view->pageviewsPerVisit = number_format( $ga->getPageviewsPerVisit(), 2);
					$this->view->timeOnSite = $result;
					
					$this->view->start = $ga->getPageViews();
					$this->view->end = $ga->getPageViews();
					
					$this->view->bounceVisit = number_format( $ga->getVisitBounceRate(), 2);
					$this->view->newsVisit = $ga->getNewVisits();
				}
				
			}catch(Exception $e){
				$this->view->account = false;
				$this->view->errorAuthAnalytics = $e->getMessage();
			}
			
		}
		else 
			$this->view->account = false;
				
		$moduleExistActive = array("galerieImage" => 1, "advertising" => 1, "documents" => 1, "users" => 1, "blocs" => 1, "skins" => 1);
		
		foreach ($moduleExistActive as $key => $module)
		{
			$jobsModuleFolderExists = realpath(dirname(__FILE__).'/../../'.$key.'/');
			if ( !$jobsModuleFolderExists || !file_exists($jobsModuleFolderExists."/Bootstrap.php"))
			{
				$moduleExistActive[$key] = 0;
			}
		}
		$this->view->moduleExistActive = $moduleExistActive;
    }

    public function changemultiAction()
    {
    	$id = $this->_request->getParam('id');
    	
		global $multi_site_prefix;
		
		$multi_site_prefix_prev = $multi_site_prefix;
		$multi_site_prefix 	= $id."_";
		
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("admin", "login"))
		{		
			if($id == MULTI_SITE_ID)
			{
				setcookie("multi_site");
			}
			else 
			{
				setcookie("multi_site[change]"	, 1, 0, "/administration");
				setcookie("multi_site[id]"		, $id, 0, "/administration");
			}
		}
		else 
		{
			$multi_site_prefix = $multi_site_prefix_prev;
		}

		return $this->_redirect($this->_helper->route->full('admin'));
    }

}
	


