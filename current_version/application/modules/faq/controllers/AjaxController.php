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

class Faq_AjaxController extends Zend_Controller_Action
{
	public function updateOrderAction()
	{
		try {
			if ($this->getRequest()->isPost()){	
				$list_order = $_POST['list_order'];
				if (!empty($list_order) && is_array($list_order)) {
					foreach ($list_order as $id => $order) {
						$faq = new Faq_Object_Question(intval($id), 'all');

						// Si l'order change
						if ($faq->question_order == intval($order)) 
							continue;
						
		 				$datas = array("question_order" => intval($order)); 				
		 				$faq->fromArray($datas);
		 				$faq->save();
						
					}
				}
			}
						
			$response['error'] = false;
		}
		catch (Exception $e)
		{
			$response['error'] = true;
			$response['message'] = $e->getMessage();
		}
		
		echo json_encode($response);
	}
}