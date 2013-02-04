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

class Users_Form_ViewAccessForm extends Zend_Form
{
	public function init()
	{
		$id = new Zend_Form_Element_Hidden('id');
		$id->setDecorators(array('ViewHelper'));
		$this->addElement($id);

		$item = new Zend_Form_Element_Text('name');
		$item->setLabel('Name : ');
		$item->setRequired(TRUE);
		$item->setAttrib('size',20);
		$this->addElement($item);

		$groupModel = new Users_Model_DbTable_Group();
		$groups = $groupModel->getAllGroups();
		
		foreach($groups as $group)
		{
			$n = '';
			for($i=0; $i < $group->level; $i++)
				$n .= '- ';
				
			$item = new Zend_Form_Element_Checkbox($group->id);
			$item->setLabel($n.$group->name);
			$item->setDecorators(array(
				'ViewHelper',
				array('Label', 'options' => array(
					'placement' => 'append')
				),
				array('decorator' => array('data' => 'HtmlTag'), 
					'options' => array('tag' => 'div')
				)
			)); 
			$this->addElement($item);
		}
	}
	
	public function presetCheckbox($groups){
		foreach ($groups as $group){
			if($this->getElement($group))
				$this->getElement($group)->setValue('1');
		}
	}
	
	public function setAction($action)
    {
		$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseAction = rtrim($baseurl ,'/') . $action;
        parent::setAction($baseAction);
    }
}