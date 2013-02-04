<?php

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "{Exemple}");
$page->type 		= "{exemple}";
$page->url_system 	= $this->_helper->route->full('{exemple}', array("action" => "index"));
$page->enable 		= 0;
$page->save();

