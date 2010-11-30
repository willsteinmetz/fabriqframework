<?php
/**
 * @file Module base file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
class FabriqModule extends Controller {
	public $name;
	public static $mname;
	
	function __construct() {
		spl_autoload_register(get_class($this) . '::autoload');
		$this->name = str_replace('_module', '', get_class($this));
		self::$mname = $this->name;
	}
	
	/**
	 * Module specific autoloading function for modules
	 * @param string $class
	 */
	public static function autoload($class) {
		$class = str_replace("_mm", '', $class);
		$module = str_replace('_module', '', self::$mname);
		$file = "modules/{$module}/models/{$class}.model.php";
		if (file_exists($file)) {
			require_once($file);
		}
	}
}
	