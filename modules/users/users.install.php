<?php

class users_install {
	public function install() {
		$module = FabriqModules::register_module('users');
		$perms = array(
			'create users',
			'administer users',
			'ban users',
			'enable users'
		);
		
		$perm_ids = FabriqModules::register_perms($module, $perms);
		
		global $db;
		
		switch ($db->type) {
			case 'MySQL':
				$sql = "CREATE TABLE IF NOT EXISTS `fabmod_users_users` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`display` VARCHAR(24) NOT NULL,
					`email` VARCHAR(100) NOT NULL,
					`encpwd` VARCHAR(100) NOT NULL,
					`status` INT(4) NOT NULL DEFAULT 0,
					`banned` TINYINT(1) NOT NULL DEFAULT 0,
					`created` DATETIME NOT NULL,
					`updated` DATETIME NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=INNODB;";
				$db->query($sql);
			break;
			case 'pgSQL':
				
			break;
		}
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('users/login', 'users', 'login', 'module');
		$pathmap->register_path('users/logout', 'users', 'logout', 'module');
		$pathmap->register_path('users/forgotpassword', 'users', 'forgotpassword', 'module');
		$pathmap->register_path('users/create', 'users', 'create', 'module');
		$pathmap->register_path('users/update', 'users', 'update', 'module');
		$pathmap->register_path('users/enable', 'users', 'enable', 'module');
		$pathmap->register_path('users/ban', 'users', 'ban', 'module');
		$pathmap->register_path('users/register', 'users', 'register', 'module');
	}
	
	public function configure() {
		if (isset($_POST['submit'])) {
			global $_FAPP;
		}
	}
	
	public function uninstall() {
		// core modules cannot be uninstalled
	}
}
	