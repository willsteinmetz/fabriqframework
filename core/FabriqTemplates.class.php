<?php
/**
 * @file Templating functionality file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
abstract class FabriqTemplates {
	private static $tplvars = array();
	private static $template = null;
	
	/**
	 * Adds a template variable
	 * @param string $var
	 * @param mixed $val
	 */
	public static function set_var($var, $val) {
		self::$tplvars[$var] = $val;
	}
	
	/**
	 * Sets an array of variables with the given associative array
	 * @param array $vars
	 */
	public static function set_vars($vars) {
		if (count($vars) > 0) {
			foreach ($vars as $key => $val) {
				self::$tplvars[$key] = $val;
			}
		}
	}
	
	/**
	 * Get a template variable
	 * @param string $var
	 * @return mixed
	 */
	public static function get_var($var) {
		if (isset(self::$tplvars[$var])) {
			return self::$tplvars[$var];
		}
		return false;
	}
	
	/**
	 * Get the template variables
	 * @return array
	 */
	public static function get_vars() {
		return self::$tplvars;
	}
	
	/**
	 * Sets the template to render. Returns the template if nothing is passed in
	 * @param string $tpl
	 */
	public static function template($tpl = null) {
		if ($tpl == null) {
			return self::$template;
		} else {
			self::$template = $tpl;
		}
	}
	
	/**
	 * Render the specified template
	 */
	public static function render() {
		if (Fabriq::render() == 'none') {
			return false;
		}
		ob_start();
		extract(self::$tplvars);
		if (Fabriq::render() == 'layout') {
			$tpl = "app/views/layouts/" . self::$template . ".tpl.php";
			if (!file_exists($tpl)) {
				throw new Exception('Template ' . self::$template . ' could not be loaded');
			}
			require_once($tpl);
		} else if (Fabriq::render() == 'view') {
			$view = "app/views/" . PathMap::render_controller() . '/' . PathMap::render_action() . '.view.php';
			if (!file_exists($view)) {
				throw new Exception('View' . PathMap::render_controller() . '::' . PathMap::render_action() . ' could not be loaded');
			}
			require_once($view);
		}
		ob_flush();
		ob_clean();
	}
	
	
	public static function render_to_var($controller, $action, $var) {
		ob_start();
		extract(self::$tplvars);
		require_once("app/views/{$controller}/{$action}.view.php");
		$data = ob_get_clean();
		FabriqTemplates::set_var($var, $data);
	}
}
	