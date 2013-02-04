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

class Admin_UploadController extends Zend_Controller_Action
{
	public function uploadAction()
	{
		try
		{
			// Nom de l'element pour récupérer la configuration
			if(!$elementName = htmlentities($_POST['name']))
				throw new Exception(_t('Element name unknow, unable to load configuration'));
			
			// Vérification de l'existance de la session
			if(!Zend_Session::namespaceIsset('multiUpload-' . $elementName))
				throw new Exception(_t('Session expire or you are not loggued'));
			
			// récupération de la session et de la configuration
			$session = new Zend_Session_Namespace('multiUpload-' . $elementName);
			$options = $session->options;
			
			// Vérification des droits 
			if($options['adminOnly'] == true){
				$auth 		= Zend_Auth::getInstance();
				$backAcl 	= CMS_Acl_Back::getInstance();
				
				if(!$auth->hasIdentity() || !$backAcl->hasPermission('admin', 'login'))
					throw new Exception(_t('You don\'t have permission to upload files'));
			}
			
			// Paramètres obligatoires
			if(!isset($options["uploadPath"]))
				throw new Exception(_t('Unknow upload path'));
			
			$image = new CMS_Image($options);
			$datas[] = $image->upload();
		}
		catch(Exception $e){
			$datas = array(array('error' => $e->getMessage()));
		}
		
		if(DISABLE_CORE_PAGE){
			die(json_encode($datas));
		}
		
        echo json_encode($datas);
	}
	
	public function deleteAction(){
		
		$result = array();
		
		try {
			$folder = $_GET['folder'];
			$name = $_GET['name'];
			
			if (isset($folder) && isset($name)) {
				CMS_Image::delete($folder, $name);
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