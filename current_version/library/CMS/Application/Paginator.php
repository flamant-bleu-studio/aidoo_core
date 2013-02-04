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
	private $_linkTags = "";
	private $_classTags = array();
	
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
	
	public function setAlignment($alignment) {
		$this->_alignment = $alignment;
	}
	
	public function setSize($size) {
		$this->_size = $size;
	}
	
	private function getClassPagination()
	{
		$return = 'pagination';
		
		if ($this->_alignment == 'right' || $this->_alignment == 'centered')
			$return .= ' pagination-'.$this->_alignment;
		
		if ($this->_size == 'large' || $this->_size == 'small' || $this->_size == 'mini')
			$return .= ' pagination-'.$this->_size;
		
		return $return;
	}
	
	private function getUrl()
	{
		return BASE_URL.$this->_routeHelper->full($this->_routeParams["route"], $this->_routeParams);
	}
	
	public function getPrevious()
	{
		$return = '';
		
		$this->_routeParams["page"] = ($this->_currentPage-1 > 1) ? $this->_currentPage-1 : null;
		
		$return .= '<li class="previous'.($this->_currentPage == 1 ? ' disabled' : '').'"><a href="'.$this->getUrl().'" '.$this->_linkTags.' class="'.implode(" ", $this->_classTags).'" ><</a></li>';
		
		return $return;
	}
	
	public function getNext()
	{
		$return = '';
		
		$this->_routeParams["page"] = ($this->_currentPage+1 <= $this->_countPages) ? $this->_currentPage+1 : $this->_countPages;
		
		$return .= '<li class="next'.($this->_currentPage == $this->_countPages ? ' disabled' : '').'"><a href="'.$this->getUrl().'" '.$this->_linkTags.' class="'.implode(" ", $this->_classTags).'" >></a></li>';
		
		return $return;
	}
	
    public function paginate()
    {
		$this->countPages();
		$this->getCurrentPage();
		
		if($this->_countPages <= 1)
			return '';
		
		$output = '<div class="'.$this->getClassPagination().'"><ul>';
		
		$output .= $this->getPrevious();
		
		for ($i = 1; $i <= $this->_countPages; $i++) {
			$this->_routeParams["page"] = ($i != 1) ? $i : null;
			
			$output .= '<li'.($this->_currentPage == $i ? ' class="active"' : '').'><a href="'.$this->getUrl().'" '.$this->_linkTags.' class="'.implode(" ", $this->_classTags).'" >'.$i.'</a></li>';
		}
		
		$output .= $this->getNext();	
		
		$output .= '</ul></div>';
		
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
	 * @param array $routeParams
	 * @throws Zend_Exception
	 * @return
	 */
	public function setRouteParams(array $routeParams)
    {
    	if(!isset($routeParams["route"]) || !isset($routeParams["module"]) || !isset($routeParams["controller"]) || !isset($routeParams["action"]))
    		throw new Zend_Exception(_t("Paginator route param is missing"));
    	
    	$this->_routeParams = $routeParams;
    	
        return;
    }
    
    private function countPages()
    {
    	if(!isset($this->_countPages))
    		if($this->nbItems > 0)
        		$this->_countPages = ceil($this->nbItems / $this->byPage);
        	else
        		$this->_countPages = 1;
        return;
    }
    
    public function addTagInLink($datas = array())
    {
    	if (!empty($datas)){
    		foreach ($datas as $tag => $value) {
    			$this->_linkTags .= " ".$tag."='".$value."'";
    		}
    	}
    }
    
    public function addClassInLink($class = null)
    {
    	if ($class) {
    		$this->_classTags[] = $class;
    	}
    }
    
	private function getCurrentPage()
	{
		if(!isset($this->_currentPage)){
			$this->_currentPage = Zend_Controller_Front::getInstance()->getRequest()->getParam("page");
			
			if($this->_currentPage > $this->_countPages)
				throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
		}
	}
}