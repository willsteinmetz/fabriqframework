<?php

class roles_Roles extends ModuleModel {
	public $roles = array();
	
	function __construct() {
		parent::__construct('roles', array('role', 'enabled'), 'roles');
	}
	
	public function getAll($getDisabled = false) {
		global $db;
		
		if ($getDisabled) {
			$sql = "SELECT * FROM {$this->db_table} ORDER BY role";
		} else {
			$sql = "SELECT * FROM {$this->db_table} WHERE enabled = 1 ORDER BY role";
		}
		
		$this->fill($db->prepare_select($sql, $this->fields()));
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
		
		$sql = "SELECT * FROM {$this->db_table} WHERE role=?";
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
		
		$sql = "SELECT * FROM {$this->db_table} WHERE role IN (" . $db->qmarks . ")";
		$this->fill($db->prepare_select($sql, $this->fields(), $roles));
	}
}	
	