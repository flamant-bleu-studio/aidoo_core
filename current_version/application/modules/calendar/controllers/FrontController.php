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

class Calendar_FrontController extends CMS_Controller_Action
{
	public function renderAction()
	{
		$date = new DateTime($_POST['current']);
		$action = $_POST['action'];
		
		$id = (int)$this->_request->getParam('id');
		
		$bloc = new Bloc_Calendar_Main($id);
		
		if ($action == 'prev')
			$date->add(date_interval_create_from_date_string('-1 month'));
		else 
			$date->add(date_interval_create_from_date_string('+1 month'));
		
		echo Calendar_Lib_Render::frontBlocMonth($date, $bloc->id_calendar);die;
	}
}