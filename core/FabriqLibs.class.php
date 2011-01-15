<?php
/**
 * @file Library inclusion functionality file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
abstract class FabriqLibs {
	private static $phpqueue = array();
	/**
	 * Add a JavaScript library file to the JavaScript queue
	 * @param string $file
	 * @param string $libdir
	 * @param string $ext
	 */
	public static function js_lib($file_name, $libdir = '', $ext = '.js') {
		Fabriq::add_js($file_name, 'libs/javascript/' . $libdir . '/', $ext);
	}
	
	/**
	 * Add a CSS library file to the CSS queue
	 * @param string $file
	 * @param string $libdir
	 * @param string $ext
	 * @param string $media
	 */
	public static function css_lib($file_name, $libdir = '', $ext = '.css', $media = 'screen') {
		Fabriq::add_css($file_name, $media, 'libs/css/' . $libdir . '/', $ext);
	}
	
	/**
	 * Returns the number of php libraries in the queue
	 * @return integer
	 */
	public static function php_lib_count() {
		return count(self::$phpqueue);
	}
	
	/**
	 * Returns the PHP library queue
	 * @return array
	 */
	public static function phpqueue() {
		return self::$phpqueue;
	}
	
	/**
	 * Include external PHP libraries to be used with Fabriq
	 * @param unknown_type $file
	 * @param unknown_type $libdir
	 */
	public static function php_lib($file_name, $libdir = '') {
		self::$phpqueue[] = 'libs/php/' . $libdir . '/' . $file_name;
	}
}
