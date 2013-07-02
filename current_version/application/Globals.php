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

/**
 * Add notice message
 *
 * @param $message string message to display
 * @deprecated use _success or _info
 */
function _message($message)
{
	CMS_Error_DisplayManager::getInstance()->addMessage($message, CMS_Error_DisplayManager::TYPE_MESSAGE);
}

/**
 * Add error message
 *
 * @param $message string message to display
 */
function _error($message)
{
	CMS_Error_DisplayManager::getInstance()->addMessage($message, CMS_Error_DisplayManager::TYPE_ERROR);
}

/**
 * Add info message
 *
 * @param $message string message to display
 */
function _info($message)
{
	CMS_Error_DisplayManager::getInstance()->addMessage($message, CMS_Error_DisplayManager::TYPE_INFO);
}

/**
 * Add success message
 *
 * @param $message string message to display
 */
function _success($message)
{
	CMS_Error_DisplayManager::getInstance()->addMessage($message, CMS_Error_DisplayManager::TYPE_SUCCESS);
}

/**
 * Add warning message
 *
 * @param $message string message to display
 */
function _warning($message)
{
	CMS_Error_DisplayManager::getInstance()->addMessage($message, CMS_Error_DisplayManager::TYPE_WARNING);
}

/**
 * Translate string
 *
 * @return string translated string
 */
function _t($message)
{
	return Zend_Registry::get('translate')->translate($message);
}

/**
 * Translate string width plural
 *
 * @return string plural translated string
 */
function _n($single, $plural, $number)
{
	return Zend_Registry::get('translate')->translate(array($single, $plural, $number));
}

/**
 * Debug var function
 */
function pre_dump($var, $die = false)
{
	echo '<pre>';
	if (is_array($var))
		echo print_r($var, true);
	else
		echo var_dump($var);
	echo '</pre>';
	
	if ($die === true)
		die;
}