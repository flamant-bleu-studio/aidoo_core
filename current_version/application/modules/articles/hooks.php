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

/**
 * Menu administration
 */
function appendArticlesTabMenu($tabs)
{
	$backAcl = CMS_Acl_Back::getInstance();
	if($backAcl->hasPermission("mod_articles", "view"))
	{
		$tabs['siteLife']['children'][] = array("title" => "Articles", "routeName" => "articles_back",  "moduleName" => "articles", "controllerName" => "back", "icon" => "doc.png");
	}
	return $tabs;
}

$hooks->add('Back_Main_Menu_Generate', 'appendArticlesTabMenu', 140);

/**
 * @param Articles_Object_Article $object
 */
function extractArticleToSearch($object) {
	$datas = array();
	
	$datas['item_id'] 	= get_class($object).'-'.$object->id_article;
	$datas['type'] 		= get_class($object);
	$datas['datas']['typeName'] 	= 'Article';
	
	$datas['content'][] = $object->chapeau;
	
	$availableNodesSearch = array();
	
	$xml_file = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/articles/'.$object->type.'/type.xml';
	$xml = new Zend_Config_Xml($xml_file);
	$xml = $xml->toArray();
	
	foreach ($xml['nodes'] as $node) {
		$name = key($node);
		
		if (!in_array($node[$name]['type'], array('text', 'textarea')))
			continue;
		
		$isSearchable = ($node[$name]['options']['searchable'] === null) ? true : (($node[$name]['options']['searchable'] === "true" ? true : false));
		
		if ($isSearchable)
			$availableNodesSearch[] = $name;
	}
	
	foreach ($object->nodes as $name => $node)
		if (in_array($name, $availableNodesSearch))
			$datas['content'][] = $node;
	
	$datas['datas']['title'] = $object->title;
	$datas['datas']['isVisible'] = $object->status;
	
	$datas['datas']['picture']			= $object->image;
	$datas['datas']['picture_folder']	= 'articles';
	
	$datas['url_front'] = array("route" => "articles", "params" => array("module" => "articles", "controller" => "front", "action" => "view", "id" => $object->id_article));
	$datas['url_back']  = array("route" => "articles_back", "params" => array("module" => "articles", "controller" => "back", "action" => "edit", "id" => $object->id_article));
	
	return $datas;
}

function regenerateSearchableContentArticles($return, $office)
{
	if ($office == 'front' || $office == 'back') {
		if ($office == 'front')
			$filters = array('status' => Articles_Object_Article::STATUS_PUBLISH);
		else if ($office == 'back')
			$filters = null;
		
		$articles = Articles_Object_Article::get($filters, null, null, 'all');
		
		foreach ($articles as $article)
			$return[] = extractArticleToSearch($article);
	}
	
	return $return;
}

$hooks->add('regenerateSearchableContent', 'regenerateSearchableContentArticles');

/**
 * @param Articles_Object_Article $object
 */
function updateArticle($object) {
	
	$datas = extractArticleToSearch($object);
	
	// FRONT
	if ($object->status == Articles_Object_Article::STATUS_DRAFT)
		CMS_Search_Front::getInstance()->deleteItem($datas['item_id']);
	else
		CMS_Search_Front::getInstance()->updateItem($datas);
	
	// BACK
	CMS_Search_Back::getInstance()->updateItem($datas);
}

function deleteArticle($id_article) {
	$item_id = 'Articles_Object_Article-'.$id_article;
	
	CMS_Search_Front::getInstance()->deleteItem($item_id);
	CMS_Search_Back::getInstance()->deleteItem($item_id);
}

$hooks->add('Articles_Object_Article_AfterInsert', 'updateArticle');
$hooks->add('Articles_Object_Article_AfterUpdate', 'updateArticle');
$hooks->add('Articles_Object_Article_AfterDelete', 'deleteArticle');