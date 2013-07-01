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

class Jobs_FrontController extends Zend_Controller_Action
{
	private $storageFolder="/tmp/upload";

	public function indexAction()
	{
		// --- filters
		
		$contractTypeList = Jobs_Lib_Manager::getCriteria("contract_type");
		$sectorList = Jobs_Lib_Manager::getCriteria("sector");
		$domainList = Jobs_Lib_Manager::getCriteria("domain");
		
		$searchparams = array();
		
		if($this->getRequest()->isPost())
		{
			if($_POST['contractType'] && in_array($_POST['contractType'], $contractTypeList))
			{
				$selectedContractType = $_POST['contractType'];
				$searchparams['contract_type'] = $selectedContractType;
				$this->view->selectedContractType 	= $selectedContractType;
			}
			if($_POST['sector'] && in_array($_POST['sector'], $sectorList))
			{
				$selectedSector = $_POST['sector'];
				$searchparams['sector'] = $selectedSector;
				$this->view->selectedSector 		= $selectedSector;
			}
			if($_POST['domain'] && in_array($_POST['domain'], $domainList))
			{
				$selectedDomain = $_POST['domain'];
				$searchparams['domain'] = $selectedDomain;
				$this->view->selectedDomain 		= $selectedDomain;
			}
		}
		$this->view->contractTypeList 		= $contractTypeList;
		$this->view->sectorList 			= $sectorList;
		$this->view->domainList 			= $domainList;
		
		
		// --- output jobs
		
		$job = new Jobs_Object_Jobs();
		$jobs = $job->get($searchparams);

		//print_r($searchparams);
		if( $jobs > 0)
		{
			foreach ($jobs as &$job)
			{
				$job->description = implode(' ',array_slice(explode(' ',$job->description),0,20))."...";
			}
		}
		
	//	print_r($jobs);die();
		
		$this->view->jobs = $jobs;

	}

	public function viewAction()
	{
		$id = (int) $this->_request->getParam('id');

		$job = new Jobs_Object_Jobs($id);

		if(!$job)
			throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);

