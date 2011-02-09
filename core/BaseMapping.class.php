<?php
/**
 * @files Base Mapping class - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class BaseMapping {
	private static $controller;
	private static $rendercontroller;
	private static $action;
	private static $renderaction;
	
	/**
	 * Controller getter/setter
	 * if NULL, return the $controller variable
	 * @param string $c
	 * @return string
	 */
	public static function controller($c = NULL) {
		if ($c != NULL) {
			self::$controller = $c;
		} else {
			return self::$controller;
		}
	}
	
	/**
	 * Render controller getter/setter
	 * if NULL, return the $rendercontroller variable
	 * @param string $controller
	 * @return string
	 */
	public static function render_controller($c = NULL) {
		if ($c != NULL) {
			self::$rendercontroller = $c;
		} else {
			return self::$rendercontroller;
		}
	}
	
	/**
	 * Action getter/setter
	 * if NULL, return the $action variable
	 * @param string $a
	 * @return string
	 */
	public static function action($a = NULL) {
		if ($a != NULL) {
			self::$action = $a;
		} else {
			return self::$action;
		}
	}
	
	/**
	 * Render action getter/setter
	 * if NULL, return the $renderaction variable
	 * @param string $action
	 * @return string
	 */
	public static function render_action($a = NULL) {
		if ($a != NULL) {
			self::$renderaction = $a;
		} else {
			return self::$renderaction;
		}
	}
	
	/**
	 * getter for the base path for the application
	 * @return string
	 */
	public static function base_path() {
		global $_FAPP;
		
		return $_FAPP['apppath'];
	}
	
	/**
	 * Getter for if clean URLs are enabled
	 * @return boolean
	 */
	public static function clean_urls() {
		global $_FAPP;
		
		return $_FAPP['cleanurls'];
	}
	
	/**
	 * Getter for string value if clean URLs are enabled
	 * @return boolean
	 */
	public static function clean_urls_str() {
		global $_FAPP;
		
		if ($_FAPP['cleanurls']) {
			return 'true';
		}
		return 'false';
	}
	
	/**
	 * Builds a path
	 * @return string
	 */
	public static function build_path() {
		$path = '';
		for ($i = 0; $i < func_num_args(); $i++) {
			$path .= func_get_arg($i);
			if ($i < (func_num_args() - 1)) {
				$path .= '/';
			}
		}
		if (self::clean_urls()) {
			return self::base_path() . $path;
		} else {
			return 'index.php?q=' . $path;
		}
	}
	
	/**
	 * Argument getter/setter
	 * @param integer $index
	 * @param object $val
	 * @return object
	 */
	public static function arg($index, $val = NULL) {
		global $q;
		
		if ($val == NULL) {
			if (count($q) > $index) {
				return $q[$index];
			} else {
				return FALSE;
			}
		} else {
			$q[$index] = $val;
		}
	}
	
	/**
	 * Determines the path and sets the $controller, $action,
	 * $render_controller, and $render_action variables. This function
	 * can be extended in the /app/PathMap.class.php file to add custom
	 * functionality.
	 */
	public static function map_path() {
		global $q;
		global $_FAPP;
		
		$mapped = false;
		
		if (FabriqModules::enabled('pathmap')) {
			if (isset($_SESSION['FABMOD_USERS_forcepwdreset']) && ($_SESSION['FABMOD_USERS_forcepwdreset'] == 1)) {
				if (!in_array('users', $q) && !in_array('changePassword', $q)) {
					header('Location:' . call_user_func_array('BaseMapping::build_path', array_merge(array('users', 'changePassword'), $q)));
				}
			}
		}
		
		if (count($q) > 0) {
			if ((trim($q[0]) != '') && (file_exists("app/controllers/{$q[0]}.controller.php"))) {
				self::controller($q[0]);
				$mapped = true;
			}
			if (count($q) > 1) {
				if (!is_numeric($q[1])) {
					self::action($q[1]);
				} else {
					self::action($_FAPP['adefault']);
				}
			} else {
				self::action($_FAPP['adefault']);
			}
		}
		
		// try to map path with pathmap module if enabled and necessary
		if (FabriqModules::enabled('pathmap') && !$mapped) {
			$pathmap = &FabriqModules::module('pathmap');
			$pathmap->redirect($_GET['q']);
		}
		
		// resolve controller and action if not already declared
		if (PathMap::controller() == '') {
			if (count($q) == 0) {
				PathMap::controller($_FAPP['cdefault']);
				PathMap::arg(0, $_FAPP['cdefault']);
				PathMap::action($_FAPP['adefault']);
				PathMap::arg(1, $_FAPP['adefault']);
			} else if (($q[0] != '') && (!file_exists("app/controllers/{$q[0]}.controller.php"))) {
				PathMap::controller('errors');
				PathMap::action('fourohfour');
			}
		}
		
		// resolve render controller and action
		PathMap::render_controller(PathMap::controller());
		PathMap::render_action(PathMap::action());
	}
}