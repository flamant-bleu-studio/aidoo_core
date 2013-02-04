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

class Calendar_Lib_Render
{
	private static function month(DateTime $date, $activeEventsOnly = false, $where_calendars = null)
	{
		$view = Zend_Layout::getMvcInstance()->getView();
		
		// Récupération des différents calendriers
		$temp_calendars = Calendar_Object_Calendar::get();
		$calendars = array();
		
		if(!empty($temp_calendars))
			foreach ($temp_calendars as $calendar)
				$calendars[$calendar->id_calendar] = $calendar;
		
		$view->calendars = $calendars;
		
		// Config
		$view->start_day = date('w', mktime(0, 0, 0, $date->format('m'), 1, $date->format('Y')));
		$view->days_in_month = date('t', $date->format('Y-m-d'));
		
		$date->modify('last day of');
		$last_month = $date->format('Y-m-d 23:59:59');
		
		$date->modify('first day of');
		$first_month = $date->format('Y-m-d 00:00:00');
		
		$view->datetime = $date;
		$view->current_month = ucfirst(CMS_Date::getMonth($date, 'fr')) . ' ' . $date->format('Y');
		$view->current_date = new DateTime();
		
		// Récupération des évenements du mois
		
		$where = array(
			'date' => array(
				'(? <= A.date_start AND A.date_start >= ?) OR (A.date_start <= ? AND A.date_end >= ?) OR (? <= A.date_end AND A.date_end >= ?)', // (before and current) || (current) || (current and after)
				$first_month,
				$first_month,
				$first_month,
				$first_month,
				$first_month,
				$first_month
			)
		);
		
		if ($where_calendars != null) {
			$where['calendar'] = array('A.id_calendar = '. implode(' OR A.id_calendar = ', $where_calendars));
		}
		
		if ($activeEventsOnly)  {
			$where['status'] = 1;
		}
		
		$temp_events = Calendar_Object_Event::get($where);
		
		$events = array();
		
		if ($temp_events) {
			foreach ($temp_events as $e) {
				$events[$e->id_event] = $e;
			}
		}
		
		$map_events = array();
		
		if ($events) {
			foreach ($events as $e) {
				$start = new DateTime($e->date_start);
				$end = new DateTime($e->date_end);
				
				while($start <= $end) {
					$map_events[$start->format('Y-m-d')][] = $e->id_event;
					$start->add(date_interval_create_from_date_string('+1 day'));
				}
			}
		}
		
		$view->events = $events;
		$view->map_events = $map_events;
	}
	
	public static function backMonth(DateTime $date)
	{
		self::month($date);
		
		$view = Zend_Layout::getMvcInstance()->getView();
		$view->setScriptPath(APPLICATION_PATH.'/modules/calendar/views/');
		return $view->render('render/back_month.tpl');
	}
	
	public static function frontBlocMonth(DateTime $date, $calendars = null)
	{
		self::month($date, true, $calendars);
		
		$view = Zend_Layout::getMvcInstance()->getView();
		$view->setScriptPath(APPLICATION_PATH.'/modules/calendar/views/');
		return $view->render('render/front_bloc_month.tpl');
	}
}