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

class CMS_Smarty_Plugins_Function {
	
	public static function routeShort($params, $content){
		
		$action = $params['action'];
		unset($params['action']);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		
		return $helper->getFrontController()->getBaseUrl().$helper->short($action, $params);
	}	
	
	public static function routeFull($params, $content){
		
		$routeName = $params['route'];
		unset($params['route']);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		return $helper->getFrontController()->getBaseUrl().$helper->full($routeName, $params);
	}

	/**
	 * Sample : {image folder='articles' name='test.jpg', size='default'}
	 */
	public static function image($params, $content)
	{
		return CMS_Image::getLink($params['folder'], $params['name'], $params['size']);
	}
	
	public static function AppendAnalyticsTracking($params, &$smarty)
	{
		$config = CMS_Application_Config::getInstance();
		$social = json_decode($config->get('social'), true);
		
		if (!$social || empty($social['googleanalytics']))
			return '';
		
		return "<script type='text/javascript'>
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '".$social['googleanalytics']."']);
		  _gaq.push(['_trackPageview']);
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>";
	}
	
	public static function GenerateStyleBackground($params, &$smarty)
	{
		$page = CMS_Page_Current::getInstance();
		$template = $smarty->get_template_vars('template');
		
		if (!($template instanceof Blocs_Object_Template))
			return '';
		
		if ($template->bgType == 1) {
			
			if ($template->bgRepeat == 1)
				$repeat = 'repeat-x';
			else if ($template->bgRepeat == 2)
				$repeat = 'repeat-y';
			else if ($template->bgRepeat == 3)
				$repeat = 'repeat';
			else
				$repeat = 'no-repeat';
			
			return '<style>
				html {
					background-image: url('.CMS_Image::getLink("templates", $template->bgPicture).');
					background-repeat: '.$repeat.';
					background-position: top center;
					background-color: #'.$template->bgColor1.';
				}
			</style>';
		}
		else if ($template->bgType == 2) {
			return '<style>
				html {
					background-color: #'.$template->bgColor1.';
				}
			</style>';
		}
		else if ($template->bgType == 3) {
			if (!$template->bgGradient) {
				return '<style>
					html {
						background: -webkit-gradient(linear, left top, left bottom, from(#'.$template->bgColor1.'), to(#'.$template->bgColor2.'));
						background: -moz-linear-gradient(top, #'.$template->bgColor1.', #'.$template->bgColor2.');
						filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#'.$template->bgColor1.', endColorstr=#'.$template->bgColor2.');
						-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#'.$template->bgColor1.', endColorstr=#'.$template->bgColor2.')";
					}
					</style>';
			}
			else {
				return '<style>
					html {
						background: -webkit-gradient(linear, left top, right top, from(#'.$template->bgColor1.'), to(#'.$template->bgColor2.'), color-stop(0.7, #'.$template->bgColor2.'));
						background: -moz-linear-gradient(left top, #'.$template->bgColor1.', #'.$template->bgColor2.' 70%);
						filter: progid:DXImageTransform.Microsoft.gradient(startColorStr=#'.$template->bgColor1.', endColorStr=#'.$template->bgColor2.', GradientType=1);
						-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#'.$template->bgColor1.', endColorstr=#'.$template->bgColor2.', GradientType=1)";
					}
					</style>';
			}
		}
	}
}