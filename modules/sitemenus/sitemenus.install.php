<?php

class sitemenus_install {
	public function install() {
		$mod = new Modules();
		$mod->getModuleByName('sitemenus');
		$perms = array(
			'create menus',
			'update menus',
			'delete menus',
			'administer menus'
		);
		
		$perm_ids = FabriqModules::register_perms($mod->id, $perms);
		
		global $db;
		$sql = "CREATE TABLE fabmod_sitemenus_menus (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`menuName` VARCHAR(50) NOT NULL,
			`description` TEXT NULL,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=INNODB;";
		$db->query($sql);
		$sql = "CREATE TABLE fabmod_sitemenus_menuitems (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`itemName` VARCHAR(100) NOT NULL,
			`path` VARCHAR(255) NULL,
			`menu` INT(11) NOT NULL,
			`parentItem` INT(11) NULL,
			`weight` INT(11) NOT NULL DEFAULT 0,
			`newWindow` TINYINT(1) NOT NULL DEFAULT 0,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=INNODB;";
		$db->query($sql);
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('sitemenus', 'sitemenus', 'index', 'module');
		$pathmap->register_path('sitemenus/index', 'sitemenus', 'index', 'module');
		$pathmap->register_path('sitemenus/create', 'sitemenus', 'create', 'module');
		$pathmap->register_path('sitemenus/update/!#', 'sitemenus', 'update', 'module', null, 2);
		$pathmap->register_path('sitemenus/destroy/!#', 'sitemenus', 'destroy', 'module', null, 2);
		$pathmap->register_path('sitemenus/items/index/!#', 'sitemenus', 'itemsIndex', 'module', null, 3);
		$pathmap->register_path('sitemenus/items/create/!#', 'sitemenus', 'itemsCreate', 'module', null, 3);
		$pathmap->register_path('sitemenus/items/update/!#', 'sitemenus', 'itemsUpdate', 'module', null, 3);
		$pathmap->register_path('sitemenus/items/destroy/!#', 'sitemenus', 'itemsDestroy', 'module', null, 3);
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}
	
	public function uninstall() {
		$mod = new Modules();
		$mod->getModuleByName('sitemenus');
		
		// remove perms
		FabriqModules::remove_perms($mod->id);
		
		// remove paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->remove_path('sitemenus');
		$pathmap->remove_path('sitemenus/index');
		$pathmap->remove_path('sitemenus/create');
		$pathmap->remove_path('sitemenus/update/!#');
		$pathmap->remove_path('sitemenus/destroy/!#');
		$pathmap->remove_path('sitemenus/items/index/!#');
		$pathmap->remove_path('sitemenus/items/create/!#');
		$pathmap->remove_path('sitemenus/items/update/!#');
		$pathmap->remove_path('sitemenus/items/destroy/!#');
		
		// delete database table
		global $db;
		$sql = "DROP TABLE `fabmod_sitemenus_menus`;";
		$db->query($sql);
		$sql = "DROP TABLE `fabmod_sitemenus_menuitems`;";
		$db->query($sql);
		
		// set module as not installed
		$mod->installed = 0;
		$mod->update();
	}
	
	public function update_2_1_8() {
		// update the module version number
		$mod = new Modules();
		$mod->getModuleByName('users');
		$mod->versioninstalled = '2.1.8';
		$mod->update();
	}
}
	