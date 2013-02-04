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

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Middle office article");
$page->type 		= "articles-middle";
$page->url_system 	= $this->_helper->route->full('articles_middle', array("action" => "index"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Mes articles");
$page->type 		= "articles-middle";
$page->url_system 	= $this->_helper->route->full('articles_middle', array("action" => "my-articles"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Ajouter un article");
$page->type 		= "articles-middle";
$page->url_system 	= $this->_helper->route->full('articles_middle', array("action" => "add-article"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Editer un article");
$page->type 		= "articles-middle";
$page->url_system 	= $this->_helper->route->full('articles_middle', array("action" => "edit-article"));
$page->enable 		= 0;
$page->wildcard		= 1;
$page->save();