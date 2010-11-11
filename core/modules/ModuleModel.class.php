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
	/**
	 * Module model base class constructor
	 * Module tables must be prefixed with fabmod_. If the module models are not
	 * defined with fabmod_ prefixed, it will be automatically added
	 * @param array $attributes
	 * @param string $db_table
	 * @param string $id_name
	 */
	function __construct($attributes, $db_table, $id_name = 'id') {
		if (substr($table, 0, 7) != 'fabmod_') {
			$table = 'fabmod_' . $table;
		}
		parent::__construct($attributes, $db_table, $id_name);
	}
}
	