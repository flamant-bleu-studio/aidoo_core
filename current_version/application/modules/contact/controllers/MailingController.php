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

class Contact_MailingController extends Zend_Controller_Action
{
	public function init() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}


	private function alreadyRegisteredUser( $email)
	{
		$db = Zend_Registry::get('db');
		$exists = $db->query("SELECT * FROM wp_mailpress_users WHERE email=?",$email);
		if($exists->fetch(Zend_DB::FETCH_OBJ)) 
		{
			return true;
		}
		return false;
	}
	
	
	private function registerUser( $email)
	{
		$db = Zend_Registry::get('db');

		$ip = $_SERVER['REMOTE_ADDR'] ;
		$registerDateTime = date("Y-m-d H:i:s");

		$results = $db->query("INSERT INTO `wp_mailpress_users` (`email`, `name`, `status`, `confkey`, `created`, `created_IP`, `created_agent`, `created_user_id`, `created_country`, `created_US_state`, `laststatus`, `laststatus_IP`, `laststatus_agent`, `laststatus_user_id`) VALUES
			( ?, ?, 'active', '326f404dfdbbe339fda11bd109063617', ?, ? , 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)', 1, 'ZZ', 'ZZ', '0000-00-00 00:00:00', '', '', 0)",
		array($email, $email, registerDateTime, $ip));
			
		$lastId = $db->lastInsertId();
		
		if ($lastId)
		{
			$db->query ("INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (? , 4, 0)", $lastId);
		
			$db->query ("UPDATE wp_mailpress_users SET created_user_id=id WHERE id=?", $lastId );
		    $results = $db->query("Select * FROM wp_mailpress_users");
			$countRegistered = count($results->fetchAll(Zend_Db::FETCH_OBJ));
			$db->query ("UPDATE wp_mailpress_stats SET scount=? WHERE stype='u' AND slib='active'", $countRegistered );
			$db->query ("UPDATE wp_term_taxonomy SET count=? WHERE taxonomy='MailPress_mailing_list' AND term_taxonomy_id=4", $countRegistered );
			return true;
		}
	}


	/*---
	 *
	 *
	 */

	public function indexAction()
	{
	}

	/*--
	 *
	 */

	public function registerAction()
	{

		$response = array();

		try
		{
			if($_POST['email'])
			{
				if(@eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $_POST['email'])) 
				{
					if ($this->alreadyRegisteredUser($_POST['email']))
					{
						$response['error'] = true;
						$response['errormessage'] = "Déjà inscrit";
					}
					else
					{
						$return = $this->registerUser($_POST['email']);
						if ($return == true)
						{
							$response['error'] = false;
							$response['message'] = "Inscription enregistrée";
						}
						else
						{
							$response['error'] = true;
							$response['errormessage'] = "Erreur d'inscription";
						}
					}
				}
				else 
				{
					$response['error'] = true;
					$response['errormessage'] = "Email non valide";
				}
			}
			else
			{
					$response['error'] = true;
					$response['errormessage'] = "Email manquant";
			}
		}
		catch (Exception $e)
		{
			$response['error'] = true;
			$response['errormessage'] = $e->getMessage();
		}

		header('Content-type: application/json');
		echo json_encode($response);

	}

}

