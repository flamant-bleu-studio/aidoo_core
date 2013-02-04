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
$page->title 		= array(1 => "Connexion de l'utilisateur");
$page->type 		= "users-middle";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "login"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Déconnexion de l'utilisateur");
$page->type 		= "users-middle";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "logout"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Inscription de l'utilisateur");
$page->type 		= "users-middle";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "register"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Edition de son profil");
$page->type 		= "users-middle";
$page->url_system 	= $this->_helper->route->full('users_middle', array("action" => "edit-profil"));
$page->enable 		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Voir le profil d'un utilisateur");
$page->type 		= "users-front";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "view"));
$page->enable 		= 1;
$page->wildcard 	= 1;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Comfirmer son inscription");
$page->type 		= "users-front";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "confirm-email"));
$page->enable 		= 0;
$page->wildcard 	= 1;
$page->visible		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Demander un changement de mot de passe");
$page->type 		= "users-front";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "forgot-password"));
$page->enable 		= 0;
$page->visible		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Comfirmer le changement de mot de passe");
$page->type 		= "users-front";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "forgot-password-confirm"));
$page->enable 		= 0;
$page->wildcard 	= 1;
$page->visible		= 0;
$page->save();

$page = new CMS_Page_PersistentObject();
$page->title 		= array(1 => "Liste des utilisateurs");
$page->type 		= "users-front";
$page->url_system 	= $this->_helper->route->full('users', array("action" => "list"));
$page->enable 		= 1;
$page->wildcard 	= 1;
$page->save();