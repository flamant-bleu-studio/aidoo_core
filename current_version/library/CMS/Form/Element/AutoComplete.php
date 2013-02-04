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

class CMS_Form_Element_AutoComplete extends Zend_Form_Element_Text
{
	
	private $_options;
	
	public function __construct($field_name, $attributes = null) {
		
		// Default values
		$this->_options["minLength"] = 2;
		$this->_options["delay"] = 300;
		
    	parent::__construct($field_name, $attributes);
    }
    
    public function setAjax($_route)
    {
    	$this->_options["routeAjax"] = $_route;
    }
    
    public function setMinLengthStartComplete($_minLength)
    {
    	$this->_options["minLength"] = (int)$_minLength;
    }
    
	public function setDelay($_delay)
    {
    	$this->_options["delay"] = (int)$_delay; // en ms
    }
    
    public function render(Zend_View_Interface $view = null)
    {
    	if( empty($this->_options["routeAjax"]) )
	    	throw new Zend_Exception("Missing params 'routeAjax' for AutoComplete Form Element");
    	
	    // Documentation :
	    // http://jqueryui.com/demos/autocomplete/
	    
    	$processLayout = CMS_Application_ProcessLayout::getInstance();
		$processLayout->appendJsScript('
			
			var cache = {},
				lastXhr;
			$("#' . $this->_name . '").autocomplete({
				delay: ' . $this->_options["delay"] . ',
				minLength: ' . $this->_options["minLength"] . ',
				source: function( request, response ) {
					var term = request.term;
					if ( term in cache ) {
						response( cache[ term ] );
						return;
					}
					
					lastXhr = $.getJSON( "' . $this->_options["routeAjax"] . '", request, function( data, status, xhr ) {
						cache[ term ] = data;
						if ( xhr === lastXhr ) {
							response( data );
						}
					});
				}
			});
			
		');
		
		return parent::render($view);
    }
	
}