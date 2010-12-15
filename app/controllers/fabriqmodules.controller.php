<?php
/**
 * @file Module display file - DO NOT EDIT
 * @author Will Steinmetz
 * This controller is used whenever a page's content is rendered
 * completely by one or more modules
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class fabriqmodules_controller extends Controller {
	function index() {
		
	}
	
	public function manage() {
		global $_FAPP;
		// @TODO require role
		Fabriq::title('Admin | Manage modules');
		Fabriq::page_js_on();
		if (!$_FAPP['templating']) {
			global $modules;
		}
		$modules = new Modules();
		$modules->getAll();
		
		// get and install any new modules
		$available = fabriqmodules_helper::scan_modules();
		$toInstall = fabriqmodules_helper::to_install($modules, $available);
		foreach ($toInstall as $install) {
			FabriqModules::install($install);
		}
		
		// update modules collection
		$modules = new Modules();
		$modules->getAll();
		
		if ($_FAPP['templating']) {
			FabriqTemplates::set_var('modules', $modules);
		}
	}
	
	public function enable() {
		
	}
	
	public function disable() {
		
	}
	
	public function hasConfiguration() {
		Fabriq::render('view');
		global $_FAPP;
		if (!$_FAPP['templating']) {
			global $module;
			global $numConfigs;
		}
		
		global $db;
		$sql = "SELECT COUNT(*) AS num FROM fabmods_module_configs WHERE module = ?";
		$data = $db->prepare_select($sql, array('num'), PathMap::arg(2));
		$numConfigs = $data[0]['num'];
		
		if ($_FAPP['templating']) {
			FabriqTemplates::set_var('module', $module);
			FabriqTemplates::set_var('numConfigs', $numConfigs);
		}
	}
	
	public function configure() {
		Fabriq::render('view');
		global $_FAPP;
		if (!$_FAPP['templating']) {
			global $module;
		}
		$module = new Modules(PathMap::arg(2));
		$installer = $module->module . '_install';
		$config = new $installer();
		$config->configure();
		if ($_FAPP['templating']) {
			FabriqTemplates::set_var('module', $module);
		}
	}
}
		