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
		$module = FabriqModules::register_module('pathmap');
		$perms = array(
			'create paths',
			'update paths',
			'delete paths',
			'access paths'
		);
		$perm_ids = FabriqModules::register_perms($module, $perms);
		
		global $db;
		
		switch ($db->type) {
			case 'MySQL':
				$sql = "CREATE TABLE `fabmod_pathmap_paths` (
					`id` INT(11) NOT NULL,
					`path` VARCHAR(100) NOT NULL,
					`modpage` ENUM('module', 'page') NOT NULL DEFAULT 'module',
					`controller` VARCHAR(100) NOT NULL,
					`action` VARCHAR(100) NOT NULL,
					`extra` VARCHAR(100) NULL,
					`wildcard` INT(11) NULL,
					`created` DATETIME NOT NULL,
					`updated` DATETIME NOT NULL
				);";
			break;
			case 'pgSQL':
				
			break;
		}
		$db->query($sql);
	}
	
	public function uninstall() {
		// core modules cannot be uninstalled
	}
}
	