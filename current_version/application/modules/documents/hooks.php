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

$hooks = CMS_Application_Hook::getInstance();

/*
 * Ajout des types de documents
 */
function ApiCreateDocument($tab)
{
	$backAcl = CMS_Acl_Back::getInstance();
	if (!$backAcl->hasPermission('mod_documents', 'create')) {
		return $tab;
	}
	
	$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');

	$typesPath = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/documents';
	$directory = new DirectoryIterator($typesPath);

    foreach ($directory as $fileinfo)
    {
    	if(!$fileinfo->isDir())
    		continue;
    	
		if (file_exists($fileinfo->getPathname().'/type.xml'))
		{
			$desc = new Zend_Config_Xml($fileinfo->getPathname().'/type.xml');
			
			$tab["documents_".$fileinfo->getFilename()] = array(
				"name" => "Contenu ".$desc->name,
				"api_name" => "Documents_Lib_Api",
				"api_params" => array("type" => $fileinfo->getFilename())
			);
		}
	}
	
	return $tab;
}

$hooks->add('listCreateApi', 'ApiCreateDocument');

/**
 * Menu admin
 */
function appendDocumentTabMenu($tabs)
{
	$backAcl = CMS_Acl_Back::getInstance();
	
	if($backAcl->hasPermission("mod_documents", "view"))
		$tabs['siteManage']['children'][] = array("title" => "Mes pages", "routeName" => "documents_back",  "moduleName" => "documents", "controllerName" => "back", "icon" => "doc.png");
	
	return $tabs;
}

$hooks->add('Back_Main_Menu_Generate', 'appendDocumentTabMenu', 300);

/**
 * @param Documents_Object_Document $object
 */
function extractDocumentToSearch($object) {
	
	$datas = array();
	
	$datas['item_id'] 	= get_class($object).'-'.$object->id_document;
	$datas['type'] 		= get_class($object);
	$datas['datas']['typeName'] = 'Page';
	
	$availableNodesSearch = array();
	
	$xml_file = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/documents/'.$object->type.'/type.xml';
	$xml = new Zend_Config_Xml($xml_file);
	$xml = $xml->toArray();
	
	foreach ($xml['nodes'] as $node) {
		$name = key($node);
		
		if (!in_array($node[$name]['type'], array('text', 'textarea', 'TinyMCE')))
			continue;
		
		$isSearchable = ($node[$name]['options']['searchable'] === null) ? true : (($node[$name]['options']['searchable'] === "true" ? true : false));
		
		if ($isSearchable)
			$availableNodesSearch[] = $name;
	}
	
	foreach ($object->nodes as $name => $node)
		if (in_array($name, $availableNodesSearch))
			$datas['content'][] = $node;
	
	$datas['datas']['title']		= $object->title;
	$datas['datas']['isVisible'] 	= $object->status;
	
	$datas['datas']['picture']			= '';
	$datas['datas']['picture_folder']	= '';
	
	$datas['url_front'] = array("route" => "doc", "params" => array("module" => "documents", "controller" => "front", "action" => "view", "id" => $object->id_document));
	$datas['url_back']  = array("route" => "documents_back", "params" => array("module" => "documents", "controller" => "front", "action" => "edit", "id" => $object->id_document));
	
	return $datas;
}

function regenerateSearchableContentDocuments($return, $office)
{
	if ($office == 'front' || $office == 'back') {
		if ($office == 'front')
			$filters = array('status' => Documents_Object_Document::STATUS_PUBLISH);
		else if ($office == 'back')
			$filters = null;
		
		$documents = Documents_Object_Document::get($filters, null, null, 'all');
		
		foreach ($documents as $document)
			$return[] = extractDocumentToSearch($document);
	}
	
	return $return;
}

$hooks->add('regenerateSearchableContent', 'regenerateSearchableContentDocuments');

/**
 * @param Documents_Object_Document $object
 */
function updateDocument($object) {
	
	$datas = extractDocumentToSearch($object);
	
	// FRONT
	if ($object->status == Documents_Object_Document::STATUS_DRAFT)
		CMS_Search_Front::getInstance()->deleteItem($datas['item_id']);
	else
		CMS_Search_Front::getInstance()->updateItem($datas);
	
	// BACK
	CMS_Search_Back::getInstance()->updateItem($datas);
}

function deleteDocument($id_document) {
	$item_id = 'Documents_Object_Document-'.$id_document;
	
	CMS_Search_Front::getInstance()->deleteItem($item_id);
	CMS_Search_Back::getInstance()->deleteItem($item_id);
}

$hooks->add('Documents_Object_Document_AfterInsert', 'updateDocument');
$hooks->add('Documents_Object_Document_AfterUpdate', 'updateDocument');
$hooks->add('Documents_Object_Document_AfterDelete', 'deleteDocument');