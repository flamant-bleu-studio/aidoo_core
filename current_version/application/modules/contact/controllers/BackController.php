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

class Contact_BackController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission('mod_contact', 'view')) {
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		$listContact = Contact_Object_Contact::getEnable();

		/** Create Form(s) **/
		if( count($listContact) > 0 )
		{
			foreach ($listContact as $key => $type)
			{
				$contactType[$type->id]["form"] = new Contact_Form_ContactConfiguration( array("name" => $type->type) );
				$contactType[$type->id]["name"] = $type->type;
				$contactType[$type->id]["form_field"] = array(
					"emails" 					=> $type->type."emails",
					"emailsCci" 				=> $type->type."emailsCci",
					"content" 				=> $type->type."content",
					"typeContact" 			=> $type->type."typeContact",
					"selectName" 			=> $type->type."selectName",
					"selectMails" 			=> $type->type."selectMails",
					"response_check" 	=> $type->type."response_check",
					"auto_response" 		=> $type->type."auto_response"
				); 		
				if (!$type->emails){
					$contactType[$type->id]["error_mail"] = true;
				}			
				$contactType[$type->id]["nb_submission"] = $type->getNbSubmission();
				$contactType[$type->id]["has_email_reply"] = $type->hasEmailReply();
			}
		}
		
		/** POST **/
		if( $this->getRequest()->isPost() )
		{
			if( count($listContact) > 0 )
			{
				foreach ($listContact as $type)
				{
					/** Save All Forms **/
					if( $contactType[$type->id]['form']->isValid($_POST) )
					{
						if( $type->id ) // update
							$contact = new Contact_Object_Contact($type->id);
						else // create
							$contact = new Contact_Object_Contact();
						
						if( !$contact->type )
							$contact->type = $type->type;
						
						$contact->typeSelect = !empty($_POST[$contactType[$type->id]["form_field"]["typeContact"]]) ? $_POST[$contactType[$type->id]["form_field"]["typeContact"]] : 0;

						if( $contact->typeSelect == 0 ) // type classiqe
						{
							/** emails desinataires **/
							if( $_POST[$contactType[$type->id]["form_field"]["emails"]] )
							{
								$emails = explode(";", $_POST[$contactType[$type->id]["form_field"]["emails"]]);
								if( count($emails) > 0 ) 
								{
									$datasEmails = array();
									
									foreach($emails as $email)
									{
										if( $email != "" )
											$datasEmails[] = $email;
									}
									
									$contact->emails = json_encode($datasEmails);
								}
							}
							else
								$contact->emails = null;
							
							/** emails cci **/
							if( $_POST[$contactType[$type->id]["form_field"]["emailsCci"]] )
							{
								$emails = explode(";", $_POST[$contactType[$type->id]["form_field"]["emailsCci"]]);
								if( count($emails) > 0 ) 
								{
									$datasEmails = array();
									
									foreach($emails as $email)
									{
										if( $email != "" )
											$datasEmails[] = $email;
									}
									
									$contact->emailsCci = json_encode($datasEmails);
								}
							}
							else
								$contact->emailsCci = null;
						}
						else 
						{
							$datas = array();
							$datasEmails = array();
							
							/** emails desinataires **/							
							$i = 1;
							(bool)$end = false;
							
							while( $end === false )
							{
								if( array_key_exists($contact->type."selectName-".$i, $_POST) && array_key_exists($contact->type."selectMails-".$i, $_POST) )
								{	
									if( !empty($_POST[$contact->type."selectName-".$i]) && !empty($_POST[$contact->type."selectMails-".$i]) ) {
										
										$emails = explode(";", $_POST[$contact->type."selectMails-".$i]);
										
										if( count($emails) > 0 ) 
										{
											$datas = array();
											
											foreach($emails as $email)
											{
												if( $email != "" )
													$datas[] = $email;
											}
											
											$datasEmails[$i]["name"] 	= $_POST[$contact->type."selectName-".$i];
											$datasEmails[$i]["emails"] 	= $datas;
										}
										
									}
								}
								else
									$end = true;
								
								$i++;
								
							}
							if (!empty($datasEmails)) 
								$contact->emails = json_encode($datasEmails);
							else  
								$contact->emails = null;
							
							
						}
											
						$contact->content = $_POST[$contactType[$type->id]["form_field"]["content"]];
						
						
						if ($_POST[$contactType[$type->id]["form_field"]["response_check"]] && $_POST[$contactType[$type->id]["form_field"]["auto_response"]] != '')
							$contact->auto_response = $_POST[$contactType[$type->id]["form_field"]["auto_response"]];
						else 
							$contact->auto_response = null;

						// Chargement des nodes (lazy loading) pour ne pas qu'ils soient supprimés
						$contact->nodes;

						$contact->save();
					}
					else
						_error(_t('invalid form'));
				}

				_message(_t("Email address have been changed"));
				return $this->_redirect($this->_helper->route->short("index"));
			}
		}
		
