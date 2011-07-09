<?php

class users_Users extends ModuleModel {
	function __construct() {
		parent::__construct('users', array('display', 'email', 'encpwd', 'status', 'banned', 'forcepwdreset'), 'users');
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
	
	public static function displayExists($display, $user = null) {
		global $db;
		
		if ($user != null) {
			$query = "SELECT COUNT(*) AS num FROM fabmod_users_users WHERE display = ? AND id != ?";
			$data = $db->prepare_select($query, array('num'), array($display, $user));
		} else {
			$query = "SELECT COUNT(*) AS num FROM fabmod_users_users WHERE display = ?";
			$data = $db->prepare_select($query, array('num'), $display);
		}
		return ($data[0]['num'] > 0) ? true : false;
	}

	public static function emailExists($email, $user = null) {
		global $db;
		
		if ($user != null) {
			$query = "SELECT COUNT(*) AS num FROM fabmod_users_users WHERE email = ? AND id != ?";
			$data = $db->prepare_select($query, array('num'), array($email, $user));
		} else {
			$query = "SELECT COUNT(*) AS num FROM fabmod_users_users WHERE email = ?";
			$data = $db->prepare_select($query, array('num'), $email);
		}
		return ($data[0]['num'] > 0) ? true : false;
	}
}
	