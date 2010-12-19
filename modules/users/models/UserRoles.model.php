<?php

class UserRoles_mm extends ModuleModel {
	function __construct($id = NULL) {
		parent::__construct('users', array('user', 'role'), 'roles');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	function getRoles($user) {
		global $db;
		
		$query = "SELECT ur.id, ur.user, ur.role, ur.created, ur.updated, r.role as roleName  FROM {$this->db_table} ur, fabmod_roles_roles r WHERE user = ? AND ur.role = r.id";
		$this->fill($db->prepare_select($query, array_merge($this->fields(), 'roleName'), $user));
	}
}
