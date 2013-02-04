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

class Menu_AjaxController extends Zend_Controller_Action {

	public function updateparentAction()
	{
		$response = array();
		
		try
		{
			$datas = array();
			
			if($_POST['src'])
				$datas['src'] = $_POST['src'];
				
			if($_POST['dst'])
				$datas['dst'] = $_POST['dst'];
				
			if ($datas['src'] == $datas['dst'])
			{
				$response['error'] = false;
				echo json_encode($response);
				return;
			}
				
			$model = new Menu_Model_DbTable_Menu();
			$return = $model->updateParent($datas['src'], $datas['dst']);
			
			if ($return == true)
			{
				$response['error'] = false;
				$response['message'] = "success";
			}
			else
			{
				$response['error'] = true;
				$response['message'] = "Impossible de deplacer cet élément";
			}
		}
		catch (Exception $e)
		{
			$response['error'] = true;
			$response['message'] = $e->getMessage();
		}				

		
		echo json_encode($response);
	}
	
	public function moveprevioussiblingAction()
	{
		$response = array();
		
		try
		{
			$datas = array();
			
			if($_POST['src'])
				$datas['src'] = $_POST['src'];
				
			if($_POST['dst'])
				$datas['dst'] = $_POST['dst'];
			
			if ($datas['src']==$datas['dst'])
			{
				$response['error'] = false;
				echo json_encode($response);
				return;
			}
			
			$model = new Menu_Model_DbTable_Menu();
			$return = $model->moveItemsToPreviousSibling($datas['src'],$datas['dst']);
			
			if ($return == true)
			{
				$response['error'] = false;
				$response['message'] = "success";
			}
			else
			{
				$response['error'] = true;
				$response['message'] = "Impossible de deplacer cet élément";
			}
		}
		catch (Exception $e)
		{
			$response['error'] = true;
			$response['message'] = $e->getMessage();
		}				
		
		echo json_encode($response);
	}
}