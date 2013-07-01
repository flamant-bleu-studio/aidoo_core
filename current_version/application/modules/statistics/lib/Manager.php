
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

class Statistics_Lib_Manager {
	
	public function authentification($account, $password) {
		$login = Zend_Gdata_ClientLogin::getHttpClient($account, $password, 'analytics');
		$token = $login->getClientLoginToken();
		return $token;
	}
		
	public function getProfiles($token) {
		
		$url	= "https://www.google.com/analytics/feeds/accounts/default";
		$client 	= new Zend_Http_Client($url);
		$client->setHeaders( "Authorization: GoogleLogin auth=".$token );
		$r 	= $client->request(Zend_Http_Client::GET);
		$xmlBody 	= $r->getBody();
		$xmlBody 	= str_replace('dxp:','',$xmlBody);

		//
		// utilisation de SimpleXML
		$xml 		= simplexml_load_string($xmlBody);
		$tProfiles	= array();	// tableau qui va recevoir tous les profils

		//
		// pour chaque résultat
		foreach($xml->entry as $entry) {

			//
			// tableau qui va contenir temporairement les infos d'un seul profil
			$tVal 	= array();
				
			//
			// récupérer les infos principales
			$tVal['title']	= strval( $entry->title );
			$tVal['id']		= strval( $entry->id );
			$tVal['tableId']= strval( str_replace('ga:', '', $entry->tableId) );
			
			//
			// récupérer les propriétés, qui sont stockées sous forme d'attribut, dans des noeuds "property"
			foreach($entry->property as $r) {
				$name	= strval($r['name']);
				$name	= trim( str_replace('ga:', '', $name) );
				$tVal[$name] = strval($r['value']);
			}
			
			//
			// ajouter au tableau final
			$tProfiles[$tVal['tableId']] = $tVal;
		}

		//
		// afficher
		return $tProfiles;
	}	
	
		public function getDatas($id, $token, $start, $end) {
			$url = "https://www.google.com/analytics/feeds/data?ids=ga:".$id;
			$url .= "&metrics=ga:visits,ga:pageviews,ga:pageviewsPerVisit,ga:timeOnSite";
			$url .= "&start-date=".$start;
			$url .= "&end-date=".$end;
			$url .= "&max-results=50";	
			
			$client	= new Zend_Http_Client($url);
			$client->setHeaders( "Authorization: GoogleLogin auth=".$token );
			$r = $client->request(Zend_Http_Client::GET);
			
			$xmlBody = $r->getBody();
			$xmlBody = str_replace('dxp:','',$xmlBody);
			
			$xml = simplexml_load_string($xmlBody);
			//print_r($xmlBody);die();
			$tPageviews = array();
			
			foreach($xml->entry as $entry) {
				$tVal = array();
	
				foreach($entry->metric as $r) {
					$name = strval($r['name']);
					$name = trim( str_replace('ga:', '', $name) );
					$tVal[$name] = strval($r['value']);
				}
				
				//
				// ajouter au tableau final
				$tPageviews['visits'] = $tVal['visits'];
				$tPageviews['pageviews'] = $tVal['pageviews'];
				$tPageviews['pageviewsPerVisit'] = $tVal['pageviewsPerVisit'];
				$tPageviews['timeOnSite'] = $tVal['timeOnSite'];
				
				return $tPageviews;
			}
		}
		
		public function duree($time) {
			$tabTemps = array("j" => 86400,
			"h" => 3600,
			"min" => 60,
			"sec" => 1);
			
			$result = "";
			
			foreach($tabTemps as $uniteTemps => $nombreSecondesDansUnite) {
				$$uniteTemps = floor($time/$nombreSecondesDansUnite);
				$time = $time%$nombreSecondesDansUnite;
			
				if($$uniteTemps > 0 || !empty($result))
					$result .= $$uniteTemps." $uniteTemps ";
			}
			return $result;
		} 
		
		public function renderStats($account, $password, $profile, $month, $year) {
			
			$analytics = new Statistics_Lib_Analytics($account, $password);			
			$profiles = $analytics->getProfileList();
			
			foreach($profiles as $key => $val) {
				if($profile == $val) {
					$gProfileId = $key;
					break;
				}
			}

			$token = Statistics_Lib_Manager::authentification($account, $password);		
			$profileId = str_replace('ga:', '', $gProfileId);
			$titleSite = $profiles[$gProfileId];
			
			$date_start = time() - 2592000; // date de départ commence un mois avant la date actuelle
			$start = date('Y-m-d', $date_start);
			$end = date('Y-m-d');
			
			$pageviews = Statistics_Lib_Manager::getDatas($profileId, $token, $start, $end);
			$analytics->setProfileById($gProfileId);
					
			$tStats = array();

			$analytics->setMonth($month, $year);
			//$analytics->setDates($start, $end);
			$visits = $analytics->getVisitors();
			$views = $analytics->getPageviews();
			
			$bounce = $analytics->getBounce();//print_r($bounce);die();
			$newsVisit = $analytics->getNewsVisit();
			
			/* build tables */
			if(count($visits)) {
				foreach($visits as $day=>$visit) { 
					$flot_datas_visits[] = '['.$day.','.$visit.']';
					$flot_datas_views[] = '['.$day.','.$views[$day].']';
				}
				$flot_data_visits = '['.implode(',',$flot_datas_visits).']';
				$flot_data_views = '['.implode(',',$flot_datas_views).']';		    
			}
			
			$tStats['site'] = $titleSite;
			$tStats['start'] = $start;
			$tStats['end'] = $end;
			$tStats['flot_data_visits'] = $flot_data_visits;
			$tStats['flot_data_views'] = $flot_data_views;
			$tStats['visits'] = $pageviews['visits'];
			$tStats['pageviews'] = $pageviews['pageviews'];
			$tStats['pageviewsPerVisit'] = floor($pageviews['pageviewsPerVisit']);
			$tStats['timeOnSite'] = Statistics_Lib_Manager::duree($pageviews['timeOnSite']/$pageviews['visits']);
			$tStats['bounceVisit'] = round($bounce[date("d")]);
			$tStats['newsVisit'] = round($newsVisit[str_replace("/", "", $profile)]);
			
			return $tStats;
		}
}