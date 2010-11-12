<?php
/**
 * @file Module managing functionality file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
abstract class FabriqModules {
	private static $modules = array();
	
	/**
	 * Calls the install function to install a module for use in the
	 * Fabriq app
	 * @param string $module
	 * @return mixed
	 */
	public static function install($module) {
		// find the installer file
		$install = "modules/{$module}/{$module}.install.php";
		if (!file_exists($install)) {
			throw new Exception("Module {$module} install file could not be found");
		}
		require_once($install);
		eval('$installer = new ' . $module . '_install();');
		return $installer->install();
	}
	
	/**
	 * Registers a module with the modules database table
	 * @param string $module
	 */
	public static function register_module($module) {
		$mod = new Model(array('module', 'enabled'), 'fabmods_modules');
		$mod->module = $module;
		$mod->enabled = 0;
		return $mod->create();
	}
	
	/**
	 * Registers permissions available for setting for this module
	 * @param int $module_id
	 * @param array $perms
	 * @return array
	 */
	public static function register_perms($module_id, $perms) {
		$mod = new Model(array('module', 'enabled'), 'fabmods_modules');
		$mod->find($module_id);
		if (($mod->id == null) || ($mod->id == '')) {
			throw new Exception('Module does not exist');
		}
		$perm_ids = array();
		foreach ($perms as $perm) {
			$perm = new Model(array('permission', 'module'), 'fabmods_perms');
			$perm->permission = $perm;
			$perm->module = $module_id;
			$perm_ids[] = $perm->create();
		}
		
		return $perm_ids;
	}
	
	/**
	 * Calls the uninstall function to uninstall a module from a Fabriq app
	 * @param string $module
	 * @return mixed
	 */
	public static function uninstall($module) {
		// find the installer file
		$uninstall = "modules/{$module}/{$module}.install.php";
		if (!file_exists($uninstall)) {
			throw new Exception("Module {$module} install file could not be found");
		}
		require_once($uninstall);
		eval('$installer = new ' . $module . '_install();');
		return $installer->uninstall();
	}
	
	/**
	 * Remove permissions for the given module
	 * @param int $module_id
	 */
	public static function remove_perms($module_id) {
		global $db;
		
		$sql = sprintf("DELETE FROM %s WHERE %s%s%s = %s", 'fabmods_perms', $db->delim, 'module', $db->delim, (($db->type == 'MySQL') ? '?' : '$1'));
		$db->prepare_cud($sql, array($module_id));
	}
	
	/**
	 * Loads a module's code
	 * @param $module
	 */
	public static function load($module) {
		// check to see if module is already loaded
		if (array_key_exists($module, self::$modules)) {
			return;
		}
		// try to load the module file
		$modfile = "modules/{$module}/{$module}.module.php";
		if (!file_exists($modfile)) {
			throw new Exception("Module {$module} could not be loaded");
		}
		require_once($modfile);
		eval('$mod = new ' . $module . '_module');
		self::$modules[$module] = $mod;
	}
	
	/**
	 * Returns a reference to the specified module for easier use
	 * @param string $module
	 * @return object
	 */
	public static function &module($module) {
		if (!array_key_exists($module, self::$modules)) {
			FabriqModules::load($module);
		}
		
		return self::$modules[$module];
	}
}
	