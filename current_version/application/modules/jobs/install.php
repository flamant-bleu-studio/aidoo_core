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
$page->title 		= array(1 => "Voir les annonces");
$page->type 		= "jobs-list";
$page->url_system 	= $this->_helper->route->full('jobs', array("action" => "index"));
$page->enable 		= 1;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Candidature spontanée");
$page->type 		= "jobs-apply";
$page->url_system 	= $this->_helper->route->full('jobs', array("action" => "candidature"));
$page->enable 		= 1;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Répondre à une annonce");
$page->type 		= "jobs-list";
$page->url_system 	= $this->_helper->route->full('jobs', array("action" => "apply"));
$page->enable 		= 1;
$page->save();