		/** Populate All Forms **/
		if( count($listContact) > 0)
		{
			foreach ($listContact as $type)
			{
				$temp = array();
				
				if( !$type->typeSelect )
				{
					/** emails destinataires **/
					if( $type->emails)
					{
						$emails = json_decode($type->emails, true);
						
						$datasEmails = "";
						if( count($emails) > 0 )
						{
							foreach ($emails as $email)
							{
								$datasEmails .= $email.";";
							}
							
							$temp[$contactType[$type->id]["form_field"]["emails"]] = $datasEmails;
						}
					}
					
					/** emails cci **/
					if( $type->emailsCci )
					{
						$emailsCci = json_decode($type->emailsCci, true);
						
						$datasEmailsCci = "";
						if( count($emailsCci) > 0 )
						{
							foreach ($emailsCci as $emailCci)
							{
								$datasEmailsCci .= $emailCci.";";
							}
							
							$temp[$contactType[$type->id]["form_field"]["emailsCci"]] = $datasEmailsCci;
						}
					}
					
				}
				else 
				{
					$datas = json_decode($type->emails, true);
					
					if(count($datas) > 0) {
						foreach ($datas as $key1 => $data) {
							$emails = "";
							if( is_array($data["emails"]) ) {
								foreach ($data["emails"] as $email) {
									$emails .= $email.";";
								}
								$datas[$key1]["emails"] = $emails;
							}
						}
					} 
					
					$contactType[$type->id]["datasSelect"] = $datas;
				}
				
				
				// Activation / desactivation du core_page si aucun email
				$page 					= CMS_Page_PersistentObject::getOneFromDB(array("url_system" => $this->_helper->route->full('contact', array('action'=>"contact", 'type'=>$type->type))), null, null, 'all');
				$page->enable 	= $type->emails ? 1 : 0;
				$page->save();
				
					
				
				
				if ($type->auto_response)
				{
					$contactType[$type->id]["form"]->getElement($type->type."response_check")->setValue(1);
				}
				
				$temp[$contactType[$type->id]["form_field"]["auto_response"]] = $type->auto_response;
				
				$contactType[$type->id]["form"]->getElement($type->type."typeContact")->setValue($type->typeSelect);
				
				$temp[$contactType[$type->id]["form_field"]["content"]] = $type->content;
				
				$contactType[$type->id]["form"]->populate($temp);
				
				unset($temp);
			}
		}
		
