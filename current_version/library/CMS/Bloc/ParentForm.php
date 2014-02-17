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

class CMS_Bloc_ParentForm extends CMS_Form_Default
{
	private $decoratorsBloc;
	private $templatesBloc;
	
	public function __construct($decorators, $templates)
	{
		$this->decoratorsBloc = $decorators;
		$this->templatesBloc = $templates;
		
		parent::__construct();
	}
	
	public function init()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		$item = new Zend_Form_Element_Hidden("from");
		$item->setValue("bloc_form");
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('designation');
		$item->setLabel(_t("Désignation"));
		$item->setDescription(_t("Titre partie admin"));
		$item->setRequired(true);
		$item->setTranslatable(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('title');
		$item->setLabel(_t("Title"));
		$item->setDescription(_t("Titre pour les visiteurs"));
		$item->setRequired(true);
		$item->setTranslatable(true);
		$this->addElement($item);
		
		if ($backAcl->hasPermission('mod_bloc', 'manage')) {
			$item = new Zend_Form_Element_Select("decorator");
			$item->setLabel(_t("Decorator"));
			$item->setDescription(_t("Your bloc design"));
			
			if ($this->decoratorsBloc['general']) {
				$item->addMultiOptions( array( _t('Common') => array() ) );
				$item->addMultiOptions($this->decoratorsBloc['general']);
			}
			
			if( isset($this->decoratorsBloc['bloc']) && $this->decoratorsBloc['bloc'] ) {
				$item->addMultiOptions( array( _t('Specific') => array() ) );
				$item->addMultiOptions($this->decoratorsBloc['bloc']);
			}
			
			$item->setValue("default");
			
			$item->setRequired(true);
			$this->addElement($item);
		}
		else {
			$item = new Zend_Form_Element_Hidden("decorator");
			$item->setValue("default");
			$item->setRequired(true);
			$this->addElement($item);
		}
		
		if (!empty($this->templatesBloc) && $backAcl->hasPermission('mod_bloc', 'manage')) {
			$item = new Zend_Form_Element_Select("templateFront");
			$item->setLabel(_t("Template"));
			
			$item->addMultiOption('front', _t('Default'));
			$item->addMultiOptions($this->templatesBloc);
			
			$item->setRequired(true);
		}
		else {
			$item = new Zend_Form_Element_Hidden("templateFront");
		}
		
		$item->setValue(0);
		
		$this->addElement($item);
		
		if ($backAcl->hasPermission('mod_bloc', 'manage')) {
			/* ---- Thèmes ---- */
			$item = new Zend_Form_Element_Select("theme");
			$item->setLabel(_t("Theme"));
			$item->setDescription(_t("Theme of your bloc"));
			$item->addMultiOption('', _t('None'));
			
			try {
				$config		= CMS_Application_Config::getInstance();
		    	$skinFront 	= $config->get("skinfront");
		    	
		    	$themes = new Zend_Config_Xml(PUBLIC_PATH.'/skins/' . $skinFront . '/skin.xml', 'blocsThemes');
				
				if( $themes->theme )
				{
					$themes = $themes->theme->toArray();
	
					// Reset level for Zend_Config_XML
					if(key($themes) != "0")
						$themes = array($themes);
					   
					foreach ($themes as $theme) {
						$item->addMultiOption($theme["class"], $theme["name"]);
					}
	            }
		    	
			}catch(Exception $e){}
			
			$this->addElement($item);
		}
		else {
			$item = new Zend_Form_Element_Hidden("theme");
			$this->addElement($item);
		}
		
		if ($backAcl->hasPermission('mod_bloc', 'manage')) {
			$item = new Zend_Form_Element_Text('classCss');
			$item->setLabel(_t("CSS class"));
			$item->setDescription(_t("your bloc class css"));
		}
		else
			$item = new Zend_Form_Element_Hidden('classCss');
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('sizeBloc');
		$item->addMultiOptions(array(
			'25' => '25%',
			'50' => '50%',
			'75' => '75%',
			'100' => '100%'
		));
		$item->setLabel(_t('Size'));
		$item->setDescription(_t('To mobile'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Save'));
		$item->setLabel(_t('Save'));
		$item->setOrder(999);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submitandquit");
		$item->setValue(_t('Save & Quit'));
		$item->setLabel(_t('Save & Quit'));
		$item->setOrder(1000);
		$this->addElement($item);
	}
	
	
}