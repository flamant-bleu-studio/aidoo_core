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

class Calendar_BackController extends CMS_Controller_Action
{
	public function indexAction()
	{		
		$this->view->calendar = Calendar_Lib_Render::backMonth(new DateTime());
	}
	
	public function addCalendarAction()
	{
		$this->_helper->layout()->setLayout('lightbox');
		
		$form = new Calendar_Form_Calendar();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$calendar = new Calendar_Object_Calendar();
				$calendar->name 		= $form->getValue('name');
				$calendar->status 		= $form->getValue('status');
				$calendar->downloadable = $form->getValue('downloadable');
				$calendar->color 		= $form->getValue('color');
				
				$calendar->save();
				
				$this->closeFancybox($this->_helper->route->full('calendar_back'));
			}
			else {
				$form->populate($_POST);
			}
		}
		
		$this->view->form = $form;
	}
	
	public function editCalendarAction()
	{
		$this->_helper->layout()->setLayout('lightbox');
		
		$id = (int)$this->_request->getParam('id');
		
		if (!$id)
			throw new Zend_Exception("Invalid id");
		
		$calendar = new Calendar_Object_Calendar($id, 'all');
		$form = new Calendar_Form_Calendar();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$calendar->name 		= $form->getValue('name');
				$calendar->status 		= $form->getValue('status');
				$calendar->downloadable = $form->getValue('downloadable');
				$calendar->color 		= $form->getValue('color');
				
				$calendar->save();
				
				$this->closeFancybox($this->_helper->route->full('calendar_back'));
			}
			else {
				$form->populate($_POST);
			}
		}
		else {
			$form->populate($calendar->toArray());
		}
		
		$this->view->form = $form;
	}
	
	public function deleteCalendarAction()
	{
		$id = $this->getRequest()->getParamInt('id');
	
		if (!$id)
			throw new Zend_Exception("Invalid id");
			
		// Récupère le calendrier concerné et le supprimme
		$calendar = new Calendar_Object_Calendar($id, 'all');
		$calendar->delete();
	
		// Récupère les évennements liés au calendrier supprimmé et les supprimme
		$events = Calendar_Object_Event::get(array('id_calendar' => $id), null, null, 'all');
		foreach($events as $event)
			$event->delete();
		
		_message(_t('Calendar deleted'));
		return $this->_redirect($this->_helper->route->short('index'));
		
	}
	
	public function addEventAction()
	{
		$this->_helper->layout()->setLayout('lightbox');
		
		$form = new Calendar_Form_Event();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$event = new Calendar_Object_Event();
				
				$event->id_calendar = $form->getValue('id_calendar');
				$event->status 		= $form->getValue('status');
				$event->name 		= $form->getValue('name');
				$event->description = $form->getValue('description');
				$event->date_start 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_start'));
				$event->date_end 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_end'));
				
				$event->save();
				
				$this->closeFancybox($this->_helper->route->full('calendar_back'));
			}
			else {
				$form->populate($_POST);
			}
		}
		
		$this->view->form = $form;
	}
	
	public function editEventAction()
	{
		$this->_helper->layout()->setLayout('lightbox');
		
		$id = (int)$this->_request->getParam('id');
		
		if (!$id)
			throw new Zend_Exception("Invalid id");
		
		$form = new Calendar_Form_Event();
		$event = new Calendar_Object_Event($id, 'all');
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$event->id_calendar = $form->getValue('id_calendar');
				$event->status 		= $form->getValue('status');
				$event->name 		= $form->getValue('name');
				$event->description = $form->getValue('description');
				$event->date_start 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_start'));
				$event->date_end 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_end'));
				
				$event->save();
				
				$this->closeFancybox($this->_helper->route->full('calendar_back'));
			}
			else {
				$form->populate($_POST);
			}
		}
		else {
			$form->populate($event->toArray());
		}
		
		$this->view->form = $form;
	}
	
	public function deleteEventAction()
	{
		$id = $this->getRequest()->getParamInt('id');
	
		if (!$id)
			throw new Zend_Exception("Invalid id");
			
		// Récupère l'évennement concerné et le supprimme
		$event = new Calendar_Object_Event($id, 'all');
		$event->delete();
	
		_message(_t('Event deleted'));
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function renderAction()
	{
		$date = new DateTime($_POST['current']);
		$action = $_POST['action'];
		
		if ($action == 'prev')
			$date->add(date_interval_create_from_date_string('-1 month'));
		else 
			$date->add(date_interval_create_from_date_string('+1 month'));
		
		echo Calendar_Lib_Render::backMonth($date);die;
	}
}