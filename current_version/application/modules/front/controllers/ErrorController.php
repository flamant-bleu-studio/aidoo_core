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

class Front_ErrorController extends CMS_Controller_Action
{	
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $this->view->error = true;
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                header("HTTP/1.0 404 Not Found");
                $this->view->message = 'Page demandée non trouvée';
                break;
            default:
                // application error
                header("HTTP/1.1 500 Internal Server Error");
                $this->view->message = 'Application error';
                $this->view->codeError = 5;
                break;
        }
        
        CMS_Log::err($errors->exception->getMessage() . ' - ' . $errors->exception);
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true)
            $this->view->exception = $errors->exception;
        
        $this->view->request   = $errors->request;
        
        $dump = print_r($this->_request->getParams(), true);
        
        $this->view->dump_request = substr($dump, 0, 20000);
    }
    
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

    /*
     * Error pour le server REST 
     * */
    public function errorapiauthAction()
    {
    	$this->getResponse()->setHttpResponseCode(401);
		$arrayError['error'] = true;
		$arrayError['message'] = "Request unauthorised";
    	$this->_helper->json($arrayError);	
    }
}
