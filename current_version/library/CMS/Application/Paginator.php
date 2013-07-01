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

class CMS_Application_Paginator
{
	public $byPage = 10;
	public $nbItems;
	
	private $_currentPage;
	private $_countPages;
	
	private $_routeParams;
	private $_url;
	
	private $_linkAttr = array();
	private $_linkClass = array();
	
	private $_generatedLinkAttr;
	private $_generatedLinkClass;
	
	private $_routeHelper;
	
	public function __construct()
	{
		$this->_routeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
	}
	
	/**
	 * Values: null, right, centered
	 */
	private $_alignment;
	/**
	 * Values: null, large, small, mini
	 */
	private $_size;
	
	/**
	 * Values: default, ellipsis
	 */
	private $_typeRender;
	
	public function setAlignment($alignment) {
		$this->_alignment = $alignment;
	}
	
	public function setSize($size) {
		$this->_size = $size;
	}
	
	public function setTypeRender($type) {
		$this->_typeRender = $type;
	}
	
	protected function getClassPagination()
	{
		$return = 'pagination';
		
		if ($this->_alignment == 'right' || $this->_alignment == 'centered')
			$return .= ' pagination-'.$this->_alignment;
		
		if ($this->_size == 'large' || $this->_size == 'small' || $this->_size == 'mini')
			$return .= ' pagination-'.$this->_size;
		
		return $return;
	}
	
	protected function getUrl()
	{
		if (!empty($this->_url))
			return BASE_URL.$this->_url.'/page-'.$this->_routeParams['page'];
		
		return BASE_URL.$this->_routeHelper->full($this->_routeParams["route"], $this->_routeParams);
	}
	
	protected function getPrevious()
	{
		$return = '';
		
		$this->_routeParams["page"] = ($this->_currentPage-1 > 1) ? $this->_currentPage-1 : 1;
		
		$return .= '<li class="previous'.($this->_currentPage == 1 ? ' disabled' : '').'"><a href="'.$this->getUrl().'" '.$this->_generatedLinkAttr.' class="'.$this->_generatedLinkClass.'" ><</a></li>';
		
		return $return;
	}
	
	protected function getNext()
	{
		$return = '';
		
		$this->_routeParams["page"] = ($this->_currentPage+1 <= $this->_countPages) ? $this->_currentPage+1 : $this->_countPages;
		
		$return .= '<li class="next'.($this->_currentPage == $this->_countPages ? ' disabled' : '').'"><a href="'.$this->getUrl().'" '.$this->_generatedLinkAttr.' class="'.$this->_generatedLinkClass.'" >></a></li>';
		
		return $return;
	}
	
	protected function getFirst()
	{
		$return = '';
		
		$this->_routeParams["page"] = 1;
		
		$return .= '<li class="first'.($this->_currentPage == 1 ? ' disabled' : '').'"><a href="'.$this->getUrl().'" '.$this->_generatedLinkAttr.' class="'.$this->_generatedLinkClass.'" ><<</a></li>';
		
		return $return;
	}
	
	protected function getLast()
	{
		$return = '';
		
		$this->_routeParams["page"] = $this->_countPages;
		
		$return .= '<li class="last'.($this->_currentPage == $this->_countPages ? ' disabled' : '').'"><a href="'.$this->getUrl().'" '.$this->_generatedLinkAttr.' class="'.$this->_generatedLinkClass.'" >>></a></li>';
		
		return $return;
	}
	
	protected function generateClass()
	{
		if (!empty($this->_linkClass))
			$this->_generatedLinkClass = implode(" ", $this->_linkClass);
		else
			$this->_generatedLinkClass = '';
	}
	
	protected function generateAttr() {
		if (!empty($this->_linkAttr)) {
			$result = '';
			foreach ($this->_linkAttr as $key => $value) {
				$result .= ' '.$key.'="'.$value.'"';
			}
			$this->_generatedLinkAttr = $result;
		}
		else
			$this->_generatedLinkAttr = '';
	}
	
    public function paginate()
    {
		$this->countPages();
		$this->getCurrentPage();
		$this->generateClass();
		$this->generateAttr();
		
		if($this->_countPages <= 1)
			return '';
		
		$output = '<div class="'.$this->getClassPagination().'"><ul>';
		
		$output .= $this->getFirst();
		$output .= $this->getPrevious();
		
		if (!empty($this->_typeRender) && $this->_typeRender == 'ellipsis')
			$output .= $this->renderPaginateEllipsis();
		else
			$output .= $this->renderPaginateNormal();
		
		$output .= $this->getNext();
		$output .= $this->getLast();
		
		$output .= '</ul></div>';
		
		return $output;
    }
    
