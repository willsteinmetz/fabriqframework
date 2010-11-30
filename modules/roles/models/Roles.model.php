<?php

class Roles_mm extends ModuleModel {
	public $roles = array();
	
	function __construct($id = NULL) {
		parent::__construct('roles', array('role', 'enabled'), 'roles');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	public function getRoles() {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} ORDER BY role";
		$this->fill($db->prepare_select($sql, $this->fields()));
		
		for ($i = 0; $i < $this->count(); $i++) {
			$this->roles[$this[$i]->id] = $i;
		}
	}
	
	public function getRole($role) {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} WHERE role=" . (($db->type == 'MySQL') ? '?' : '$1');
		$this->fill($db->prepare_select($sql, $this->fields(), strtolower($role)));
	}
	
	public function getRolesByName($roles) {
		global $db;
		if (!is_array($roles)) {
			$roles = explode(',', $roles);
		}
		foreach ($roles as &$role) {
			$role = strtolower(trim($role));
		}
		
		$sql = "SELECT * FROM {$this->db_table} WHERE role IN (" . (($db->type == 'MySQL') ? $db->qmarks : $db->placeholders) . ")";
		$this->fill($db->prepare_select($sql, $this->fields(), $roles));
	}
}	
	