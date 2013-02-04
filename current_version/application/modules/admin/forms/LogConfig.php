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

class Admin_Form_LogConfig extends CMS_Form_Default {
	
	public function init() {
		
		/**
		 * Options level log
		 * Define by Zend_Log
		 */
		$options = array(
			CMS_Log::EMERG 	=> 'Emergency',
			CMS_Log::ALERT 	=> 'Alert',
			CMS_Log::CRIT 		=> 'Critical',
			CMS_Log::ERR 		=> 'Error',
			CMS_Log::WARN 		=> 'Warning',
			CMS_Log::NOTICE 	=> 'Notice',
			CMS_Log::INFO 		=> 'Info',
			CMS_Log::DEBUG 	=> 'Debug',
		);
		
		$item = new Zend_Form_Element_Checkbox('log_stream');
    	$item->setLabel(_t('Save in file'));
    	$item->setDescription(_t('Check to activate'));
		$this->addElement($item);
        
		$item = new Zend_Form_Element_Select('log_stream_min_level');
		$item->setLabel(_t('Minimum level managed'));
		$item->setRequired(true);
		$item->addMultiOptions($options);
		$item->setValue(CMS_Log::DEBUG);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('log_mail');
    	$item->setLabel(_t('Send a mail'));
    	$item->setDescription(_t('Check to activate'));
		$this->addElement($item);
        
		$item = new Zend_Form_Element_Select('log_mail_min_level');
		$item->setLabel(_t('Minimum level managed'));
		$item->setRequired(true);
		$item->addMultiOptions($options);
		$item->setValue(CMS_Log::WARN);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('log_mail_to');
    	$item->setLabel(_t('Mail to'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('log_db');
    	$item->setLabel(_t('Save in database'));
    	$item->setDescription(_t('Check to activate'));
		$this->addElement($item);
        
		$item = new Zend_Form_Element_Select('log_db_min_level');
		$item->setLabel(_t('Minimum level managed'));
		$item->setRequired(true);
		$item->addMultiOptions($options);
		$item->setValue(CMS_Log::DEBUG);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('log_firebug');
    	$item->setLabel(_t('Send to firebug'));
    	$item->setDescription(_t('Check to activate'));
		$this->addElement($item);
        
		$item = new Zend_Form_Element_Select('log_firebug_min_level');
		$item->setLabel(_t('Minimum level managed'));
		$item->setRequired(true);
		$item->addMultiOptions($options);
		$item->setValue(CMS_Log::DEBUG);
		$this->addElement($item);
	}
}