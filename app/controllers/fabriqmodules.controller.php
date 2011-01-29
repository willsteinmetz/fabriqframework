<?php
/**
 * @file Module display file - DO NOT EDIT
 * @author Will Steinmetz
 * This controller is used whenever a page's content is rendered
 * completely by one or more modules
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class fabriqmodules_controller extends Controller {
	function index() {
		
	}
	
	public function manage() {
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			global $_FAPP;
			// @TODO require role
			Fabriq::title('Admin | Manage modules');
			Fabriq::page_js_on();
			Fabriq::fabriq_ui_on();
			if (!$_FAPP['templating']) {
				global $modules;
			}
			$modules = new Modules();
			$modules->getAll();
			
			// get and install any new modules
			$available = $this->scan_modules();
			$toInstall = $this->to_install($modules, $available);
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
	}
	
	public function enable() {
		Fabriq::render('none');
		header('Content-type:application/json');
		
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			$module = new Modules(PathMap::arg(2));
			if ($module->module != '') {
				$module->enabled = 1;
				$module->update();
				echo json_encode(array('success' => true));
			} else {
				echo json_encode(array('success' => false));
			}
		} else {
			echo json_encode(array('success' => false, 'notLoggedIn' => true));
		}
	}
	
	public function disable() {
		Fabriq::render('none');
		header('Content-type:application/json');
		
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			$module = new Modules(PathMap::arg(2));
			if ($module->module != '') {
				$module->enabled = 0;
				$module->update();
				echo json_encode(array('success' => true));
			} else {
				echo json_encode(array('success' => false));
			}
		} else {
			echo json_encode(array('success' => false, 'notLoggedIn' => true));
		}
	}
	
	public function configure() {
		Fabriq::render('view');
		global $_FAPP;
		if (!$_FAPP['templating']) {
			global $module;
		}
		$module = new Modules(PathMap::arg(2));
		$install_file = "modules/{$module->module}/{$module->module}.install.php";
		if (!file_exists($install_file)) {
			throw new Exception("Module {$module->module} install file could not be found");
		}
		require_once($install_file);
		eval("\$installer = new {$module->module}_install();");
		$installer->configure();
		if ($_FAPP['templating']) {
			FabriqTemplates::set_var('module', $module);
		}
	}

	private function scan_modules() {
		$modules = array();
		if ($handle = opendir('modules')) {
			while (false !== ($file = readdir($handle))) {
				if (strpos($file, '.') === FALSE) {
					$modules[] = $file;
				}
			}
			closedir($handle);
		} else {
			throw new Exception('Modules directory could not be found/read');
		}
		return $modules;
	}
	
	private function to_install($installed, $available) {
		$toInstall = array();
		
		foreach ($available as $mod) {
			if (!$installed->installed($mod)) {
				$toInstall[] = $mod;
			}
		}
		
		return $toInstall;
	}
}
		