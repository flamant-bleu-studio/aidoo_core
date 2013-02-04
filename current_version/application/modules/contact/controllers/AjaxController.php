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

class Contact_AjaxController extends Zend_Controller_Action
{
	public function init()
	{
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    
    public function sendnewAction()
    {
    	try{
    		if( $this->getRequest()->isPost() )
    		{
    			// Récupération du contact
    			$contact = Contact_Object_Contact::getOne(array('type' => $_POST["type_form"]));
    			// Puis de son formulaire
    			$form = $contact->getForm();
    			
    			$_POST["values"][] = array('name' => 'url', 'value' =>$_POST["url"]);
    			
    			// Remplir les champs du formulaire
    			foreach ($form->getElements() as $elems => $obj)
    			{
    				foreach($_POST["values"] as $value){
    					if ($elems == $value['name'])
    						$form->getElement($elems)->setValue( $value['value']);
    				}
    			}
    		}
    		
    		// Envoyer le nouveau formulaire a l'objet
    		$contact->setForm($form);
    		// Send à l'email indiqué
    		$contact->sendForm();
    		
    		$result['error'] = false;
    	} catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}
		
		echo json_encode($result);
    }
    
	public function sendAction()
	{
		$result = array();
		
		try
		{
			if( $this->getRequest()->isPost() )
			{
				/** Xml type exist **/
				$typesPath = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact';
				if( file_exists($typesPath . "/" . $_POST["type_form"] . "/type.xml") )
				{
					$xml = new Zend_Config_Xml($typesPath . "/" . $_POST["type_form"] . "/type.xml");
					/** Form to bloc **/
					if( $xml->bloc && $xml->bloc == "true" )
					{
						/** Generate array datas to valid form **/
						$datas = array();
						foreach ($_POST["values"] as $field)
						{
							$datas[$field["name"]] = $field["value"];
						}
						
						/** Valid form **/
						$form = new CMS_Form_Default(array("xml" => $xml->nodes));
						if( $form->isValid($datas) )
						{
							/** Verify emails in bdd for this type **/
							$contact = Contact_Object_Contact::get(array("type" => $_POST["type_form"]));
							if( !$contact->emails && !$contact->emailsCci )
								throw new Zend_Exception(_t("No email is configure for this form"));
							
							/** Get Smarty & Change path tpl **/
					    	$view = Zend_Layout::getMvcInstance()->getView();
					    	$path = $view->getScriptPaths();
					    	$view->setScriptPath($typesPath . "/" . $_POST["type_form"]);
							
				    		$nodes = array();					
				    		/** All Field Form **/
				            foreach ($xml->nodes->elements as $key => $value)
				            {
				            	/** Assign value **/
				            	$val = (is_string($_POST[$key])) ? htmlspecialchars($_POST[$key]) : $_POST[$key];
				            	$view->assign($key, $val);
				            }
				            
				            if(isset($_POST["url"])){
				            	$view->urlFrom = "http://".$_SERVER["SERVER_NAME"].htmlspecialchars($_POST["url"]);
				            	
				            }

					    	/** Generaet content email **/
					        $mailContent = $view->renderInnerTpl("email.tpl");
							
					        /** Config Mail **/
					        $mail = new Zend_Mail('UTF-8');
					        
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
					        
							$mail->setBodyHtml($mailContent)
								->setFrom($_SERVER["SERVER_NAME"], $_SERVER["SERVER_NAME"])
								->setSubject("Message envoyé depuis vote site: ". $_SERVER["SERVER_NAME"]);
							
							/** Send Mail **/
							if( !$mail->send() )
								throw new Zend_Exception(_t("Error send mail"));
								
							/** Remise du chemin des templates d'origines **/
		       				$view->setScriptPath($path); 	
						}
						else
							throw new Zend_Exception(_t("This form contains errors"));
					}
					else
						throw new Zend_Exception(_t("This type form don't available to bloc"));
				}
				else
					throw new Zend_Exception(_t("This type form don't exist"));
			}
			
			$result['error'] = false;
		}
		catch (Exception $e)
		{
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}
		
		echo json_encode($result);
	}
}
