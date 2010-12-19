<?php

class Users_mm extends ModuleModel {
	function __construct($id = NULL) {
		parent::__construct('users', array('display', 'email', 'encpwd', 'status', 'banned'), 'users');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	function getByDisplayEmail($user) {
		global $db;
		
		$query = "SELECT * FROM {$this->db_table} WHERE display = ? OR email = ?";
		$this->fill($db->prepare_select($query, $this->fields(), array($user, $user)));
	}
}
	