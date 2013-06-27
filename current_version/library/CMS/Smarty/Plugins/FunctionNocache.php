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

class CMS_Smarty_Plugins_FunctionNocache {
	
	public static function messages($params, $content){

		$types = array(
			CMS_Error_DisplayManager::TYPE_ERROR,
			CMS_Error_DisplayManager::TYPE_MESSAGE,
			CMS_Error_DisplayManager::TYPE_WARNING
		);

		$displayManager = CMS_Error_DisplayManager::getInstance();

		
		$html = "";
		$display = false;
		
		foreach ($types as $type) {

			$messages = $displayManager->getMessages($type);

			/*
			if (!$messages){
				$messages = $displayManager->getCurrentMessages($type);
			}*/
				
			if ($messages) {
				
				$display = true;
				
				$html .= '<ul class="unstyled alert ';
				
				switch ($type) {
					case CMS_Error_DisplayManager::TYPE_MESSAGE:
						$html .= 'alert-success';
						break;
					case CMS_Error_DisplayManager::TYPE_WARNING:
						break;
					default:
						$html .= 'alert-error';
						break;
				}
				
				$html .= '">';
				

				foreach ( $messages as $message ) {
					$html .=  '<li>'.$message->message.'</li>';
				}
				
				$html .= '</ul>';
			}
		}
		
		if($display)
			return $html;
		
		return "";
	}
	
	public static function formatDate($params, Smarty_Internal_Template $smarty)
	{	
		$date 	= (isset($params['date'])) 		? $params['date'] 	: date('Y-m-d H:i:s');
		$locale = (isset($params['locale'])) 	? $params['locale'] : CMS_Application_Config::getInstance()->getActiveLang();
		$format = (isset($params['format'])) 	? $params['format'] : 'EEE F HH:mm';
		
		$formatDate = new Zend_Date($date, 'yyyy-MM-dd hh:mm:ss', $locale);

		return $formatDate->toString($format);
	}
	
	public static function appendFile($params, $content){

		$src = $params['src'];
		unset($params['src']);
		
		$type = $params['type'];
		unset($params['type']);
		
		$cache = (isset($params['cache']) && $params['cache'] == 'true') ? 1 : 0;
		unset($params['cache']);
		
		$append = CMS_Application_ProcessLayout::getInstance();
		
		if($type == "css")
		{
			$media = isset($params['media']) ? $params['media'] : "all";
			unset($params['media']);
		
			$append->appendCssFile($src, $cache, $media);
		}
		else if($type == "js")
		{
			$append->appendJsFile($src, $cache);
		}
		else if($type == "jquery")
		{
			$append->appendJQuery($src, $cache);
		}

	}
	
	
	public static function appendInline($params, $content) {

		$src = $params['content'];
		unset($params['content']);
		
		$type = $params['type'];
		unset($params['type']);

		$append = CMS_Application_ProcessLayout::getInstance();
		
		if($type == "css")
		{
			$append->appendCssScript($src);
		}
		else if($type = "js")
		{
			$place = ($params['place']) ? $params['place'] : null;
			unset($params['place']);
			$append->appendJsScript($src,$place);
		}
		
	}

	public static function AppendJQueryLibs() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHTMLJQueryLibs();
	}
	
	public static function AppendJsFiles() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHtmlJsFiles();
	}
	
	public static function AppendJsScripts() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHtmlJsScripts();
	}
	
	public static function AppendCssFiles() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHtmlCssFiles();
	}
	
	public static function AppendCssScripts() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHtmlCssScripts();
	}
	
	public static function AppendJsScriptsBottom() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHTMLJsScriptsBottom();
	}
	
	public static function AppendCacheCssJs() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getCacheCssJs();
	}
	
	public static function AppendHeadContent() {
		$processLayout	= CMS_Application_ProcessLayout::getInstance();
		return $processLayout->getHeadContent();
	}
	
	public static function AppendActionHook($hook_name) {
		CMS_Application_Hook::getInstance()->exec_actions($hook_name);
	}
	
	
	public static function formButtons($params, $content) {
		
		$link = $params['cancelLink'];
		unset($params['cancelLink']);
		
		
		$html = '
			<div class="row-fluid form_submit">
				<button class="btn btn-large btn-primary" name="submit" value="true">Sauvegarder & Rester</button>
				<button class="btn btn-large btn-success" name="submitandquit" value="true">Sauvegarder & Quitter</button>';
		
		if($link)
			$html .= '<a href="'.$link.'" class="btn btn-mini btn-danger" style="margin:18px 0 0 4px;">Annuler & Quitter</a>';
		
		$html .=  '</div>';
		
		return $html;
	
	}

}
