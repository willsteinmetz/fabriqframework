<?php
/**
 * @file Module model file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
class ModuleModel extends Model {
	public $module;
	
	/**
	 * Module model base class constructor
	 * Module tables must be prefixed with fabmod_[modulename]_.
	 * When defining the model, do not provide fabmod_[modulename]_
	 * @param array $attributes
	 * @param string $db_table
	 * @param string $id_name
	 */
	function __construct($module, $attributes, $db_table, $id_name = 'id') {
		$this->module = $module;
		$db_table = "fabmod_{$module}_{$db_table}";
		parent::__construct($attributes, $db_table, $id_name);
	}
}
	