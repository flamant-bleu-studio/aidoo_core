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

global $multi_site_prefix;

/** Delete table **/
$db = Zend_Registry::get('db');
if ($db)
{
	$query = "DROP TABLE IF EXISTS ".$multi_site_prefix."campaign;";
	try
	{
		$db->query($query);
	}
	catch (Exception $e)
	{
		die("SQL ERROR: ".$query."<br/><br/>".$e->getMessage());
	}
	
	$query = "DROP TABLE IF EXISTS ".$multi_site_prefix."campaign_advert;";
	try
	{
		$db->query($query);
	}
	catch (Exception $e)
	{
		die("SQL ERROR: ".$query."<br/><br/>".$e->getMessage());
	}
}

/** Delete permission **/
$backAcl = CMS_Acl_Back::getInstance();
$backAcl->deletePermissions("mod_advertising");
$backAcl->deletePermissions("mod_advertising-default");

