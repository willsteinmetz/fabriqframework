<?php
/**
 * fabriqmodules install file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2013, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class fabriqmodules_install extends FabriqModuleInstall {
	public function info() {
		return array(
			"module" => "fabriqmodules",
			"version" => $this->getLatestUpdate(),
			"author" => "Will Steinmetz",
			"description" => "This module manages installing and updating Fabriq modules."
		);
	}
	
	public function install() {
		$mod = new Modules();
		$mod->getModuleByName('fabriqmodules');
		$perms = array(
			'manage modules'
		);
		
		$perm_ids = FabriqModules::register_perms($mod->id, $perms);
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('fabriqmodules', 'fabriqmodules', 'manage', 'module');
		$pathmap->register_path('fabriqmodules/manage', 'fabriqmodules', 'manage', 'module');
		$pathmap->register_path('fabriqmodules/configure/!#', 'fabriqmodules', 'configure', 'module', null, 2);
		$pathmap->register_path('fabriqmodules/disable/!#', 'fabriqmodules', 'disable', 'module', null, 2);
		$pathmap->register_path('fabriqmodules/enable/!#', 'fabriqmodules', 'enable', 'module', null, 2);
		$pathmap->register_path('fabriqmodules/install/!#', 'fabriqmodules', 'install', 'module', null, 2);
		$pathmap->register_path('fabriqmodules/uninstall/!#', 'fabriqmodules', 'uninstall', 'module', null, 2);
		
		// give administrators the ability to manage modules
		$adminPerm = FabriqModules::new_model('roles', 'ModulePerms');
		$adminPerm->permission = $perm_ids[0];
		$adminRole = FabriqModules::new_model('roles', 'Roles');
		$adminRole->getRole('administrator');
		$adminPerm->role = $adminRole->id;
		$adminPerm->id = $adminPerm->create();
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}

	public function update_2_1_7() {
		// update the module version number
		$mod = new Modules();
		$mod->getModuleByName('fabriqmodules');
		$mod->versioninstalled = '2.1.7';
		$mod->update();
	}
	
	public function uninstall() {
		// core modules cannot be uninstalled
	}
}
	