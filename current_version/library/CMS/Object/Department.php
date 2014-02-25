<?php

class CMS_Object_Department extends CMS_Object_MonoLangEntity
{
	public $id_department;
	public $code;
	public $name;
	
	protected static $_model;
	protected static $_modelClass = "CMS_Model_DbTable_Department";
}