<?php

class ModuleConfigs extends Model {
	function __construct($id = NULL) {
		parent::__construct(array('module', 'var', 'val'), 'fabmods_module_configs');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	function getForModule($module) {
		global $db;
		
		if (is_numeric($module)) {
			$sql = "SELECT * FROM {$this->db_table} WHERE module = ? ORDER BY var";
		} else {
			$sql = "SELECT * FROM {$this->db_table} WHERE module = (SELECT id FROM fabmods_modules WHERE module = ?) ORDER BY var";
		}
		$this->fill($db->prepare_select($sql, $this->fields(), $module));
	}
}	
