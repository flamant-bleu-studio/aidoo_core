<?php

class Sitemap_CronController extends Zend_Controller_Action
{
	public function indexAction()
	{
		Sitemap_Lib_Sitemap::updateSitemap();
		
		echo "ok";die;
	}
}