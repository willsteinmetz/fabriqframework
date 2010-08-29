<?php
/**
 * @files Base Mapping class - DO NOT EDIT
 * @author Will Steinmetz
 * --
 * Copyright (c)2010, Ralivue.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Ralivue.com nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Ralivue.com BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * --
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
		
		if (count($q) > 0) {
			if (trim($q[0]) != '') {
				self::controller($q[0]);
			} else {
				self::controller($_FAPP['cdefault']);
				self::arg(0, $_FAPP['cdefault']);
			}
			if (count($q) > 1) {
				if (!is_numeric($q[1])) {
					self::action($q[1]);
				} else {
					self::action($_FAPP['adefault']);
					self::arg(1, $_FAPP['adefault']);
				}
			} else {
				self::action($_FAPP['adefault']);
				self::arg(1, $_FAPP['adefault']);
			}
		} else {
			self::controller($_FAPP['cdefault']);
			self::arg(0, $_FAPP['cdefault']);
			self::action($_FAPP['adefault']);
			self::arg(1, $_FAPP['adefault']);
		}
		
		self::render_controller(self::controller());
		self::render_action(self::action());
	}
}