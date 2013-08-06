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

class Contact_Object_Contact extends CMS_Object_MonoLangEntityWithNodes
{
	public $id;
	
	public $type;
	public $typeSelect; // 0 ou 1
	
	public $emails;
	public $emailsCci;
	
	public $content;
	public $auto_response;
	public $save_data;

	protected $nodes;
	protected $_form;
		
	protected static $_nodes = array(
			"nodes" => "Contact_Object_ContactSave"
	);
	
	protected static $_modelClass = "Contact_Model_DbTable_Contact";
	protected static $_model;
		
	public static function get(array $filters = null)
	{
		self::_getModel();
		
		$ids = static::$_model->get($filters);
		
		if( count($ids) )
		{
			$return = array();
			foreach ($ids as $id)
			{
				$return[] = new static($id);
			}
			return $return;
		}
		
		return null;
	}
	
	/**
	 * 
	 * Fonction de récupération des formulaires actif
	 * (core_page enable = 1)
	 * @return array() objet contact
	 */
	public static function getEnable()
	{
		$allContact = self::get();
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		$list_enable = array();
		if (!empty($allContact)) {
			foreach ($allContact as $contact){
				$page = CMS_Page_Object::getOneFromDB(array("url_system" => $helper->full('contact', array('action'=>"contact", 'type'=>$contact->type))), null, null, 'all');
				if ($page->visible){
					$list_enable[] = $contact;
				}
			}
		}
		
		return $list_enable;
	}
	
