<?php

class CMS_Form_Element_RadioButtonGroup extends Zend_Form_Element_Hidden
{
	public $helper = 'formRadioButtonGroup';
	
	private $_options;
	
	public function __construct($field_name, $attributes = null)
	{
		Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
		
		$this->_options['values'] = array();
		$this->_options['params'] = $attributes['params'];
		
		parent::__construct($field_name, $attributes);
	}
	
	public function setOption($value, $text)
	{
		$this->_options['values'][$value] = $text;
	}
	
	public function setOptions(array $options)
	{
		$this->_options['values'] = array_merge($this->_options['values'], $options);
	}
	
	private function setParams()
	{
		$this->setAttrib('options', $this->_options);
	}
	
	public function render(Zend_View_Interface $view = null)
	{
		$this->setParams();
		
		$processLayout = CMS_Application_ProcessLayout::getInstance();
		
		$processLayout->appendJsScript("
			$('#form_". $this->_name ." .btn-group').on('click', '.btn', function(){
				$('#form_". $this->_name ." #". $this->_name ."').val($(this).data('value'));
			});
		");
		
		return parent::render($view);
	}
}