	protected function renderPaginateEllipsis()
    {
    	$output = '';
    	
    	if (($this->_currentPage-3) > 1)
	    	$output = '<li class="disabled"><a>...</a></li>';
    	
    	for ($i = ($this->_currentPage-3); $i <= $this->_currentPage+3; $i++) {
    		
    		if ($i > $this->_countPages || $i < 1)
    			continue;
    		
			$this->_routeParams["page"] = ($i != 1) ? $i : 1;
			
			$output .= '<li'.($this->_currentPage == $i ? ' class="active"' : '').'><a href="'.$this->getUrl().'" '.$this->_generatedLinkAttr.' class="'.$this->_generatedLinkClass.'" >'.$i.'</a></li>';
		}
		
		if ($this->_currentPage+3 < $this->_countPages)
    		$output .= '<li class="disabled"><a>...</a></li>';
    	
		return $output;
    }
    
    protected function renderPaginateNormal()
    {
    	$output = '';
    	
    	for ($i = 1; $i <= $this->_countPages; $i++) {
			$this->_routeParams["page"] = ($i != 1) ? $i : 1;
			
			$output .= '<li'.($this->_currentPage == $i ? ' class="active"' : '').'><a href="'.$this->getUrl().'" '.$this->_generatedLinkAttr.' class="'.$this->_generatedLinkClass.'" >'.$i.'</a></li>';
		}
		
		return $output;
    }
    
    /**
     * Retourne l'entier à partir duquel doivent être récupérés les élements. (limit sql)
     *
     * @return int limit
     */
	public function getFromLimit()
	{
		$this->countPages();
		$this->getCurrentPage();
		
		if($this->_currentPage > 1 && $this->nbItems > 0) {
            if($this->_currentPage > 1 && $this->_currentPage <= $this->_countPages) {
                $from = ($this->_currentPage - 1) * $this->byPage;
            }
            else {
                $from = $this->_currentPage * $this->byPage;
            }
        }
        else {
            $from = '0';
        }
        return $from;
	}
	
	/**
	 * Spécifie au paginateur les paramètres de route qui seront utilisés pour générer les liens.
	 *
	 * @param mixed $routeParams
	 * @throws Zend_Exception
	 * @return
	 */
	public function setRouteParams($routeParams)
    {
    	if(is_array($routeParams)) {
	    	if(!isset($routeParams["route"]) || !isset($routeParams["module"]) || !isset($routeParams["controller"]) || !isset($routeParams["action"]))
	    		throw new Zend_Exception(_t("Paginator route param is missing"));
	    	
	    	$this->_routeParams = $routeParams;
    	}
    	else {
    		$this->_url = $routeParams;
    	}
    	
        return;
    }
    
    protected function countPages()
    {
    	if(!isset($this->_countPages)) {
    		if($this->nbItems > 0)
        		$this->_countPages = ceil($this->nbItems / $this->byPage);
        	else
        		$this->_countPages = 1;
    	}
    	
        return;
    }
    
    public function addLinkAttr($key, $value) {
    	$this->_linkAttr[$key] = $value;
    }
    
    public function addLinkClass($class) {
    	$this->_linkClass[] = $class;
    }
    
    /**
     * @deprecated
     * Use : addLinkAttr()
     */
    public function addTagInLink($datas = array())
    {
    	if (!empty($datas)){
    		foreach ($datas as $tag => $value) {
    			$this->addLinkAttr($tag, $value);
    		}
    	}
    }
    
    /**
     * @deprecated
     * Use : addLinkClass()
     */
    public function addClassInLink($class = null)
    {
    	if ($class) {
    		$this->addLinkClass($class);
    	}
    }
    
	protected function getCurrentPage()
	{
		if(!isset($this->_currentPage)){
			$this->_currentPage = Zend_Controller_Front::getInstance()->getRequest()->getParam("page");
			
			if (empty($this->_currentPage))
				$this->_currentPage = 1;
			
			if($this->_currentPage > $this->_countPages)
				throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
		}
	}
}