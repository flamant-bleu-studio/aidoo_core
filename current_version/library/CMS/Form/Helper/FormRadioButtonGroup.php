<?php

class CMS_Form_Helper_FormRadioButtonGroup extends Zend_View_Helper_FormHidden
{
	public function formRadioButtonGroup($name, $value = null, array $attribs = null)
	{
		$options = $attribs['options']['params'];
		$values  = $attribs['options']['values'];
		
		$html = '<div class="btn-group" data-toggle="buttons-radio">';
		
		if (!empty($values))
			foreach ($values as $v => $text)
				$html .= '<button type="button" class="btn'. (($v == $value) ? ' active' : '') .'" data-value="'. $v .'">'. $text .'</button>';
		
		$html .= '</div>';
		
		$html .= parent::formHidden($name, $value, $attribs);
		
		return $html;
	}
}