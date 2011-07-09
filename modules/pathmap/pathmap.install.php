<?php
/**
 * @file pathmap module install file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
 
class pathmap_install {
	public function install() {
		$mod = new Modules();
		$mod->getModuleByName('pathmap');
		$perms = array(
			'create paths',
			'update paths',
			'delete paths',
			'access paths'
		);
		$perm_ids = FabriqModules::register_perms($mod->id, $perms);
		
		global $db;
		$sql = "CREATE TABLE `fabmod_pathmap_paths` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`path` VARCHAR(100) NOT NULL,
			`modpage` ENUM('module', 'page') NOT NULL DEFAULT 'module',
			`controller` VARCHAR(100) NOT NULL,
			`action` VARCHAR(100) NOT NULL,
			`extra` VARCHAR(100) NULL,
			`wildcard` INT(11) NULL,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=INNODB;";
		$db->query($sql);
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}
	
	public function uninstall() {
		// core modules cannot be uninstalled
	}
}
	