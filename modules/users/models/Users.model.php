<?php

class Users_mm extends ModuleModel {
	function __construct($id = NULL) {
		parent::__construct('users', array('display', 'email', 'encpwd', 'status', 'banned'), 'users');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	public function getByDisplayEmail($user) {
		global $db;
		
		$query = "SELECT * FROM {$this->db_table} WHERE display = ? OR email = ?";
		$this->fill($db->prepare_select($query, $this->fields(), array($user, $user)));
	}
	
	public function ban() {
		$this->banned = 1;
		$this->update();
	}
	
	public function enable() {
		$this->banned = 0;
		$this->update();
	}
	
	public function getList($start = 1) {
		global $db;
		
		$query = "SELECT * FROM {$this->db_table} ORDER BY display LIMIT ?, 10";
		$this->fill($db->prepare_select($query, $this->fields(), ($start - 1)));
	}
}
	