<?php
/**
 * @file pathmap module install file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2013, Ralivue.com
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
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('403', 'pathmap', '_403', 'module');
		$pathmap->register_path('404', 'pathmap', '_404', 'module');
		$pathmap->register_path('500', 'pathmap', '_500', 'module');
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}
	
	public function uninstall() {
		// core modules cannot be uninstalled
	}
	
	public function update_2_1_1() {
		// update the module version number
		$mod = new Modules();
		$mod->getModuleByName('pathmap');
		$mod->versioninstalled = '2.1.1';
		$mod->update();
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('403', 'pathmap', '_403', 'module');
		$pathmap->register_path('404', 'pathmap', '_404', 'module');
		$pathmap->register_path('500', 'pathmap', '_500', 'module');
	}
	
	public function update_2_1_3() {
		// update the module version number
		$mod = new Modules();
		$mod->getModuleByName('pathmap');
		$mod->versioninstalled = '2.1.3';
		$mod->update();
	}
}
	