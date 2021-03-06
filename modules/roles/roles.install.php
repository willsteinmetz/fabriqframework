<?php

class roles_install extends FabriqModuleInstall {
	public function info() {
		return array(
			"module" => "roles",
			"version" => $this->getLatestUpdate(),
			"author" => "Will Steinmetz",
			"description" => "Manage user roles. This module installs the roles authenticated, unathenticated, and administrator upon install.",
			"dependsOn"=> array(
				"pathmap",
				"users"
			)
		);
	}
	
	public function install() {
		$mod = new Modules();
		$mod->getModuleByName('roles');
		$perms = array(
			'create roles',
			'update roles',
			'delete roles',
			'manage roles'
		);
		$perm_ids = FabriqModules::register_perms($mod->id, $perms);
		
		global $db;
		$sql = "CREATE TABLE IF NOT EXISTS `fabmod_roles_roles` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`role` VARCHAR(100) NOT NULL,
			`enabled` TINYINT(1) NOT NULL DEFAULT 1,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=INNODB;";
		$db->query($sql);
		$sql = "CREATE TABLE IF NOT EXISTS `fabmod_roles_moduleperms` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`permission` INT(11) NOT NULL,
			`role` INT(11) NOT NULL,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`),
			CONSTRAINT `fk_moduleperms_permission` FOREIGN KEY (`permission`) REFERENCES fabmods_perms(id) ON DELETE CASCADE,
			CONSTRAINT `fk_moduleperms_role` FOREIGN KEY (`role`) REFERENCES fabmod_roles_roles(id) ON DELETE CASCADE
		) ENGINE=INNODB;";
		$db->query($sql);
		
		// create base roles
		$role = FabriqModules::new_model('roles', 'Roles');
		$role->role = "unauthenticated";
		$role->enabled = 1;
		$role->id = $role->create();
		$role = FabriqModules::new_model('roles', 'Roles');
		$role->role = "authenticated";
		$role->enabled = 1;
		$role->id = $role->create();
		$role = FabriqModules::new_model('roles', 'Roles');
		$role->role = "administrator";
		$role->enabled = 1;
		$role->id = $role->create();
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('fabriqadmin/roles/manage', 'roles', 'index', 'module');
		$pathmap->register_path('fabriqadmin/roles/create', 'roles', 'create', 'module');
		$pathmap->register_path('fabriqadmin/roles/perms', 'roles', 'perms', 'module');
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}
	
	public function uninstall() {
		// core modules cannot be uninstalled
	}
}
	