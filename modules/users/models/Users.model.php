<?php

class Users_mm extends ModuleModel {
	function __construct($id = NULL) {
		parent::__construct('users', array('display', 'email', 'encpwd', 'status', 'banned'), 'users');
		if ($id != NULL) {
			$this->find($id);
		}
	}
}
	