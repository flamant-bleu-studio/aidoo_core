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

class Bloc_Membres_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $nb_article;
	public $group;
	public $imageFormat;
	public $showArchive;
	public $textArchive;
		
	public $displayMode;
	public $fromMode;
	
	public $showPagination;
	public $pagerPosition;
	public $showArrow;
	public $nb_page;

	public $alignment;
	public $scrolling;
	
	public $tickerSpeed;
	
	public $autoStart;
	public $stopHover;
	public $selection;
	
	protected $_adminFormClass = 'Bloc_Membres_AdminForm';
	
	protected static $_translatableFields = array('textArchive');
	
	public static $MODE_HORIZONTAL 	= 0;
	public static $MODE_VERTICAL 	= 1;
	
	public static $DISPLAY_MODE_UNIQUE 	= 0;
	public static $DISPLAY_MODE_SLIDE 	= 1;
	public static $DISPLAY_MODE_TICKER 	= 2;

	public function runtimeAdmin($view){
		$view->membresLst = Users_Object_User::get();
		$view->selection = $this->selection;
	}
	
	public function runtimeFront($view) {

		$view->imagePath = BASE_URL.'/resources/'.UPLOAD_FOLDER.'/profils/thumbs/' . ((isset($this->imageFormat)) ? $this->imageFormat."-" : '');
		
		if($this->fromMode != "selection" && $this->category){
			if($this->displayMode == self::$DISPLAY_MODE_UNIQUE )
				$count = $this->nb_article ;
			else 
				$count = $this->nb_article * $this->nb_page;
			
			$membres = Users_Object_User::get(array('group' => (int)$this->group), null, (int)$count);
			

			$countArticle = count($membres);
		}
		else if($this->fromMode == "selection") {
			
			$membres = array();
			if($this->selection){
				$membres = Users_Object_User::get(array("id" => $this->selection));
			}

			$countArticle = count($membres);
		}
		
		$view->count 	= $countArticle;
		$view->membres 	= $membres;
		$view->datas 	= $this->toArray();
		
		if($this->displayMode == self::$DISPLAY_MODE_SLIDE || $this->displayMode == self::$DISPLAY_MODE_TICKER){
			// IF slide différent de l'alignement => 1 li pour plusieurs articles
			if($this->alignment !== $this->scrolling ){
				$view->countli 			= ceil($countArticle / $this->nb_article);
				$view->countbyli 		= $this->nb_article;
				$view->displaySlideQty 	= 1;
			}
			// IF slide dans le même sens que l'alignement => 1 li par article
			else {
				$view->countli 			= $countArticle;
				$view->countbyli 		= 1;
				$view->displaySlideQty 	= $this->nb_article;
			}
			
			if($this->displayMode == self::$DISPLAY_MODE_TICKER)
				$view->ticker = true;
			
		}
		else {
			$view->countli 			= $countArticle;
			$view->countbyli 		= 1;
		}
	}
	
	public function save($post){
	
		$this->nb_article 		= $post['nb_article'];
		$this->category 		= $post['category'];
		$this->fromMode	 		= $post['fromMode'];
		$this->imageFormat		= $post['imageFormat'];
		
		$this->showArchive 		= $post['showArchive'];
		$this->textArchive 		= $post['textArchive'];

		$this->truncateText		= ($post['truncateText'] != '') ? (int)$post['truncateText'] : '0' ;
		$this->displayMode 		= $post['displayMode'];
		
		$this->showPagination 	= $post['showPagination'];
		$this->pagerPosition 	= $post['pagerPosition'];
		$this->nb_page 			= ($post['nb_page'] != '') ? (int)$post['nb_page'] : '3' ;
		$this->showArrow		= $post['showArrow'];
		
		$this->alignment 		= $post['alignment'];
		$this->scrolling 		= $post['scrolling'];
		
		$this->tickerSpeed  	= ($post['tickerSpeed'] != '') ? (int)$post['tickerSpeed'] : '2500' ;
		
		$this->autoStart 		= $post['autoStart'];
		$this->stopHover 		= $post['stopHover'];
		
		$this->selection = array();
		if(!empty($_POST["selection"])){
			foreach($_POST["selection"] as $key => $s){
				if($s == "on")
					$this->selection[] = $key;
			}
		}
		
		$id = parent::save($post);
		
		return $id;
	}
}
