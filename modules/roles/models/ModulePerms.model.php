<?php

class roles_ModulePerms extends ModuleModel {
	public $perms = array();
	
	function __construct() {
		parent::__construct('roles', array('permission', 'role'), 'moduleperms');
	}
	
	public function getAll($organize = true) {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} ORDER BY permission, role";
		$this->fill($db->prepare_select($sql, $this->fields()));
		
		// organize the permissions
		if ($organize) {
			$this->reindex();
		}
	}
	
	public function reindex() {
		$this->perms = array();
		for ($i = 0; $i < $this->count(); $i++) {
			if (!array_key_exists($this[$i]->permission, $this->perms)) {
				$this->perms[$this[$i]->permission] = array();
			}
			$this->perms[$this[$i]->permission][$this[$i]->role] = $i;
		}
	}
}
	