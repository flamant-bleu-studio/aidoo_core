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

class CMS_Controller_Plugin_GoogleAnalytics extends CMS_Controller_Plugin_Abstract_Abstract {
	    
	/**
	 *  Init Google analytics 
	 */    
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$this->initVarEnv($request);
    	
    	if(!$this->_isAdmin && !$this->_isLoggin && !$this->_isAjax && !$this->_isError || defined("NOTFOUND")) {
	    	$socialConfig = CMS_Application_Config::getInstance()->get("social");
	    	if ($socialConfig)
	    	{
	    		$data = json_decode($socialConfig,true);
	    		$GoogleAnalyticsAccount = $data['googleanalytics'];
	    		
	    		if ($GoogleAnalyticsAccount)
	    			$this->_view->assign("GoogleAnalyticsAccount", $GoogleAnalyticsAccount);
	    	}
    	}
    }    
}
