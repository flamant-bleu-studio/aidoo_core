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

class CMS_Controller_Front extends Zend_Controller_Front
{
	
	/**
	* Instance of Zend_Controller_Request_Abstract
	* @var Zend_Controller_Request_Abstract
	*/
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	/**
     * Dispatch an HTTP request to a controller/action.
     *
     * @param Zend_Controller_Request_Abstract|null $request
     * @param Zend_Controller_Response_Abstract|null $response
     * @return void|Zend_Controller_Response_Abstract Returns response object if returnResponse() is true
     */
    public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
    {   	
        if (!$this->getParam('noErrorHandler') && !$this->_plugins->hasPlugin('Zend_Controller_Plugin_ErrorHandler')) {
            // Register with stack index of 100
            require_once 'Zend/Controller/Plugin/ErrorHandler.php';
            $this->_plugins->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(), 100);
        }
        
        if (!$this->getParam('noViewRenderer') && !Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
            Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-80, new Zend_Controller_Action_Helper_ViewRenderer());
        }

        /**
         * Instantiate default request object (HTTP version) if none provided
         */
        if (null !== $request) {
            $this->setRequest($request);
        } elseif ((null === $request) && (null === ($request = $this->getRequest()))) {
            require_once 'CMS/Controller/Request/Http.php';
            $request = new CMS_Controller_Request_Http();
            $this->setRequest($request);
        }
        
        /**
         * Set base URL of request object, if available
         */
        if (is_callable(array($this->_request, 'setBaseUrl'))) {
            if (null !== $this->_baseUrl) {
                $this->_request->setBaseUrl($this->_baseUrl);
            }
        }
        
        /**
         * Instantiate default response object (HTTP version) if none provided
         */
        if (null !== $response) {
            $this->setResponse($response);
        } elseif ((null === $this->_response) && (null === ($this->_response = $this->getResponse()))) {
            require_once 'Zend/Controller/Response/Http.php';
            $response = new Zend_Controller_Response_Http();
            $this->setResponse($response);
        }
        
        /**
         * Initialize plugin 
         */
        $this->loadPlugin();
        
        /**
         * Register request and response objects with plugin broker
         */
        $this->_plugins
             ->setRequest($this->_request)
             ->setResponse($this->_response);

        /**
         * Initialize router
         */
        $router = $this->getRouter();
        $router->setParams($this->getParams());
        
        /**
         * Initialize dispatcher
         */
        $dispatcher = $this->getDispatcher();
        $dispatcher->setParams($this->getParams())
                   ->setResponse($this->_response);
        
        // Begin dispatch
        try {
            /**
             * Route request to controller/action, if a router is provided
             */

            /**
            * Notify plugins of router startup
            */
            $this->_plugins->routeStartup($this->_request);

            try {
                $router->route($this->_request);
            }  catch (Exception $e) {
                if ($this->throwExceptions()) {
                    throw $e;
                }

                $this->_response->setException($e);
            }

            /**
            * Notify plugins of router completion
            */
            $this->_plugins->routeShutdown($this->_request);

            /**
             * Notify plugins of dispatch loop startup
             */
            $this->_plugins->dispatchLoopStartup($this->_request);
            
            /**
             *  Attempt to dispatch the controller/action. If the $this->_request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->_request->setDispatched(true);
                
                /**
                 * Notify plugins of dispatch startup
                 */
                $this->_plugins->preDispatch($this->_request);
                
                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$this->_request->isDispatched()) {
                    continue;
                }
               
                /**
                 * Dispatch request
                 */
                try {
                   $dispatcher->dispatch($this->_request, $this->_response); 
                } catch (Exception $e) {
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->_response->setException($e);
                }
                
                /**
                 * Notify plugins of dispatch completion
                 */
                $this->_plugins->postDispatch($this->_request);
            } while (!$this->_request->isDispatched());
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $this->_response->setException($e);
        }
        
        /**
         * Notify plugins of dispatch loop completion
         */
        try {
            $this->_plugins->dispatchLoopShutdown();
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $this->_response->setException($e);
        }

        if ($this->returnResponse()) {
            return $this->_response;
        }
       
        $hooks = CMS_Application_Hook::getInstance()->exec_actions("CMS_Front_BeforeDisplayHttp");
        $this->_response->sendResponse();
    }    
    
    /**
     * 
     * Load plugin 
     * @TODO link this with DB to 'enable' or 'disable' some plugin from backoffice
     */
    public function loadPlugin()
    {
    	// Ce champs n'existe pas encore en bdd, il sera utilisé pour checker quels plugins sont actif
//     	$listCMSPlugin = json_decode(CMS_Application_Config::getInstance()->get('activeCMSPlugins'));

//     	$this->registerPlugin(new CMS_Acl_Plugins_CheckRights());
    	
//     	foreach ($listCMSPlugin as $path => $name)
//     		$this->registerPlugin(new $path());
    	
//     	$this->registerPlugin(new CMS_Controller_Plugin_ApiAuth());
    	
//     	->registerPlugin(new CMS_Controller_Plugin_ProcessAdmin())
//     	->registerPlugin(new CMS_Controller_Plugin_LangSelector())
//     	->registerPlugin(new CMS_Controller_Plugin_ProcessSEO())
//     	->registerPlugin(new CMS_Controller_Plugin_Maintenance())
//     	->registerPlugin(new CMS_Controller_Plugin_GoogleAnalytics())
//     	->registerPlugin(new CMS_Controller_Plugin_LoadSkin());
    	
    }  
}