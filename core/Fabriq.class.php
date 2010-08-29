<?php
/**
 * @files This file contains functions used throughout Fabriq applications - DO NOT EDIT
 * @author Will Steinmetz
 * --
 * Copyright (c)2010, Ralivue.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *		 * Redistributions of source code must retain the above copyright
 *			 notice, this list of conditions and the following disclaimer.
 *		 * Redistributions in binary form must reproduce the above copyright
 *			 notice, this list of conditions and the following disclaimer in the
 *			 documentation and/or other materials provided with the distribution.
 *		 * Neither the name of the Ralivue.com nor the
 *			 names of its contributors may be used to endorse or promote products
 *			 derived from this software without specific prior written permission.
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

function __autoload($model) {
	require_once("app/models/{$model}.model.php");
}

abstract class Fabriq {
	private static $cssqueue = array();
	private static $jsqueue = array();
	private static $title;
	private static $render = 'layout';
	private static $layout = 'application';
	
	/**
	 * Adds a stylesheet to the CSS queue for stylesheet includes
	 * @param string $stylesheet
	 * @param string $media
	 * @param string $path
	 */
	public static function add_css($stylesheet, $media = 'screen', $path = 'public/stylesheets/', $ext = '.css') {
		self::$cssqueue[] = array('css' => $stylesheet, 'media' => $media, 'path' => $path, 'ext' => $ext);
	}
	
	/**
	 * Public getter for $cssqueue
	 * @return array
	 */
	public static function cssqueue() {
		return self::$cssqueue;
	}
	
	/**
	 * Adds a JavaScript file to the JavaScript queue for JavaScript includes
	 * @param string $javascript
	 * @param string $path
	 * @param string $ext
	 */
	public static function add_js($javascript, $path = 'public/javascripts/', $ext = '.js') {
		self::$jsqueue[] = array('js' => $javascript, 'path' => $path, 'ext' => $ext);
	}
	
	/**
	 * Public getter for $jsqueue
	 * @return array
	 */
	public static function jsqueue() {
		return self::$jsqueue;
	}
	
	/**
	 * Creates a link to another page in the application
	 * @param string $linktext
	 * @param string $controller
	 * @param string $action
	 * @param array $queries
	 * @param boolean $blank
	 */
	public static function link_to($linktext, $controller, $action = NULL, $queries = false, $blank = false, $title = NULL) {
		global $_FAPP;
		
		echo "<a href=\"";
		if (!$_FAPP['cleanurls']) {
			echo "index.php?q=";
		} else {
			echo $_FAPP['apppath'];
		}
		echo "{$controller}";
		if ($action != NULL) {
			echo "/{$action}";
		}
		if ($queries != false) {
			foreach($queries as $key => $val) {
				echo "/{$val}";
			}
		}
		echo "\"";
		if ($blank) {
			echo " target=\"_blank\"";
		}
		echo " title=\"";
		if ($title) {
			echo strip_tags($title);
		} else {
			echo strip_tags($linktext);
		}
		
		echo "\">{$linktext}</a>";
	}
	
	/**
	 * Includes the specified model
	 * @param string $model
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function model($model) {
		Messaging::message('The function Fabriq::model() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		require_once("app/models/{$model}.model.php");
	}
	
	/**
	 * page title getter/setter
	 * if NULL, return the page title
	 * @param string $title
	 * @return string
	 */
	public static function title($title = NULL) {
		if ($title != NULL) {
			self::$title = strip_tags($title);
		} else {
			return self::$title;
		}
	}
	
	/**
	 * getter/setter for the $render variable
	 * if NULL, return the $render variable
	 * @param string $render
	 * @return string
	 */
	public static function render($r = NULL) {
		if ($r != NULL) {
			switch($r) {
				case 'none':
					self::$render = 'none';
					break;
				case 'layout':
					self::$render = 'layout';
					break;
				case 'view': default:
					self::$render = 'view';
					break;
			}
		} else {
			return self::$render;
		}
	}
	
	/**
	 * layout file getter/setter
	 * if NULL, return the $layout variable
	 * @param string $layout
	 * @return string
	 */
	public static function layout($l = NULL) {
		if ($l != NULL) {
			self::$layout = $l;
		} else {
			return self::$layout;
		}
	}
	
	/**
	 * Controller getter/setter
	 * if NULL, return the $controller variable
	 * @param string $c
	 * @return string
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function controller($c = NULL) {
		Messaging::message('The function Fabriq::controller() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::controller($c);
	}
	
	/**
	 * Render controller getter/setter
	 * if NULL, return the $rendercontroller variable
	 * @param string $controller
	 * @return string
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function render_controller($c = NULL) {
		Messaging::message('The function Fabriq::render_controller() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::render_controller($c);
	}
	
	/**
	 * Action getter/setter
	 * if NULL, return the $action variable
	 * @param string $a
	 * @return string
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function action($a = NULL) {
		Messaging::message('The function Fabriq::action() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::action($a);
	}
	
	/**
	 * Render action getter/setter
	 * if NULL, return the $renderaction variable
	 * @param string $action
	 * @return string
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function render_action($a = NULL) {
		Messaging::message('The function Fabriq::render_action() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::render_action($a);
	}
	
	/**
	 * Issue a server error
	 */
	public static function fabriq_error() {
		Fabriq::render('none');
		header('Location: ' . PathMap::build_path(500));
	}
	
	/**
	 * turn on page javascript include
	 */
	public static function page_js_on() {
		Fabriq::add_js(PathMap::render_controller() . '.script', 'app/scripts/');
	}
	
	/**
	 * Determines whether or not the configuration file has been
	 * created yet
	 */
	public static function installed() {
		if (!file_exists('config/config.inc.php')) {
			header("Location: install.php");
			exit();
		}
	}
	
	/**
	 * Argument getter/setter
	 * @param integer $index
	 * @param object $val
	 * @return object
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function arg($index, $val = NULL) {
		Messaging::message('The function Fabriq::arg() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::arg($index, $val);
	}
	
	/**
	 * getter for the base path for the application
	 * @return string
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function base_path() {
		Messaging::message('The function Fabriq::base_path() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::base_path();
	}
	
	/**
	 * Getter for if clean URLs are enabled
	 * @return boolean
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function clean_urls() {
		Messaging::message('The function Fabriq::clean_urls() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::clean_urls();
	}
	
	/**
	 * Getter for string value if clean URLs are enabled
	 * @return boolean
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function clean_urls_str() {
		Messaging::message('The function Fabriq::clean_urls_str() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		return PathMap::clean_urls_str();
	}
	
	/**
	 * Builds a path
	 * @return string
	 * DEPRECATED - will be removed in version 1.0
	 */
	public static function build_path() {
		Messaging::message('The function Fabriq::build_path() has been deprecated and will be removed in the next release of Fabriq', 'warning');
		$args = func_get_args();
		return call_user_func_array(array('PathMap', 'build_path'), $args);
	}
}