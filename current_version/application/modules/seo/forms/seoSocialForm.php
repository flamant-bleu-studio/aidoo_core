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

class seo_Form_seoSocialForm extends CMS_Form_Default
{
    public function init()
    {
    	$hidden = $this->createElement('hidden', 'social');
    	$this->addElement($hidden);
    	
		$gAnalytics = $this->createElement('text', 'googleanalytics');
		$gAnalytics->setLabel("Google Analytics code UA");
		$gAnalytics->setDescription("Exemple : UA-XXXXXX-X");
		$gAnalytics->setAttrib('size',20);
		$gAnalytics->addValidator(
			'regex', 
			false, 
			array(
            	'pattern' => '/^UA-[0-9]+-[0-9]+$/',
            	'messages' => '"%value% n\'est pas au bon format (ex : UA-XXXXX-X)'
            )
        );
		$this->addElement($gAnalytics);
		
		$gAnalytics = $this->createElement('text', 'googleaccount');
		$gAnalytics->setLabel("Google Analytics email");
		$gAnalytics->setDescription("votre.mail@gmail.com");
		$gAnalytics->setAttrib('size',20);
		$gAnalytics->addValidator(new Zend_Validate_EmailAddress());
		$this->addElement($gAnalytics);
		
		$gAnalytics = $this->createElement('password', 'googlepassword');
		$gAnalytics->setLabel("Google Analytics mot de passe");
		$gAnalytics->setDescription("Mot de passe du compte Google");
		$gAnalytics->setAttrib('size',20);
		$gAnalytics->setAttrib('renderPassword', true);
		$this->addElement($gAnalytics);
		
		/*
		$gAnalytics = $this->createElement('text', 'googleprofile');
		$gAnalytics->setLabel("Profil Google Analytics");
		$gAnalytics->setDescription("Site Web à analyser");
		$gAnalytics->setAttrib('size',20);
		$this->addElement($gAnalytics);		
		*/
				
		$facebook = $this->createElement('text', 'facebook');
		$facebook ->setLabel("Facebook");
		$facebook->setAttrib('size',20);
		$this->addElement($facebook);
		
		$twitter = $this->createElement('text', 'twitter');
		$twitter->setLabel("Twitter");
		$twitter->setAttrib('size',20);
		$this->addElement($twitter);
		
		$sitename = $this->createElement('text', 'sitename');
		$sitename->setLabel("Nom du site");
		$sitename->setDescription("Utilisé pour les commentaire facebook");
		$sitename->setAttrib('size',20);
		$this->addElement($sitename);
		
		$item = new CMS_Form_Element_SubmitCustom("submitsocial");
		$item->setValue(_t('Update'));
		$item->setLabel(_t('Update'));
		$this->addElement($item);
		
		$config = CMS_Application_Config::getInstance();
		//$this->updateButtonLinks($config);		
    }

	public function updateButtonLinks($config)
	{
		$socialConfig = $config->get("social");
		
		if ($socialConfig)
		{
			$data = json_decode($config->get("social"),true);
		}
	}

}
