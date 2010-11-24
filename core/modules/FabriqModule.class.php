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
	
	/**
	 * Add a module stylesheet to the CSS queue
	 * @param string $stylesheet
	 * @param string $media
	 * @param string $path
	 * @param string $ext;
	 */
	public function add_css($stylesheet, $media = 'screen', $path = '', $ext = '.css') {
		Fabriq::add_css($stylesheet, $media, "modules/{$this->name}/stylesheets/{$path}", $ext);
	}
	
	/**
	 * Add a module JavaScript to the JS queue
	 * @param string $javascript
	 * @param string $path
	 * @param string $ext
	 */
	public function add_js($javascript, $path = '', $ext = '.js') {
		Fabriq::add_js($javascript, "modules/{$this->name}/javascripts/{$path}", $ext);
	}
}
	