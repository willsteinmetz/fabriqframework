<?php

class Modules extends Model {
	function __construct($id = NULL) {
		parent::__construct(array('module', 'enabled'), 'fabmods_modules');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	function getModuleByName($module) {
		global $db;
		
		$sql = "SELECT * FROM fabmods_modules WHERE module=" . (($db->type == 'MySQL') ? '?' : '$1');
		$this->fill($db->prepare_select($sql, $this->fields(), $module));
	}
	
	function getAll() {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} WHERE enabled = " . (($db->type == 'MySQL') ? '?' : '$1') . " ORDER BY module";
		$this->fill($db->prepare_select($sql, $this->fields(), 1));
	}
}
	