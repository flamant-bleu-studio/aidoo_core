<?php

class Sitemap_Lib_Sitemap
{
	public static function updateSitemap()
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		
		$root = $dom->createElement('urlset');
		$root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		
		$root = $dom->appendChild($root);
		
		/**
		 * Pages
		 */
		
		$pages = CMS_Page_Object::get(array('enable' => 1, 'visible' => 1, 'wildcard' => 0));
		
		foreach ($pages as $page) {
			$nodePage = $dom->createElement('url');
			$nodePage = $root->appendChild($nodePage);
			
			$nodePage->appendChild($dom->createElement('loc', 'http://'.$_SERVER['SERVER_NAME'].BASE_URL.$page->getUrl()));
		}
		
		/**
		 * Véhicules
		 */
		
		$routeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		
		$vehicules = Selectup_Lib_Vehicule::getVehicules();
		
		foreach ($vehicules as $vehicule) {
			$nodePage = $dom->createElement('url');
			$nodePage = $root->appendChild($nodePage);
			
			$nodePage->appendChild(@$dom->createElement('loc', 'http://'.$_SERVER['SERVER_NAME'].BASE_URL.$routeHelper->full('selectupVehicule', array('action' => 'fiche', 'id' => $vehicule->fiche->id_vehicule, 'object' => $vehicule->fiche))));
		}
		
    	file_put_contents(PUBLIC_PATH . '/sitemap.xml', $dom->saveXML());
	}
}