		// Droit utilisateurs
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("mod_contact", "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_contact");
			$formAcl->setAction(BASE_URL.$this->_helper->route->short('updateAcl'));
			$formAcl->addSubmit(_t("Submit"));
		
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->backAcl = $backAcl;
		$this->view->formType = $contactType;
	}
	
	/**
	 * 
	 * Fonction d'affichage et de sauvegarde des options du module de contact 
	 * Activation du formulaire
	 * Visualisation du formulaire
	 * Sauvegarde lors de l'envoi du formulaire (pour les exports)
	 */
	public function optionAction()
	{
		$this->_helper->layout()->setLayout('lightbox');
		$listContact = Contact_Object_Contact::get();

		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission('mod_contact', 'editOption')) {
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		/** Create Form(s) **/
		if( count($listContact) > 0 )
		{
			foreach ($listContact as $key => $type)
			{
				$contactType[$type->id]["form"] 			= new Contact_Form_ContactOption( array("name" => $type->type) );
				$contactType[$type->id]["name"] 			= $type->type;
				$contactType[$type->id]["form_field"] 	= array(
								"activation" 		=> $type->type."activation",
								"save" 				=> $type->type."save"
				);
			}
		}
		
		/** POST **/
		if( $this->getRequest()->isPost() )
		{
			if( count($listContact) > 0 )
			{
				foreach ($listContact as $type)
				{
					/** Save All Forms **/
					if( $contactType[$type->id]['form']->isValid($_POST) )
					{
						// On modifie le core page pour visible / enable
						$page 					= CMS_Page_PersistentObject::getOneFromDB(array("url_system" => $this->_helper->route->full('contact', array('action'=>"contact", 'type'=>$type->type))), null, null, 'all');
						//$page->enable 	= $_POST[$contactType[$type->id]["form_field"]["activation"]] != 0 ? 1 : 0;
						$page->visible 	= $_POST[$contactType[$type->id]["form_field"]["activation"]] != 0 ? 1 : 0;
						
						$page->save();
						
						//On modifie le champs dans la table 1_contact
						$contact 						= new Contact_Object_Contact($type->id);
						$contact->save_data 	= $_POST[$contactType[$type->id]["form_field"]["save"]] != 0 ? 1 : 0;
						$contact->nodes;
						
						$contact->save();
					}
				}
			}
			
			$this->closeIframe();
		}
		
		
		/** Populate All Forms **/
		if( count($listContact) > 0)
		{
			foreach ($listContact as $type)
			{
				// On modifie le core page pour visible / enable
				$page 		= CMS_Page_Object::get($this->_helper->route->full('contact', array('action'=>"contact", 'type'=>$type->type)));
				
				$temp 		= array();
				$contact 	= new Contact_Object_Contact($type->id);

				$temp[$contactType[$type->id]["form_field"]["activation"]] 	= $page->visible;
				$temp[$contactType[$type->id]["form_field"]["save"]] 			= $contact->save_data;
				
				$contactType[$type->id]["form"]->populate($temp);
			}
		}
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$this->view->backAcl 			= $backAcl;
		$this->view->formType 		= $contactType;
		$this->view->listContact 	= $listContact;
	}
	
	/**
	 * 
	 * Fonction permettant de recharger les fichiers XML, 
	 * de les entrer en base pour l'enregistrement dans 1_contact et le corps page
	 */
	public function reloadAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if(!$backAcl->hasPermission("mod_contact", "view")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Récupération des XMLs de contact via la méthod static
		$list_formulaire= Contact_Object_Contact::getAllXmlFromApp();

		// Liste des formulaire a ne pas supprimer de la base
		$list_no_delete 		= array();
		$nb_insert_contact 		= 0;
		$nb_delete_contact 		= 0;
		$nb_delete_core_contact	= 0;
		
		foreach ($list_formulaire as $formulaire){
			$list_no_delete[] = 'contact/'.$formulaire['name'];
			
			$contact = Contact_Object_Contact::get(array('type' => $formulaire['name']));
			if (!$contact) {
				$contact 					= new Contact_Object_Contact();
				$contact->type 				= $formulaire['name'];
				$contact->typeSelect 		= 0;
				$contact->emails 			= null;
				$contact->emailsCci 		= null;
				$contact->content 			= null;
				$contact->auto_response 	= null;
				$contact->save_data 		= 1;

				$contact->save();
				$nb_insert_contact++;
			}
		}

		// Suppression dans 1_contact
		$list_contact = Contact_Object_Contact::get();
		foreach($list_contact as $contact_obj){	
			if (!in_array('contact/'.$contact_obj->type, $list_no_delete)){
				// Suppression dans le corps page + 1_contact
				$contact_obj->delete();
				
				$nb_delete_contact++;
			}
		}
		
		// Suppression dans le core page
		$pages_core = CMS_Page_Object::getFromDB(array('type' => 'contact'));
		foreach($pages_core as $page){
			if (!in_array($page->url_system, $list_no_delete)){
				// Suppression dans le corps page + 1_core_pages
				$page->delete();
				
				$nb_delete_core_contact++;
			}
		}
				
		_message(_t('Reload done : '.$nb_insert_contact.' form added, '.$nb_delete_contact.' form deleted, core pages deleted : '.$nb_delete_core_contact));
		return $this->_redirect($this->_helper->route->short("index"));
	}
	
	/**
	 * 
	 * Fonction de création d'export pour le module. 
	 * Forme un CSV avec les données contenues dans '1_contact_save'
	 */
	public function exportAction() {
		$name = $this->_request->getParam('id');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission('mod_contact', 'export')) {
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Instance du formulaire de contact à exporter
		$contact_obj = Contact_Object_Contact::getOne(array('type' => $name));
		
		// Désactivation du rendu
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
	
		/*
		 * Construction du CSV
		*/
		$rows 			= $contact_obj->getKeyInForm();
		$return 		= array();

		foreach ($contact_obj->nodes as $data_save)
			$return[$data_save->num_send][$data_save->key] = $data_save->value;

		if (empty($rows))
			$rows[] = 'No data saved';
		
		// Ouverture d'un fichier "echo"
		$out = fopen('php://output', 'w');
	
		// Entête du fichier
		fputcsv($out, $rows, ',');
	
		// Données
		foreach($return as $row){
			fputcsv($out, $row);
		}
		fclose($out);
	
		// Header download CSV
		$this->getResponse()->setHeader('Content-Type', 'application/csv-tab-delimited-table', true);
		$this->getResponse()->setHeader('Content-Transfer-Encoding', 'binary', true);
		$this->getResponse()->setHeader('Content-disposition', 'attachment; filename="Export.csv"', true);
	}
	
	public function updateaclAction()
	{
		if($this->getRequest()->isPost())
		{
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->updatePermissionsFromAclForm("mod_contact", $_POST['ACL']))
			_message(_t("Rights updated"));
			else
			_error(_t("Insufficient rights"));
		}
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function closeIframe() {
		echo '<script language="javascript">parent.$.fancybox.close();</script>';
	
	}
}

