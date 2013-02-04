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

class CMS_Rest_Route extends Zend_Rest_Route
{   
    /**
     * Matches a user submitted request. Assigns and returns an array of variables
     * on a successful match.
     *
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param Zend_Controller_Request_Http $request Request used to match against this routing ruleset
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($request, $partial = false)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            $request = $this->_front->getRequest();
        }
        $this->_request = $request;
        $this->_setRequestKeys();

        $path   = $request->getPathInfo();
        $params = $request->getParams();
        $values = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);
            
            //Determine Environment
            //print_r($path[0]);die;
            if($path[0] != 'api') {
            	return false;
            } else {
            	array_shift($path);
            }
            
            // Determine Module
            $moduleName = $this->_defaults[$this->_moduleKey];
            $dispatcher = $this->_front->getDispatcher();
            if ($dispatcher && $dispatcher->isValidModule($path[0])) {
                $moduleName = $path[0];
                if ($this->_checkRestfulModule($moduleName)) {
                    $values[$this->_moduleKey] = array_shift($path);
                    $this->_moduleValid = true;
                }
            }
            
            // Determine Controller
            $controllerName = $this->_defaults[$this->_controllerKey];
            if (count($path) && !empty($path[0])) {
                if ($this->_checkRestfulController($moduleName,  'api_' . $path[0])) {
                    $controllerName = 'api_' . $path[0];
                    $values[$this->_controllerKey] =  'api_' . array_shift($path);
                    $values[$this->_actionKey] = 'get';
                } else {
                    // If Controller in URI is not found to be a RESTful
                    // Controller, return false to fall back to other routes
                    return false;
                }
            } elseif ($this->_checkRestfulController($moduleName, $controllerName)) {
                $values[$this->_controllerKey] = $controllerName;
                $values[$this->_actionKey] = 'get';
            } else {
                return false;
            }

            //Store path count for method mapping
            $pathElementCount = count($path);

            // Check for "special get" URI's
            $specialGetTarget = false;
            if ($pathElementCount && array_search($path[0], array('index', 'new')) > -1) {
                $specialGetTarget = array_shift($path);
            } elseif ($pathElementCount && $path[$pathElementCount-1] == 'edit') {
                $specialGetTarget = 'edit';
                $params['id'] = urldecode($path[$pathElementCount-2]);
            } elseif ($pathElementCount == 1) {
                $params['id'] = urldecode(array_shift($path));
            } elseif ($pathElementCount == 0 && !isset($params['id']) && empty($_POST)) {
                $specialGetTarget = 'index';
            }
            
            //Redirect to action if id is not numeric
            if (isset($params['id']) && !is_numeric($params['id'])) {
            	$specialGetTarget = $params['id'];
            	$params['id'] = null;
            }
            
            // Digest URI params
            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? $path[$i + 1] : null;
                    $params[$key] = urldecode($val);
                }
            }
           
            // Determine Action
            $requestMethod = strtolower($request->getMethod());
            if ($requestMethod != 'get' && !$specialGetTarget) {
                if ($request->getParam('_method')) {
                    $values[$this->_actionKey] = strtolower($request->getParam('_method'));
                } elseif ( $request->getHeader('X-HTTP-Method-Override') ) {
                    $values[$this->_actionKey] = strtolower($request->getHeader('X-HTTP-Method-Override'));
                } else {
                    $values[$this->_actionKey] = $requestMethod;
                }
                
                // Map PUT and POST to actual create/update actions
                // based on parameter count (posting to resource or collection)
                switch( $values[$this->_actionKey] ){
                    case 'post':
                        if ($pathElementCount > 0) {
                            $values[$this->_actionKey] = 'put';
                        } else {
                            $values[$this->_actionKey] = 'post';
                        }
                        break;
                    case 'put':
                        $values[$this->_actionKey] = 'put';
                        break;
                }

            } elseif ($specialGetTarget) {
                $values[$this->_actionKey] = $specialGetTarget;
            }

        }
        $this->_values = $values + $params;

        $result = $this->_values + $this->_defaults;
        
        if ($partial && $result)
            $this->setMatchedPath($request->getPathInfo());
        
        return $result;
    }
}
