<?php
/**
 * @file fabriqmodules module file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2013, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
 
class fabriqmodules_module extends FabriqModule {
	public function manage() {
		if (FabriqModules::module('roles')->requiresPermission('manage modules', $this->name)) {
			Fabriq::title('Admin | Manage modules');
			//Fabriq::page_js_on();
			FabriqModules::add_js($this->name, $this->name);
			Fabriq::fabriq_ui_on();
			
			$modules = new Modules();
			$modules->getAll();
			
			// get and install any new modules
			$available = $this->scan_modules();
			$toRegister = $this->to_register($modules, $available);
			foreach ($toRegister as $register) {
				FabriqModules::register_module($register);
			}
			
			// update modules collection
			$modules = new Modules();
			$modules->getAll();
			
			/*if ($_FAPP['templating']) {
				FabriqTemplates::set_var('modules', $modules);
			}*/
			FabriqModules::set_var($this->name, 'modules', $module);
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
	
	public function install() {
		Fabriq::render('none');
		header('Content-type:application/json');
		
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			$module = new Modules(PathMap::arg(2));
			if ($module->module != '') {
				$module->installed = 1;
				$module->update();
				FabriqModules::install($module->module);
				echo json_encode(array('success' => true, 'hasConfiguration' => $module->hasconfigs));
			} else {
				echo json_encode(array('success' => false));
			}
		} else {
			echo json_encode(array('success' => false, 'notLoggedIn' => true));
		}
	}

	public function uninstall() {
		Fabriq::render('none');
		header('Content-type:application/json');
		
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			$module = new Modules(PathMap::arg(2));
			if ($module->module != '') {
				$module->installed = 0;
				$module->update();
				FabriqModules::uninstall($module->module);
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
		/*global $_FAPP;
		if (!$_FAPP['templating']) {
			global $module;
		}*/
		$module = new Modules(PathMap::arg(2));
		$install_file = "modules/{$module->module}/{$module->module}.install.php";
		if (!file_exists($install_file)) {
			throw new Exception("Module {$module->module} install file could not be found");
		}
		require_once($install_file);
		eval("\$installer = new {$module->module}_install();");
		$installer->configure();
		/*if ($_FAPP['templating']) {
			FabriqTemplates::set_var('module', $module);
		}*/
		FabriqModules::set_var($this->name, $module, $module);
	}
	
	private function scan_modules() {
		$modules = array();
		if ($handle = opendir('modules')) {
			while (false !== ($file = readdir($handle))) {
				if ((strpos($file, '.') === FALSE) && is_dir('modules/' . $file)) {
					$modules[] = $file;
				}
			}
			closedir($handle);
		} else {
			throw new Exception('Modules directory could not be found/read');
		}
		return $modules;
	}
	
	private function to_register($installed, $available) {
		$toInstall = array();
		
		foreach ($available as $mod) {
			if (!$installed->installed($mod)) {
				$toInstall[] = $mod;
			}
		}
		
		return $toInstall;
	}
}
	