		$this->view->job = $job;
	}

	public function applyAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		if(!$id)
			$this->_redirect($this->_helper->route->short('candidature'));
		
		$job = new Jobs_Object_Jobs($id);
		
		if(!$job)
			throw new Zend_Exception(_t("Page not found"), 404);	
		
		$this->view->job = $job;
		
		$form = new Jobs_Form_Apply($this->storageFolder, $job);
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {

				try {
					$mail = new Zend_Mail('UTF-8');
					
					// Récupération de Smarty
					$view = Zend_Layout::getMvcInstance()->getView();
					// Récupération du chemin actuel des templates
					$path = $view->getScriptPaths();
					// Changement de chemin des templates pour selectionner celui des mails
					$view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render');
	
					$datas = array();
	
					$datas['civilite'] 		= htmlspecialchars($_POST['civilite']);
					$datas['firstName'] 	= htmlspecialchars($_POST['firstName']);
					$datas['lastName'] 		= htmlspecialchars($_POST['lastName']);
					$datas['email'] 		= htmlspecialchars($_POST['email']);
					$datas['adress'] 		= htmlspecialchars($_POST['adress']);
					$datas['cp'] 			= htmlspecialchars($_POST['cp']);
					$datas['city'] 			= htmlspecialchars($_POST['city']);
					$datas['phone'] 		= htmlspecialchars($_POST['phone']);
					$datas['object'] 		= htmlspecialchars($_POST['object']);
					$datas['message'] 		= htmlspecialchars($_POST['message']);
					 
					$view->assign("civilite", 	$datas['civilite']);
					$view->assign("firstName", 	$datas['firstName']);
					$view->assign("lastName", 	$datas['lastName']);
					$view->assign("adress", 	$datas['adress']);
					$view->assign("cp", 		$datas['cp']);
					$view->assign("city", 		$datas['city']);
					$view->assign("phone", 		$datas['phone']);
					$view->assign("email", 		$datas['email']);
					$view->assign("object", 	$datas['object']);
					$view->assign("message", 	$datas['message']);
					
					if($form->cv->isUploaded())
					{
						$form->cv->receive();
						$cv = file_get_contents($form->cv->getFileName());
						$at = $mail->createAttachment($cv);
						$at->filename = basename($form->cv->getFileName());
					}
					
					$destinataires = explode(';', $job->contact);
					
					// Génération du html
					$content = $view->renderInnerTpl("email.tpl");
					// Remise du chemin des templates d'origines
					$view->setScriptPath($path);
					
					$mail = $mail->setBodyHtml($content)
					->setFrom($datas['email'], $datas['firstName'].' '.$datas['lastName'])
					->setSubject("Candidature poste: ". $datas['object']);
					
					foreach ($destinataires as $destinataire) {
						if (!empty($destinataire))
							$mail->addTo($destinataire, $destinataire);
					}
					
					$mail->send();
					
					unlink($form->cv->getFileName());
					
					$this->view->sentOk = true;
				}
				catch(Exception $e){
					$this->view->sentError = true;
				} 
			}
		}
		

		if ($id)
			$action = $this->_helper->route->short('apply', array("id" => $id));
		else
			$action = $this->_helper->route->short('apply');
			 
		$form->setAction($action);

	}
	
	public function candidatureAction()
	{
		$path = realpath(dirname(__FILE__)).'/../types/default';
		if( !file_exists( $path ))
			throw new Zend_Exception(_t("File XML not found"));
		
		$xml = new Zend_Config_Xml($path.'/type.xml');
		
		/** Create Form **/
		$form = new CMS_Form_Default(array("xml" => $xml->nodes));
		
		$form->setAction($this->_helper->route->short('candidature'));
		
		/** Add button valid **/
		$item = new Zend_Form_Element_Submit("send");
		$item->setValue(_t("Send"));
		$form->addElement($item);
		
		/** POST **/
		if( $this->getRequest()->isPost() ) 
		{
			/** Valid **/
			if( $form->isValid($_POST) )
			{
				$view = Zend_Layout::getMvcInstance()->getView();
		    	$pathView = $view->getScriptPaths();
		    	$view->setScriptPath($path);
		    	
				/** If nodes exists **/
		       	if( $xml->nodes )
		       	{
		    		$nodes = array();
		    		/** All Field Form **/
		            foreach ($xml->nodes->elements as $key => $value)
		            {
		            	if( $xml->nodes->elements->$key->type != "file" ) {
			            	/** Assign value **/
			            	$view->assign($key, htmlspecialchars($_POST[$key]));
		            	}
		            }
		    	}
		    	else
		    		throw new Zend_Exception(_t("Missing nodes"));
		    	
		    	$config = json_decode(CMS_Application_Config::getInstance()->get("contactEmail"), true);
		       	$mail_contact = explode(';', $config["mod_jobs_contact"]);
		    	
		       	if( empty($mail_contact) )
		       		throw new Zend_Exception(_t("Missing mail contact"));
				
				/** Config Mail **/
		        $mail = new Zend_Mail('UTF-8');
		        
				foreach ($mail_contact as $destinataire) {
					if (!empty($destinataire))
						$mail->addTo($destinataire, $destinataire);
				}
			    
		    	/** Generate content email **/
		        $mailContent = $view->renderInnerTpl("email.tpl");
		        
		    	$mail->setBodyHtml($mailContent)
					 ->setFrom($_POST['email'], $_POST['nom'])
					 ->setSubject("Message envoyé depuis vote site: Candidature spontanée");
				
				foreach ($xml->nodes->elements as $key => $value)
	            {
	            	if( $xml->nodes->elements->$key->type == "file" ) {
		            	if( isset($_FILES[$key]) && $_FILES[$key]["size"] > 0 ) {
		            		$contentFile = file_get_contents($_FILES[$key]["tmp_name"]);
		            		$mail->createAttachment($contentFile, $_FILES[$key]['type'], Zend_Mime::DISPOSITION_INLINE, Zend_Mime::ENCODING_BASE64, $_FILES[$key]["name"]);
		            	}
		            	
		            	unlink($_FILES[$key]["tmp_name"]);
	            	}
	            }
					 
		        /** Send Mail **/
				if( $mail->send() )
					$this->view->sendOk = true;
				else
					$this->view->sendError = true;
		       	
				$view->setScriptPath($pathView); 
			}
		}
		
		$this->view->form = $form;
	}

}

