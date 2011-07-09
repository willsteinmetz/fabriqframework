<?php

class users_UserRoles extends ModuleModel {
	public $roles = array();
	
	function __construct() {
		parent::__construct('users', array('user', 'role'), 'roles');
	}
	
	public function getRoles($user) {
		global $db;
		
		$query = "SELECT ur.id, ur.user, ur.role, ur.created, ur.updated, r.role as roleName  FROM {$this->db_table} ur, fabmod_roles_roles r WHERE user = ? AND ur.role = r.id";
		$this->fill($db->prepare_select($query, array_merge($this->fields(), array('roleName')), $user));
		for ($i = 0; $i < $this->count(); $i++) {
			$this->roles[$this[$i]->role] = $id;
		}
	}
	
	public function getRole($user, $role) {
		global $db;
		
		$query = "SELECT * FROM {$this->db_table} WHERE user= ? AND role = ?;";
		$this->fill($db->prepare_select($query, $this->fields(), array($user, $role)));
	}
}
