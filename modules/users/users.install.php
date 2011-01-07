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
		$sql = "CREATE TABLE IF NOT EXISTS `fabmod_users_roles` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`user` INT(11) NOT NULL,
			`role` INT(11) NOT NULL,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`),
			CONSTRAINT `fk_users_user` FOREIGN KEY (`user`) REFERENCES `fabmod_users_users`(`id`) ON DELETE CASCADE,
			CONSTRAINT `fk_users_role` FOREIGN KEY (`role`) REFERENCES `fabmod_roles_roles`(`id`) ON DELETE CASCADE
		) ENGINE=INNODB;";
		$db->query($sql);
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('users/index', 'users', 'index', 'module');
		$pathmap->register_path('users/index/%', 'users', 'index', 'module', null, 2);
		$pathmap->register_path('users/login', 'users', 'login', 'module');
		$pathmap->register_path('users/login/%', 'users', 'login', 'module', null, 2);
		$pathmap->register_path('users/logout', 'users', 'logout', 'module');
		$pathmap->register_path('users/forgotpassword', 'users', 'forgotpassword', 'module');
		$pathmap->register_path('users/create', 'users', 'create', 'module');
		$pathmap->register_path('users/update/%', 'users', 'update', 'module', null, 2);
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
	