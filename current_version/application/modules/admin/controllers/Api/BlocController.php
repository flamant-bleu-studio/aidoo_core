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

class Admin_Api_BlocController extends CMS_Controller_Api{

	public function indexAction(){}
	public function getAction(){}
	public function postAction(){}
	public function putAction(){}
	public function deleteAction(){}
	public function headAction() {}
	
	public function rssAction()
	{
		if(!$params = CMS_Application_Tools::checkPOST('id_bloc')) 
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
		
		$bloc_rss = Bloc_Rss_Rss::getOne((int) $params['id_bloc']);
		
		$content = file_get_contents('http://'.$bloc_rss->url_rss);
		$x = new SimpleXmlElement($content);
				
		$nb = $bloc_rss->nb_rss;
		$count = 0;
		
		$rss = "<ul>";
		foreach($x->channel->item as $entry) {
			if ($nb == $count)
				break;
			$pubDate = new DateTime($entry->pubDate);
			
			$rss .= "	<li>
								<a href=".$entry->link." title=".$entry->title." class='rss_link' target='_blank'>" . $entry->title . "</a>
								<span id='rss_date'>Publié le ".$pubDate->format('d/m/Y')."</span>
								<span id='rss_desc'>". $entry->description."</span>
							</li>";
			$count++;
		}		  
		$rss .= "</ul>";
		
		// Supprime les vidéo et les script (éviter un minimum les XSS)
		$rss = preg_replace('@<object[^>]*?>.*?</object>@si', '', $rss);
		$rss = preg_replace('@<script[^>]*?>.*?</script>@si', '', $rss);
				
		$this->view->rss = $rss;
	}
	
	public function weatherAction()
	{
		if(!$params = CMS_Application_Tools::checkPOST('id_bloc'))
			return $this->view->codeError = self::ERROR_CODE_PARAM; // Missing param
		
		$bloc_weather = Bloc_Weather_Weather::getOne((int) $params['id_bloc']);
		
		$frontend = array(
			       'lifeTime'     => 1800,
			       'automatic_serialization' => true
		);
		$backend = array('cache_dir' => CMS_PATH.'/tmp/zend_cache/');
		$cache = Zend_Cache::factory('Core', 'File', $frontend, $backend);
				
		$cache_weather= $cache->load('weather'.$params['id_bloc']);
		
		$content = null;
		if ($cache_weather === true) {
			$content 	= $cache_weather['xml'];
			$date		=	$cache_weather['date'];
		}
		
		// Si le cache n'est pas créé
		if (!$content) {			
			$content = file_get_contents('http://www.myweather2.com/developer/forecast.ashx?uac='.$bloc_weather->code.'&output=xml&query='.$bloc_weather->latlong);
			$date = new DateTime();
			$item_cache = array('date' => $date, 'xml' => $content);
			$cache->save($item_cache, 'weather'.$params['id_bloc']);
		}
				
		$x = new SimpleXmlElement($content);

		$weather = 'Heure du rapport : '.$date->format('d/m/Y').' à '.$date->format('H:i:s');
		$weather .= "<ul>";
		
		$weather .= '<li id="weather_temp"> Température : '.$x->curren_weather->temp.' °C </li>';
		$weather .= '<li id="weather_windspeed"> Vitesse du vent : '.$x->curren_weather->wind->speed.' km/h </li>';
		$weather .= '<li id="weather_winddir"> Direction du vent : '.$x->curren_weather->wind->dir.' </li>';
		$weather .= '<li id="weather_humidity"> Humidité : '.$x->curren_weather->humidity.' % </li>';
		$weather .= '<li id="weather_pressure"> Pression : '.$x->curren_weather->pressure.' hPa </li>';
		
		$weather .= "</ul>";
		$weather .= "<a class='weather_backlink' href='http://www.myweather2.com' target='_blank'>Weather provided by MyWeather2.com</a>";
		
		$this->view->weather = $weather;
	}
}
