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

class CMS_Controller_Plugin_Log extends Zend_Controller_plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		/**
		 * Récupère la configuration log du site
		 */
		$config 	= CMS_Application_Config::getInstance();
		$logConfig 	= json_decode($config->get('logConfig'), true);
		
		$tempConfig = array();
		
		if ($logConfig) {
			
			// Configuration d'écriture dans un fichier
			if ($logConfig['log_stream']) {
				
				$configStream = array(
					'writerName' => 'Stream',
					'writerParams' => array(
						'stream' => CMS_PATH.'/tmp/log/'.UNIQUE_ID.'.txt'
					),
					'formatterName' => 'Simple',
					'formatterParams' => array(
						'format' => '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL,
					)
				);
				
				if ($logConfig['log_stream_min_level']) {
					
					$configStream['filterName'] = 'Priority';
					$configStream['filterParams'] = array(
						'priority' => $logConfig['log_stream_min_level'],
					);
				}
				
				array_push($tempConfig, $configStream);
			}
			
			// Configuration d'envoit d'email
			if ($logConfig['log_mail']) {
				
				$mail = new Zend_Mail('UTF-8');
				$mail->setFrom('log@flamant-bleu.com')
		     		 ->addTo($logConfig['log_mail_to']);
		     	
		     	$configMail = array(
					'writerName' => 'Mail',
					'writerParams' => array(
						'mail' => $mail,
						'subjectPrependText' => $_SERVER['SERVER_NAME']
					)
		     	);
		     	
				if ($logConfig['log_mail_min_level']) {
					
					$configMail['filterName'] = 'Priority';
					$configMail['filterParams'] = array(
						'priority' => $logConfig['log_mail_min_level'],
					);
				}
		     	
		     	array_push($tempConfig, $configMail);
			}
			
			// Configuration d'enregistrement en base de donnée
			if ($logConfig['log_db']) {
				
				$configDb = array(
					'writerName' => 'Db',
					'writerParams' => array(
						'db' 		=> Zend_Registry::get('db'),
						'table'		=> 'log',
						'columnMap' => array(
							'priority' 		=> 'priority',
							'priorityName' 	=> 'priorityName',
							'message'		=> 'message',
							'addtime' 		=> 'timestamp'
						),
					),
				);
				
				if ($logConfig['log_db_min_level']) {
					
					$configDb['filterName'] = 'Priority';
					$configDb['filterParams'] = array(
						'priority' => $logConfig['log_db_min_level'],
					);
				}
				
				array_push($tempConfig, $configDb);
			}
			
			// Configuration d'envoit des données sur firebug
			if ($logConfig['log_firebug']) {
				
				$configFirebug = array(
					'writerName' => 'Firebug',
					'formatterName' => 'Simple',
					'formatterParams' => array(
						'format' => '%priorityName% : %message%',
					),
				);
				
				if ($logConfig['log_firebug_min_level']) {
					
					$configDb['filterName'] = 'Priority';
					$configDb['filterParams'] = array(
						'priority' => $logConfig['log_firebug_min_level'],
					);
				}
				
				array_push($tempConfig, $configFirebug);
			}
		}
		
		if (!empty($tempConfig))
			$logger = CMS_Log::factory($tempConfig);
		else
			$logger = new CMS_Log();
		
		/**
		 * Set registry "log"
		 */
		Zend_Registry::set('log', $logger);
	}
}