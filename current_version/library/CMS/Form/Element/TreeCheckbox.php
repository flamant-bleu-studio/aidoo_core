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

class CMS_Form_Element_TreeCheckbox extends Zend_Form_Element_MultiCheckbox
{
	public $helper = 'formTreeCheckbox';
	
	public function isValid($value, $context = null)
	{
		$this->setValue($value);
		$value = $this->getValue();
	
		if ((('' === $value) || (null === $value))
				&& !$this->isRequired()
				&& $this->getAllowEmpty()
		) {
			return true;
		}
	
		if ($this->isRequired()
				&& $this->autoInsertNotEmptyValidator()
				&& !$this->getValidator('NotEmpty'))
		{
			$validators = $this->getValidators();
			$notEmpty   = array('validator' => 'NotEmpty', 'breakChainOnFailure' => true);
			array_unshift($validators, $notEmpty);
			$this->setValidators($validators);
		}
	
		// Find the correct translator. Zend_Validate_Abstract::getDefaultTranslator()
		// will get either the static translator attached to Zend_Validate_Abstract
		// or the 'Zend_Translate' from Zend_Registry.
		if (Zend_Validate_Abstract::hasDefaultTranslator() &&
				!Zend_Form::hasDefaultTranslator())
		{
			$translator = Zend_Validate_Abstract::getDefaultTranslator();
			if ($this->hasTranslator()) {
				// only pick up this element's translator if it was attached directly.
				$translator = $this->getTranslator();
			}
		} else {
			$translator = $this->getTranslator();
		}
	
		$this->_messages = array();
		$this->_errors   = array();
		$result          = true;
		$isArray         = $this->isArray();
		foreach ($this->getValidators() as $key => $validator) {
			if (method_exists($validator, 'setTranslator')) {
				if (method_exists($validator, 'hasTranslator')) {
					if (!$validator->hasTranslator()) {
						$validator->setTranslator($translator);
					}
				} else {
					$validator->setTranslator($translator);
				}
			}
	
			if (method_exists($validator, 'setDisableTranslator')) {
				$validator->setDisableTranslator($this->translatorIsDisabled());
			}
	
			if ($isArray && is_array($value)) {
				$messages = array();
				$errors   = array();
				if (empty($value)) {
					if ($this->isRequired()
							|| (!$this->isRequired() && !$this->getAllowEmpty())
					) {
						$result = false;
					}
				} else {
					foreach ($value as $val) {
						if (!$validator->isValid($val, $context)) {
							$result = false;
							if ($this->_hasErrorMessages()) {
								$messages = $this->_getErrorMessages();
								$errors   = $messages;
							} else {
								$messages = array_merge($messages, $validator->getMessages());
								$errors   = array_merge($errors,   $validator->getErrors());
							}
						}
					}
				}
				if ($result) {
					continue;
				}
			} elseif ($validator->isValid($value, $context)) {
				continue;
			} else {
				$result = false;
				if ($this->_hasErrorMessages()) {
					$messages = $this->_getErrorMessages();
					$errors   = $messages;
				} else {
					$messages = $validator->getMessages();
					$errors   = array_keys($messages);
				}
			}
	
			$result          = false;
			$this->_messages = array_merge($this->_messages, $messages);
			$this->_errors   = array_merge($this->_errors,   $errors);
	
			if ($validator->zfBreakChainOnFailure) {
				break;
			}
		}
	
		// If element manually flagged as invalid, return false
		if ($this->_isErrorForced) {
			return false;
		}
	
		return $result;
	}
}
