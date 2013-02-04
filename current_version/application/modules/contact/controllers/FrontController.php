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

class Contact_FrontController extends Zend_Controller_Action
{
	
	public function contactAction()
	{
		/** Get informations in bdd **/
		$contact = Contact_Object_Contact::getOne(array("type" => $this->_request->getParam("type")));
		
		if (!$contact)
			throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
		
		$form = $contact->getForm();		
		$contact->checkFormView();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				if ($contact->sendForm())
					$this->view->sendOk = true;
				else 
					$this->view->sendError = true;
			}
			else 
				$form->populate($_POST);
		}
		
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
		$contactTypeFolder = $typesPath . '/' . $this->_request->getParam("type");
		
		// Si le type email fourni un tpl "page.tpl" on l'utilise pour générer la vue.
		if(file_exists($contactTypeFolder."/page.tpl")) {
			$view = Zend_Layout::getMvcInstance()->getView();
		    $path = $view->getScriptPaths();
		    $view->setScriptPath($contactTypeFolder);
		    
		    $form = $view->renderInnerTpl("page.tpl");
			$view->setScriptPath($path);
		}
		
		/** Assign VAR **/
		$this->view->form = $form;
		$this->view->contact_content 	= $contact->content;
		$this->view->contact_emails 		= $contact->emails;
	}

	
	public function contactActionOld()
	{
		/** Type **/
		$contactType = $this->_request->getParam("type");
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
	
		$contactTypeFolder = $typesPath . '/' . $contactType;
	
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
	
		$form->setAction($this->_helper->route->short('contact', array("type" => $contactType)));
	
		/** Get informations in bdd **/
		$contact = Contact_Object_Contact::get(array("type" => $xml->name));
	
		/** POST **/
		if( $this->getRequest()->isPost() )
		{
			/** Valid **/
			if( $form->isValid($_POST) )
			{
				/** Verify emails in bdd **/
				if( !$contact->emails && !$contact->emails )
				throw new Zend_Exception(_t("No email is configure for this form"));
	
				/** Get Smarty & Change path tpl **/
				$view = Zend_Layout::getMvcInstance()->getView();
				$path = $view->getScriptPaths();
				$view->setScriptPath($contactTypeFolder);
	
				/** If nodes exists **/
				$email_reply = null;
				if( $xml->nodes )
				{
					$nodes = array();
					$num_send = uniqid();
					/** All Field Form **/
					foreach ($xml->nodes->elements as $key => $value)
					{
						/** Assign value **/
						$val = (is_string($_POST[$key])) ? htmlspecialchars($_POST[$key]) : $_POST[$key];
						$view->assign($key, $val);
						 
						// Check si il y a dans le formulaire un email demandé
						if (strstr(strtolower($key), 'email') && $email_reply == null)
						$email_reply = $val;
						 
						if ($contact->save_data)
						{
							// SAUVEGARDE DES DONNEES
							$contact_save 					= new Contact_Object_ContactSave();
							$contact_save->parent_id 	= $contact->id;
							$contact_save->key 			= $key;
							$contact_save->value 		= $val;
							$contact_save->num_send = $num_send;
	
							$contact_save->save();
						}
					}
				}
			  
				/** Generaet content email **/
				$mailContent = $view->renderInnerTpl("email.tpl");
	
				/** Config Mail **/
				$mail = new Zend_Mail('UTF-8');
	
				if( $contact->typeSelect == 0 ) {
					/** Emails destinataires **/
					$emails = json_decode($contact->emails, true);
					if( count($emails) >0 ) {
						foreach ($emails as $email)
						$mail->addTo($email, $email);
					}
						
					/** Emails cci **/
					$emailsCci = json_decode($contact->emailsCci, true);
					if( count($emailsCci) > 0)
					$mail->addBcc($emailsCci);
				}
				else {
					/** Emails destinataires **/
					$name = $_POST["select_destinataire"];
					 
					$datas = json_decode($contact->emails, true);
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
	
				$mail->setBodyHtml($mailContent)
				->setFrom($_POST['email'], $_POST['prenom'].' '.$_POST['nom'])
				->setSubject("Message envoyé depuis vote site: ". $_SERVER["SERVER_NAME"]);
	
				// Envoi du mail automatique à l'utilisateur si le champ est renseigné
				if ($email_reply && $contact->auto_response && EMAIL_FROM){
					$mail_reply = new Zend_Mail('UTF-8');
					$mail_reply->addTo($email_reply, $email_reply);
					$mail_reply->setBodyHtml($contact->auto_response)
					->setFrom(EMAIL_FROM, EMAIL_SIGN)
					->setSubject(_t("Acknowledgment of your email from "). $_SERVER["SERVER_NAME"]);
					$mail_reply->send();
				}
	
				/** Send Mail **/
				if( $mail->send() )
				$this->view->sendOk = true;
				else
				$this->view->sendError = true;
	
				/** Remise du chemin des templates d'origines **/
				$view->setScriptPath($path);
			}
			else
			$form->populate($_POST);
		}
	
		/** Add select to select destinataire **/
		if ( $contact->typeSelect == 1) {
			$item = new Zend_Form_Element_Select("select_destinataire");
			$item->setLabel("Choisissez le destinataire");
			$item->setRequired(true);
			$item->setOrder(0);
				
			$datas = json_decode($contact->emails, true);
				
			foreach ($datas as $key => $data) {
				$item->addMultiOption($data["name"], $data["name"]);
			}
				
			$form->addElement($item);
		}
	
		/** Add button valid **/
		$item = new Zend_Form_Element_Submit("send");
		$item->setValue(_t("Send"));
		$form->addElement($item);
	
		// Si le type email fourni un tpl "page.tpl" on l'utilise pour générer la vue.
		if(file_exists($contactTypeFolder."/page.tpl")){
			$this->view->form = $form;
			$view = Zend_Layout::getMvcInstance()->getView();
			$path = $view->getScriptPaths();
			$view->setScriptPath($contactTypeFolder);
	
			$form = $view->renderInnerTpl("page.tpl");
			$view->setScriptPath($path);
		}
	
		/** Assign VAR **/
		$this->view->form = $form;
		$this->view->contact_content 	= $contact->content;
		$this->view->contact_emails 		= $contact->emails;
	}
}
