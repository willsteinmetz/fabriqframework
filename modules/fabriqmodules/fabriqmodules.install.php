<?php
/**
 * fabriqmodules install file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2013, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class fabriqmodules_install {
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
		$pathmap->register_path('fabriqmodules/install/!#', 'fabriqmodules', 'index', 'module', null, 2);
		$pathmap->register_path('fabriqmodules/uninstall/!#', 'fabriqmodules', 'index', 'module', null, 2);
		
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
	
	public function uninstall() {
		$mod = new Modules();
		$mod->getModuleByName('fabriqmodules');
		
		// remove perms
		FabriqModules::remove_perms($mod->id);
		
		// remove paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->remove_path('fabriqmodules');
		$pathmap->remove_path('fabriqmodules/manage');
		$pathmap->remove_path('fabriqmodules/configure/!#');
		$pathmap->remove_path('fabriqmodules/disable/!#');
		$pathmap->remove_path('fabriqmodules/enable/!#');
		$pathmap->remove_path('fabriqmodules/install/!#');
		$pathmap->remove_path('fabriqmodules/uninstall/!#');
		
		// set module as not installed
		$mod->installed = 0;
		$mod->update();
	}
}
	