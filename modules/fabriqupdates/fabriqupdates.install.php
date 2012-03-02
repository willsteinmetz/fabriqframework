<?php
/**
 * @file fabriqupdates.install.php
 * @author Will Steinmetz
 * fabriqupdates install file
 */

class fabriqupdates_install {
	public function install() {
		$mod = new Modules();
		$mod->getModuleByName('fabriqupdates');
		$perms = array(
			'Manage updates'
		);
		
		$perm_ids = FabriqModules::register_perms($mod->id, $perms);
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('fabriqupdates', 'fabriqupdates', 'index', 'module');
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}
	
	public function uninstall() {
		// core updates cannot be installed
	}
}
	