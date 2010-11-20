<?php

class Perms extends Model {
	function __construct($id = NULL) {
		parent::__construct(array('permission', 'module'), 'fabmods_perms');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	public function get_module_perms($module_id) {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} WHERE module=" . (($db->type == 'MySQL') ? '?' : '$1');
		$this->fill($db->prepare_select($sql, $this->fields, $module_id));
	}
}
	