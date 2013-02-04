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

class CMS_Form_Element_Captcha extends Zend_Form_Element_Captcha
{
	
	public function render($view = null)
	{
		
		
		$this->setDecorators(array('Standard'));
		
		$this->removeDecorator("viewhelper");
		
		parent::render();
		
		
		$name = $this->getName();
		
		$html = '
		
			<div id="form_'.$name.'" class="form_line">
			
				<div class="form_text">
					<div class="form_label">
						<label for="'.$name.'">'. $this->getLabel() . '*</label>
					</div>
				</div>
				
				<div class="form_elem">
					
					' . $this->getCaptcha()->render() . '<br /><br />
					<input id="'.$name.'" type="text" name="'.$name.'[input]" class="validate[]" />
					<input type="hidden" name="'.$name.'[id]" value="' . $this->getValue() . '" />
				</div>
				
				<div class="clear"></div>
				';
		
				$messages = $this->getMessages();
				
				if($messages) {
					$html .= '<div class="form_error">' . $this->getView()->formErrors($messages) . '</div>';
				}
				
		$html .=
				'
				
			</div>
			
			
			
		';
		
		return $html;
	}


}