	/**
	 * 
	 * Fonction qui va checker dans le fichier XML du contact pour voir si un element <email> s'y trouve
	 * @return true si <email> présent, false non présent
	 */
	public function hasEmailReply(){
		// Mise en variable de classe le fait d'avoir ou non le demande de l'email utilisateur
		// (pour l'affichage ou non de la réponse auto)
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
		if( file_exists($typesPath."/".$this->type."/type.xml") )
		{
			$xml = new Zend_Config_Xml($typesPath."/".$this->type."/type.xml");
			foreach ($xml->nodes->elements as $key => $value)
			{
				// Check si il y a dans le formulaire un email demandé
				if (strstr(strtolower($key), 'email') && $this->email_reply == null)
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * Retourne tout les formulaire contact par recherche de dossier dans l'application
	 * Génère en même temps leur core page en base de donnée si celui ci n'existe pas
	 */
	public static function getAllXmlFromApp()
	{
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
		$directory = new DirectoryIterator($typesPath);
		
		$contactType = array();
		
		if( count($directory) > 0 )
		{
			$hooks = CMS_Application_Hook::getInstance();
			/** Extract Type **/
			foreach($directory as $file)
			{
				if (!$file->isDot())
				{
					if( file_exists($typesPath."/".$file->getFilename()."/type.xml") )
					{
						$xml = new Zend_Config_Xml($typesPath."/".$file->getFilename()."/type.xml");
						$object = Contact_Object_Contact::getOne(array("type" => $xml->name));
						if( $object )
							$values = $object->toArray();
						$contactType[] = array( "values" => $values ? $values : array(), "name" => $xml->name, "description" => $xml->description );
						/** Page in page_cores **/
						if( $xml->page && $xml->page == "true" )
						{
							$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
							$page = CMS_Page_Object::get($helper->full('contact', array('action'=>"contact", 'type'=>$xml->name)));

							//$page = CMS_Page_Object::get($this->_helper->route->full('contact', array('action'=>"contact", 'type'=>$xml->name)));
							if( !$page )
							{
								$page 				= new CMS_Page_PersistentObject();
								$page->title 		= array(CURRENT_LANG_ID => $xml->description);
								$page->type 		= "contact";
								$page->url_system	= $helper->full('contact', array('action'=>"contact", 'type'=>$xml->name));
								$page->enable 		= 1;
								$page->visible 		= 1;
								$page->save();
							}
						}
					}
				}
			}
		}
		
		return $contactType;
	}
	
	/**
	 * 
	 * Fonction permettant de récupérer les champs xml qui se trouve dans le formulaire instancié
	 * @return array() champs
	 */
	public function getKeyInForm()
	{
		$return = array();
		$keys = self::$_model->getKeyInForm($this->id);
		
		if (count($keys) > 0){
			foreach ($keys as $key)
				$return[] = $key['key'];
		}
		return $return;
	}
		
	/**
	 * 
	 * Fonction permettant de récupérer le nombre de soumission du formulaire
	 */
	public function getNbSubmission()
	{
		return self::$_model->getNbSubmission($this->id);
	}
	
	
	public function getForm(){
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
		$contactTypeFolder = $typesPath . '/' . $this->type;
		
		/** Error **/
		if( !file_exists($contactTypeFolder . '/type.xml'))
			throw new Zend_Exception(_t("This contact form type doesn't exist"));
		
		/** Get xml  **/
		$xml = new Zend_Config_Xml($contactTypeFolder.'/type.xml');
		
		/** Error **/
		if( !($xml->page && $xml->page == "true") )
			throw new Zend_Exception(_t("This contact form type not available to page"));
		
		if($xml->title)
			$this->view->titleForm = $xml->title;
			
		/** Create Form **/
		$form = new CMS_Form_Default(array("xml" => $xml->nodes));
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		$form->setAction($helper->full('contact', array('action'=>"contact", 'type'=>$this->type)));
		
		/** Add select to select destinataire **/
		if ( $this->typeSelect == 1) {
			$item = new Zend_Form_Element_Select("select_destinataire");
			$item->setLabel("Choisissez le destinataire");
			$item->setRequired(true);
			$item->setOrder(0);
				
			$datas = json_decode($this->emails, true);
				
			foreach ($datas as $key => $data) {
				$item->addMultiOption($data["name"], $data["name"]);
			}
				
			$form->addElement($item);
		}
		
		/** Add button valid **/
		$item = new Zend_Form_Element_Submit("send");
		$item->setValue(_t("Send"));
		$form->addElement($item);
				
		$this->_form = $form;
		return $this->_form;
	}
	public function setForm($form)
	{
		$this->_form = $form; 
	}
	
	public function checkFormView(){
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
		$contactTypeFolder = $typesPath . '/' . $this->type;
		
		// Si le type email fourni un tpl "page.tpl" on l'utilise pour générer la vue.
		if(file_exists($contactTypeFolder."/page.tpl")){
			$view = Zend_Layout::getMvcInstance()->getView();
			$path = $view->getScriptPaths();
			$view->setScriptPath($contactTypeFolder);
		
			$form = $view->renderInnerTpl("page.tpl");
			$view->setScriptPath($path);
		}
	}
	
	public function sendForm()
	{
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
		$contactTypeFolder = $typesPath . '/' . $this->type;
		
		/** Error **/
		if (!file_exists($contactTypeFolder . '/type.xml'))
			throw new Zend_Exception(_t("This contact form type doesn't exist"));
		
		/** Get xml  **/
		$xml = new Zend_Config_Xml($contactTypeFolder.'/type.xml');
		
		/** Error **/
		if (!($xml->page && $xml->page == "true"))
			throw new Zend_Exception(_t("This contact form type not available to page"));
		
		if ($xml->title)
			$this->view->titleForm = $xml->title;
		
		/** Verify emails in bdd **/
		if (!$this->emails && !$this->emails)
			throw new Zend_Exception(_t("No email is configure for this form"));
		
		/** Get Smarty & Change path tpl **/
		$view = Zend_Layout::getMvcInstance()->getView();
		$path = $view->getScriptPaths();
		$view->setScriptPath($contactTypeFolder);
		
		$email_reply = null;
		
		/** If nodes exists **/
		if ( $xml->nodes ) {
			$nodes = array();
			$num_send = uniqid();
		
			$values = $this->_form->getValues();
			foreach ($values as $key => $value) {
				
				$val = (!is_array($value)) ? htmlspecialchars($value) : array_walk_recursive($value, 'htmlspecialchars');
				
				// Récupération de l'email posté pour l'éventuelle réponse auto
				if (strstr(strtolower($key), 'email') && $email_reply == null) {
					$emailValidator = new Zend_Validate_EmailAddress();
					if ($emailValidator->isValid($val)) {
						$email_reply = $val;
					}
				}
				
				// Assignation pour le TPL
				$view->assign($key, $val);
				 
				// Sauvegarde des données si configurée
				if ($this->save_data) {
					$contact_save 				= new Contact_Object_ContactSave();
					$contact_save->parent_id 	= $this->id;
					$contact_save->key 			= $key;
					$contact_save->value 		= $val;
					$contact_save->num_send 	= $num_send;
		
					$contact_save->save();
				}
			}
		}
		 
		/** Generaet content email **/
		$mailContent = $view->renderInnerTpl("email.tpl", false);
		
		/** Config Mail **/
		$mail = new Zend_Mail('UTF-8');
		
		if( $this->typeSelect == 0 ) {
			/** Emails destinataires **/
			$emails = json_decode($this->emails, true);
			if( count($emails) >0 ) {
				foreach ($emails as $email)
				$mail->addTo($email, $email);
			}
				
			/** Emails cci **/
			$emailsCci = json_decode($this->emailsCci, true);
			if( count($emailsCci) > 0)
			$mail->addBcc($emailsCci);
		}
		else {
			/** Emails destinataires **/
			$name = $_POST["select_destinataire"];
			 
			$datas = json_decode($this->emails, true);
			$test = "";
			foreach ($datas as $key => $data) {
				if( $data["name"] == $name) {
					foreach ($data["emails"] as $email) {
						$test .= " ".$email;
						$mail->addTo($email, $email);
					}
					break;
				}
			}
				
		}
		
		$prependObject = !empty($xml->object) ? $xml->object : "Message envoyé depuis votre site: ";
		
		/** Send Mail **/
		try {
			
			$mail	->setBodyHtml($mailContent)
					->setFrom($this->_form->getValue('email'), $this->_form->getValue('prenom').' '.$this->_form->getValue('nom'))
					->setSubject($prependObject . $_SERVER["SERVER_NAME"])
			 		->send();
			
			// Envoi du mail automatique à l'utilisateur si le champ est renseigné
			if ($email_reply && $this->auto_response && EMAIL_FROM && EMAIL_SIGN) {
				$mail_reply = new Zend_Mail('UTF-8');
				$mail_reply ->addTo($email_reply, $email_reply)
							->setBodyHtml($this->auto_response)
							->setFrom(EMAIL_FROM, EMAIL_SIGN)
							->setSubject(_t("Acknowledgment of your email from "). $_SERVER["SERVER_NAME"])
							->send();
			}
			
		}catch(Exception $e) {
			$errors = true;
		}
						
		/** Remise du chemin des templates d'origines **/
		$view->setScriptPath(array_reverse($path));
		
		return !$errors;
	}
}