<?php
/**
 * @file Fabriq.core.php
 * @author Will Steinmetz
 * This file contains functions and classes used throughout Fabriq applications - DO NOT EDIT
 * 
 * Copyright (c)2012, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

/**
 * Default autoloading function for including class files
 * @param string $class
 */
function fabriq_default_autoload($class) {
	// include module install file
	if (strpos($class, '_install') !== FALSE) {
		$module = str_replace('_install', '', $class);
		require_once("modules/{$module}/{$module}.install.php");
	// initialize module core
	} else if (trim($class) == 'FabriqModules') {
		Fabriq::init_module_core();
	// autoload model
	} else {
		$model = "app/models/{$class}.model.php";
		if (file_exists($model)) {
			require_once($model);
		}
	}
}

/**
 * @class Fabriq
 * Core class for the Fabriq framework
 */
abstract class Fabriq {
	private static $cssqueue = array();
	private static $jsqueue = array();
	private static $title;
	private static $render = 'template';
	private static $layout = 'application';

	/**
	 * Adds a stylesheet to the CSS queue for stylesheet includes
	 * @param string $stylesheet
	 * @param string $media
	 * @param string $path
	 */
	public static function add_css($stylesheet, $media = 'screen', $path = 'public/stylesheets/', $ext = '.css') {
		if (substr($path, 0, 4) != 'http') {
			$path = PathMap::getUrl() . $path;
		}
		self::$cssqueue[] = array('css' => $stylesheet, 'media' => $media, 'path' => $path, 'ext' => $ext);
	}

	/**
	 * Public getter for $cssqueue
	 * @return array
	 */
	public static function cssqueue() {
		self::$cssqueue = array_merge(self::$cssqueue, FabriqModules::cssqueue());
		return self::$cssqueue;
	}

	/**
	 * Empties the CSS queue except for the fabriq base css file
	 */
	public static function empty_css_queue() {
		self::$cssqueue = array();
		self::add_css('fabriq.base', 'screen', 'core/');
	}
	
	/**
	 * This function will load the given version of jQuery. If a version is in the queue,
	 * the function will replace that version with the input version
	 * @param string $version - @default 'latest'
	 */
	public static function jquery($version = 'latest') {
		if (count(self::$jsqueue) > 0) {
			$found = false;
			foreach (self::$jsqueue as &$js) {
				if ($js['js'] == 'jquery.min') {
					$js['path'] = 'libs/javascript/' . $version;
					$found = true;
					break;
				}
			}
			if (!$found) {
				FabriqLibs::js_lib('jquery.min', 'jquery/' . $version);
			}
		} else {
			FabriqLibs::js_lib('jquery.min', 'jquery/' . $version);
		}
	}
	
	/**
	 * Empties the javascript queue except for the base JavaScript files and jQuery
	 */
	public static function empty_js_queue() {
		self::$jsqueue = array();
		FabriqLibs::js_lib('jquery.min', 'jquery/' . $version);
		Fabriq::add_js('fabriq', 'core/');
	}

	/**
	 * Adds a JavaScript file to the JavaScript queue for JavaScript includes
	 * @param string $javascript
	 * @param string $path
	 * @param string $ext
	 */
	public static function add_js($javascript, $path = 'public/javascripts/', $ext = '.js') {
		if (substr($path, 0, 4) != 'http') {
			$path = PathMap::getUrl() . $path;
		}
		self::$jsqueue[] = array('js' => $javascript, 'path' => $path, 'ext' => $ext);
	}

	/**
	 * Public getter for $jsqueue
	 * @return array
	 */
	public static function jsqueue() {
		self::$jsqueue = array_merge(self::$jsqueue, FabriqModules::jsqueue());
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
	 * Return the site's title
	 * @return string
	 */
	public static function siteTitle() {
		global $_FAPP;
		return $_FAPP['title'];
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
				case 'template':
				case 'layout':// @DEPRECATED, will be removed for 3.0
					self::$render = 'template';
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
	 * include fabriq ui functionality
	 */
	public static function fabriq_ui_on() {
		Fabriq::add_js('fabriq.ui', 'core/');
		Fabriq::add_css('fabriq.ui', 'screen', 'core/');
	}

	/**
	 * Determines whether or not the configuration file has been
	 * created yet
	 */
	public static function installed() {
		if (file_exists('config/config.inc.php')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns a config setting
	 * @param string $var
	 * @return mixed
	 */
	public static function config($var) {
		global $_FAPP;

		if (!array_key_exists($var, $_FAPP)) {
			return null;
		}
		return $_FAPP[$var];
	}
}

/**
 * @class BaseMapping
 * Core class that contains the base path mapping functionality
 */
class BaseMapping {
	private static $controller;
	private static $rendercontroller;
	private static $action;
	private static $renderaction;

	/**
	 * Return the site's URL
	 * @return string
	 */
	public static function getUrl() {
		global $_FAPP;
		
		return $_FAPP['url'] . $_FAPP['apppath'];
	}
	
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
			return PathMap::getUrl() . $path;
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
		global $installed;

		$mapped = false;

		if ($installed && FabriqModules::enabled('pathmap')) {
			if (isset($_SESSION[Fabriq::siteTitle()]['FABMOD_USERS_forcepwdreset']) && ($_SESSION[Fabriq::siteTitle()]['FABMOD_USERS_forcepwdreset'] == 1)) {
				if (!in_array('users', $q) && !in_array('changePassword', $q)) {
					header('Location:' . call_user_func_array('BaseMapping::build_path', array_merge(array('users', 'changePassword'), $q)));
				}
			}
		}

		if (count($q) > 0) {
			if ((trim($q[0]) != '') && (file_exists("app/controllers/{$q[0]}.controller.php"))) {
				$controller = $q[0];
				$mapped = true;
			}
			if (count($q) > 1) {
				if (!is_numeric($q[1])) {
					$action = $q[1];
				} else {
					$action = $_FAPP['adefault'];
				}
			} else {
				$action = $_FAPP['adefault'];
			}
			if ($mapped) {
				FabriqStack::enqueue($controller, $action);
			}
		}

		// try to map path with pathmap module if enabled and necessary
		if ($installed && FabriqModules::enabled('pathmap') && !$mapped) {
			$pathmap = &FabriqModules::module('pathmap');
			$pathmap->redirect($_GET['q']);
		}

		// not installed, map to the install function
		if (!$installed) {
			PathMap::arg(0, 'fabriqinstall');
			PathMap::arg(1, 'install');
			FabriqStack::enqueue('fabriqinstall', 'install');
		}

		// resolve controller and action if not already declared
		if (PathMap::controller() == '') {
			if (count($q) == 0) {
				PathMap::arg(0, $_FAPP['cdefault']);
				PathMap::arg(1, $_FAPP['adefault']);
				FabriqStack::enqueue($_FAPP['cdefault'], $_FAPP['adefault']);
			} else if (($q[0] != '') && (!file_exists("app/controllers/{$q[0]}.controller.php"))) {
				FabriqStack::error(404);
			}
		}
	}
}

/**
 * @class Messaging
 * Core messaging class
 */
class Messaging {
	private static $errors = array();
	private static $messages = array();
	private static $warnings = array();
	private static $successes = array();

	/**
	 * Returns the list of error messages
	 * @return array
	 */
	public static function errors() {
		return self::$errors;
	}

	/**
	 * Returns the list of general messages
	 * @return array
	 */
	public static function messages() {
		return self::$messages;
	}

	/**
	 * Returns the list of warning messages
	 * @return array
	 */
	public static function warnings() {
		return self::$warnings;
	}

	/**
	 * Returns the list of success messages
	 * @return array
	 */
	public static function successes() {
		return self::$successes;
	}

	/**
	 * Add a message to the given message stack
	 * @param string $message
	 * @param string $type
	 * @return bool
	 */
	public static function message($message, $type = 'error') {
		$message = trim($message);
		if ($message == '') {
			return FALSE;
		}
		switch ($type) {
			case 'message':
				self::$messages[] = $message;
			break;
			case 'warning':
				self::$warnings[] = $message;
			break;
			case 'success':
				self::$successes[] = $message;
			break;
			case 'error': default:
				self::$errors[] = $message;
			break;
		}
		return TRUE;
	}

	/**
	 * Display the messages in the given message stack
	 * @param string $type
	 * @return bool
	 */
	public static function display_messages($type = 'errors') {
		if (count(self::$$type) > 0) {
			$output = "<div class=\"message \n";
			switch ($type) {
				case 'messages':
					$output .= "messages\">\n";
				break;
				case 'warnings':
					$output .= "warnings\">\n";
				break;
				case 'successes':
					$output .= "successes\">\n";
				break;
				case 'errors': default:
					$output .= "errors\">\n";
					$output .= "\t<p>Before continuing, you must fix the following errors:</p>\n";
				break;
			}
			$output .= "\t<ul>\n";
			foreach (self::$$type as $msg) {
				$output .= "\t\t<li>{$msg}</li>\n";
			}
			$output .= "\t</ul>\n";
			$output .= "</div>\n";

			echo $output;

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Returns the number messages in the given stack
	 * @param string $type
	 * @return int
	 */
	public static function has_messages($type = 'errors') {
		return count(self::$$type);
	}
}

/**
 * @class FabriqTemplates
 * The core class for the Fabriq templating functionality
 */
abstract class FabriqTemplates {
	private static $tplvars = array();
	private static $template = null;
	private static $body = "";

	/**
	 * Initialize templating
	 */
	public static function init() {
		global $_FAPP;
		
		if (!isset($_FAPP['templates']['default'])) {
			$_FAPP['templates']['default'] = 'application';
		}
		if (trim(FabriqTemplates::template() == '')) { 
			FabriqTemplates::template($_FAPP['templates']['default']);
		}
	}

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
		switch(Fabriq::render()) {
			case 'none':
				return false;
			break;
			case 'view':
				ob_start();
				FabriqTemplates::body();
				ob_flush();
				ob_clean();
			break;
			case 'template': default:
				ob_start();
				extract(self::$tplvars);
				$tmpl = "app/templates/" . self::$template . ".tmpl.php";
				if (!file_exists($tmpl)) {
					$tpl = "app/templates/" . self::$template . ".tpl.php";
					if (!file_exists($tpl)) {
						throw new Exception('Template ' . self::$template . ' could not be loaded');
					}
					require_once($tpl);
				} else {
					require_once($tmpl);
				}
				ob_flush();
				ob_clean();
			break;
		}
	}

	/**
	 * Render view to a variable
	 * @param string $controller
	 * @param string $action
	 * @param string $var
	 */
	public static function render_to_var($controller, $action, $var) {
		ob_start();
		extract(self::$tplvars);
		require_once("app/views/{$controller}/{$action}.view.php");
		$data = ob_get_clean();
		FabriqTemplates::set_var($var, $data);
	}
	
	/**
	 * Render the given action and put it in the body variable to print out later
	 * @param obj $action
	 */
	public static function renderToBody($action) {
		if (Fabriq::render() == 'none') {
			return;
		}
		
		ob_start();
		extract(self::$tplvars);
		switch($action->type) {
			case "module":
				
			break;
			case "controller":
			default:
				if (!file_exists("app/views/{$action->controller}/{$action->action}.view.php")) {
					FabriqStack::error(404);
				} else {
					require_once("app/views/{$action->controller}/{$action->action}.view.php");
				}
			break;
		}
		self::$body .= ob_get_contents();
		ob_end_clean();
	}
	
	/**
	 * Return what is cached in the body variable
	 * @return string
	 */
	public static function body() {
		return self::$body;
	}
}

/**
 * @class FabriqLibs
 * Core class for including code libraries
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
		Fabriq::add_js($file_name, PathMap::getUrl() . 'libs/javascript/' . $libdir . '/', $ext);
	}

	/**
	 * Add a CSS library file to the CSS queue
	 * @param string $file
	 * @param string $libdir
	 * @param string $ext
	 * @param string $media
	 */
	public static function css_lib($file_name, $libdir = '', $ext = '.css', $media = 'screen') {
		Fabriq::add_css($file_name, $media, PathMap::getUrl() . 'libs/css/' . $libdir . '/', $ext);
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

/**
 * @class FabriqStack
 * Core class to help manage requirements and determine the
 * processing stack
 */
abstract class FabriqStack {
	// public variables
	
	// private variables
	private static $queue = array();
	
	/**
	 * Requires the core code to run a Fabriq application on the client side
	 */
	public static function requireCore() {
		// include core JavaScript libraries
		Fabriq::jquery();
		Fabriq::add_js('fabriq', 'core/');
		Fabriq::add_css('fabriq.base', 'screen', 'core/');
	}
	
	/**
	 * Check whether or not the user is logged in
	 */
	public static function checkUserStatus() {
		if (((!isset($_SESSION[Fabriq::siteTitle()]['FABMOD_USERS_roles']) || ($_SESSION[Fabriq::siteTitle()]['FABMOD_USERS_roles'] == ''))) && Fabriq::installed()) {
			$role = FabriqModules::new_model('roles', 'Roles');
			$role->getRole('unauthenticated');
			$_SESSION[Fabriq::siteTitle()]['FABMOD_USERS_roles'] = serialize(array(
				$role->id,
				$role->role
			));
		}
	}
	
	/**
	 * Return whether or not the given controller exists
	 * @param string $controller
	 * @return bool
	 */
	public static function controllerExists($controller) {
		return file_exists("app/controllers/{$controller}.controller.php");
	}
	
	/**
	 * Redirect the user to the given error page
	 * @param $error
	 */
	public static function error($error) {
		header('Location: ' . PathMap::getUrl() . "{$error}");
		exit();
	}
	
	/**
	 * Add an action to execute to the queue
	 * @param string $controller
	 * @param string $action
	 * @param string $type - default "controller"
	 */
	public static function enqueue($controller, $action, $type = "controller") {
		$next = new stdClass();
		$next->controller = $controller;
		$next->action = $action;
		$next->type = $type;
		
		self::$queue[] = $next;
	}
	
	/**
	 * Remove the first item in the queue
	 */
	public static function dequeue() {
		if (count(self::$queue)) {
			return array_shift(self::$queue);
		}
		return false;
	}
	
	/**
	 * Process everything the queue
	 */
	public static function processQueue() {
		if (!count(self::$queue)) {
			return false;
		}
		
		$next = FabriqStack::dequeue();
		while ($next->controller == '') {
			if (!count(self::$queue)) {
				return false;
			}
			$next = FabriqStack::dequeue();
		}
		
		switch ($next->type) {
			case 'module':
				
			break;
			case 'controller':
			default:
				PathMap::controller($next->controller);
				PathMap::render_controller($next->controller);
				PathMap::action($next->action);
				PathMap::render_action($next->action);
				
				require_once("app/controllers/{$next->controller}.controller.php");
				$c = "{$next->controller}_controller";
				$controller = new $c();
				$a = str_replace('.', '_', $next->action);
				
				if (!$controller->hasMethod($a)) {
					FabriqStack::error(404);
				}
				
				call_user_func(array($controller, $a));
				FabriqTemplates::renderToBody($next);
			break;
		}
		
		if (count(self::$queue)) {
			FabriqStack::processQueue();
		}
	}
}

/**
 * @class Database
 * Core database class
 */
class Database {
	// public variables
	public $db;
	public $last;
	public $result;
	public $affected_rows;
	public $insert_id;
	public $num_rows;
	public $total_queries = 0;

	// private variables
	private $errorNo;
	private $errorStr;

	/**
	 * Constructor
	 */
	public function __construct($db_info) {
		$this->db = new mysqli($db_info['server'], $db_info['user'], $db_info['pwd'], $db_info['db']) or die ("A database error occurred. Please contact the administrator.");
	}

	/**
	 * Executes a given query
	 * @param string $sql
	 */
	public function query($sql) {
		$this->last = $sql;
		$this->result = $this->db->query($sql) or die (mysqli_error() . "<br />-----<br />$sql");
		$this->affected_rows = $this->db->affected_rows;
		$this->errorNo = $this->db->errno;
		$this->errorStr = $this->db->error;
		$this->total_queries++;
	}

	/**
	 * Prepares and executes a query for create, update, and
	 * delete operations
	 * @param string $sql
	 * @param array $inputs
	 * @return boolean success
	 */
	public function prepare_cud($sql, $inputs) {
		$stmt = $this->db->stmt_init();
		if ($stmt->prepare($sql)) {
			$types = '';
			if (!is_array($inputs)) {
				$inputs = array($inputs);
			}
			foreach ($inputs as $input) {
				if (is_int($input)) {
					$types .= 'i';
				} else if (is_float($input)) {
					$types .= 'd';
				} else {
					$types .= 's';
				}
			}
			$arg = array_merge(array($types), $inputs);
			// fix for call_user_func_array() taking both reference and value to
			// force it to be reference
			$args = array();
			foreach ($arg as $key => &$a) {
				$args[$key] = &$a;
			}
			call_user_func_array(array($stmt, "bind_param"), $args);

			$stmt->execute();
			$this->affected_rows = $stmt->affected_rows;
			$this->insert_id = $stmt->insert_id;
			$this->errorNo = $stmt->errno;
			$this->errorStr = $stmt->error;
			$stmt->close();

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Executes a prepared select sql query
	 * @param string $sql
	 * @param array $fields
	 * @param array $inputs
	 * @param array $attributes
	 * @return array/boolean
	 */
	public function prepare_select($sql, $fields, $inputs = array(), $attributes = NULL) {
		$stmt = $this->db->stmt_init();

		if ($stmt->prepare($sql)) {
			if (!is_array($inputs)) {
				$inputs = array($inputs);
			}
			if (count($inputs) > 0) {
				$types = '';
				foreach ($inputs as $input) {
					if (is_int($input)) {
						$types .= 'i';
					} else if (is_float($input)) {
						$types .= 'd';
					} else {
						$types .= 's';
					}
				}
				$arg2 = array_merge(array($types), $inputs);
				// fix for call_user_func_array() taking both reference and value to
				// force it to be reference
				$args = array();
				foreach ($arg2 as $key => &$a) {
					$args[$key] = &$a;
				}
				call_user_func_array(array($stmt, "bind_param"), $args);
			}

			$stmt->execute();
			$stmt->store_result();

			$cols = $stmt->field_count;

			$result = array();
			$arg = array();
			for ($i = 0; $i < $cols; $i++) {
				$result[$fields[$i]] = '';
				$arg[$i] = &$result[$fields[$i]];
			}

			call_user_func_array(array($stmt, "bind_result"), $arg);

			$results = array();
			if ($attributes == NULL) {
				while ($stmt->fetch()) {
					$r = array();
					foreach ($result as $key => $val) {
						$r[$key] = $val;
					}
					$results[] = $r;
				}
			} else {
				while ($stmt->fetch()) {
					$obj = new stdClass();
					foreach ($result as $key => $val) {
						$obj->$key = $val;
					}
					$results[] = $obj;
				}
			}

			$this->num_rows = $stmt->num_rows;
			$this->errorNo = $stmt->errno;
			$this->errorStr = $stmt->error;

			return $results;
		}
		return FALSE;
	}

	/**
	 * Escapes string for database call
	 * @param string $str
	 */
	public function escape_string($str) {
		return $this->db->real_escape_string($str);
	}

	/**
	 * Closes the database connection
	 */
	public function close() {
		$this->db->close();
	}

	/**
	 * Returns the database error number
	 * @return integer/boolean
	 */
	public function errno() {
		if ($this->errorNo !== NULL) {
			return $this->errorNo;
		}
		return FALSE;
	}

	/**
	 * Returns the database error string
	 * @return string/boolean
	 */
	public function error() {
		if ($this->errorStr !== NULL) {
			return $this->errorStr;
		}
		return FALSE;
	}

	/**
	 * Returns string version of the database error with the error number and string
	 * @return string
	 */
	public function error_str() {
		return $this->errorNo . ': ' . $this->errorStr;
	}

	/**
	 * Builds a question mark string to be used for prepared statements
	 * @param int $num
	 * @return string
	 */
	public function qmarks($num) {
		$qmarks = '';
		for ($i = 0; $i < $num; $i++) {
			$qmarks .= '?';
			if ($i != ($num - 1)) {
				$qmarks .= ', ';
			}
		}

		return $qmarks;
	}
}

/**
 * @class FabriqModelItem
 * Core class for an item in a model collection
 */
class FabriqModelItem {
	// public variables
	public $attributes = array();
	public $db_table;
	public $id_name;
	public $id;
	public $created;
	public $updated;

	// private variables
	private $data = array();

	/**
	 * Constructor
	 * @param array $attributes
	 * @param string $db_table
	 */
	public function __construct($attributes, $db_table = null, $id_name = 'id') {
		$this->attributes = $attributes;
		if ($db_table != null) {
			$this->db_table = $db_table;
		}
		$this->id_name = $id_name;
	}
	
	/**
	 * Getter
	 * @param string/integer $key
	 * @return unknown_type
	 */
	public function __get($key) {
		if (isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			return false;
		}
	}

	/**
	 * Setter
	 * @param string/integer $key
	 * @param unknown_type $value
	 * @return boolean
	 */
	public function __set($key, $value) {
		if (in_array($key, $this->attributes)) {
			$this->data[$key] = $value;
			return true;
		} else {
			$this->$key = $value;
		}
		return false;
	}
	
	/**
	 * isset operator
	 * @param $name
	 */
	public function __isset($name) {
		return isset($this->data[$name]);
	}
	
	/**
	 * unset operator
	 * @param $name
	 */
	public function __unset($name) {
		unset($this->data[$name]);
	}
	
	/**
	 * Finds a given value or collection
	 * @param string/integer $query
	 * @return boolean
	 */
	public function find($query = 'first') {
		global $db;

		$inputs = array();

		if (is_numeric($query)) {
			$sql = "SELECT " . $this->fieldsStr() . " FROM {$this->db_table} WHERE `{$this->id_name}` = ? LIMIT 1";
			$inputs[] = $query;
		} else {
			switch($query) {
				case 'last':
					$sql = "SELECT " . $this->fieldsStr() . " FROM {$this->db_table} ORDER BY `{$this->id_name}` DESC LIMIT 1";
				break;
				case 'first': all:
					$sql = "SELECT " . $this->fieldsStr() . " FROM {$this->db_table} ORDER BY `{$this->id_name}` LIMIT 1";
				break;
			}
		}

		$data = $db->prepare_select($sql, $this->fields(), $inputs);
		foreach ($data[0] as $key => $val) {
			$this->$key = $val;
		}

		if ($db->num_rows == 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Destroys a given value in the database
	 * @return integer/boolean
	 */
	public function destroy() {
		global $db;

		$sql = "DELETE FROM {$this->db_table} WHERE `{$this->id_name}` = ?";
		$db->prepare_cud($sql, array($this->id));
		return $db->affected_rows;
	}

	/**
	 * Inserts object $this->data[$index] into the database
	 * @return integer
	 */
	public function create() {
		global $db;

		$attributes = "`{$this->id_name}`, ";
		foreach ($this->attributes as $attribute) {
			$attributes .= "`{$attribute}`, ";
		}
		$attributes .= "`created`, `updated`";
		$this->created = date('Y-m-d G:i:s');
		$this->updated = date('Y-m-d G:i:s');
		$valuesStr = '';
		for ($i = 1; $i <= (count($this->attributes)); $i++) {
			$valuesStr .= '?';
			if ($i < (count($this->attributes))) {
				$valuesStr .= ', ';
			}
		}
		$valuesStr .= ", NOW(), NOW()";
		if (get_magic_quotes_gpc()) {
			foreach ($this->attributes as $attribute) {
				if ($this->$attribute != null) {
					$this->$attribute = stripslashes($this->$attribute);
				}
			}
		}

		$idField = 0;

		$data = array();
		foreach ($this->attributes as $attribute) {
			if ($this->$attribute !== null) {
				$data[] = $this->$attribute;
			} else {
				$data[] = null;
			}
		}
		$sql = "INSERT INTO {$this->db_table} ({$attributes}) VALUES ({$idField}, {$valuesStr})";

		if ($db->prepare_cud($sql, $data)) {
			return $db->insert_id;
		}
		return FALSE;
	}

	/**
	 * Updates the value of $this->data[$index] in the database
	 * @return integer
	 */
	public function update() {
		global $db;

		$this->updated = date('Y-m-d G:i:s');
		$valuesStr = '';
		for ($i = 0; $i < count($this->attributes); $i++) {
			$valuesStr .= "`{$this->attributes[$i]}` = ?, ";
		}
		$valuesStr .= '`created` = ?, `updated` = NOW()';
		if (get_magic_quotes_gpc()) {
			foreach ($this->attributes as $attribute) {
				if ($this->$attribute != null) {
					$this->$attribute = stripslashes($this->$attribute);
				}
			}
		}
		$data = array();
		foreach ($this->attributes as $attribute) {
			if ($this->$attribute !== null) {
				$data[] = $this->$attribute;
			} else {
				$data[] = null;
			}
		}
		$values = array_merge($data, array($this->created, $this->id));
		$sql = "UPDATE {$this->db_table} SET {$valuesStr} WHERE `{$this->id_name}` = ?";

		$db->prepare_cud($sql, $values);
		return $db->affected_rows;
	}
	
	/**
	 * Returns the database fields for easier use of the Database::prepare_select()
	 * function
	 * @return array
	 */
	public function fields() {
		return array_merge(array('id'), $this->attributes, array('updated', 'created'));
	}

	/**
	 * Returns the string value of the database fields for easier use of the
	 * Database::prepare_select() function
	 * @return string;
	 */
	public function fieldsStr() {
		return '`' . implode("`, `", $this->fields()) . '`';
	}
}

/**
 * @class Model
 * Core Model class that all models extend
 */
class Model implements ArrayAccess, Iterator, Countable {
	// public variables
	public $attributes = array();
	public $db_table;
	public $id_name;

	// private variables
	private $position;
	private $data = array();

	/**
	 * Constructor
	 * @param array $attributes
	 * @param string $db_table
	 */
	public function __construct($attributes, $db_table = null, $id_name = 'id') {
		$this->attributes = $attributes;
		$this->position = 0;
		if ($db_table != null) {
			$this->db_table = $db_table;
		}
		$this->id_name = $id_name;
	}

	/**
	 * Getter
	 * @param string/integer $key
	 * @return unknown_type
	 */
	public function __get($key) {
		return $this->data[0]->$key;
	}

	/**
	 * Setter
	 * @param string/integer $key
	 * @param unknown_type $value
	 * @return boolean
	 */
	public function __set($key, $value) {
		if (count($this->data) == 0) {
			$temp = new FabriqModelItem($this->attributes, $this->db_table, $this->id_name);
			$this->data[] = $temp;
		}
		$this->data[0]->$key = $value;
		return true;
	}

	/**
	 * Implements ArrayAccess::offsetSet
	 * @param $offset
	 * @param $value
	 */
	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	/**
	 * Implements ArrayAccess::offsetExists
	 * @param $offset
	 * @return unknown_type
	 */
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	/**
	 * Implements ArrayAccess::offsetUnset
	 * @param $offset
	 * @return unknown_type
	 */
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	/**
	 * Implements ArrayAccess::offsetGet
	 * @param $offset
	 * @return unknown_type
	 */
	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}

	/**
	 * Implements Iterator::rewind
	 */
	public function rewind() {
		$this->position = 0;
	}

	/**
	 * Implements Iterator::current
	 * $return unknown_type
	 */
	public function current() {
		return $this->data[$this->position];
	}

	/**
	 * Implements Iterator::key
	 * @return unknown_type
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * Implements Iterator::next
	 */
	public function next() {
		++$this->position;
	}

	/**
	 * Implements Iterator::valid
	 * @return boolean
	 */
	public function valid() {
		return isset($this->data[$this->position]);
	}

	/**
	 * Implements Countable::count
	 * @return integer
	 */
	public function count() {
		return count($this->data);
	}

	/**
	 * Finds a given value or collection
	 * @param string/integer $query
	 * @return boolean
	 */
	public function find($query = 'all') {
		global $db;

		if (is_numeric($query) || ($query == 'first') || ($query == 'last')) {
			$this->data[] = new FabriqModelItem($this->attributes, $this->db_table, $this->id_name);
			$this->data[(count($this->data) - 1)]->find($query);
			if ($this->data[(count($this->data) - 1)]->id == '') {
				unset($this->data[(count($this->data) - 1)]);
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			$inputs = array();
			$sql = "SELECT " . $this->fieldsStr() . " FROM {$this->db_table} ORDER BY `{$this->id_name}`";
			$this->fill($db->prepare_select($sql, $this->fields(), $inputs));

			if ($db->num_rows == 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	/**
	 * Destroys a given value in the database
	 * @param integer $index
	 * @return integer/boolean
	 */
	public function destroy($index = 0) {
		if (is_numeric($index) && ($index < count($this->data))) {
			return $this->data[$index]->destroy();
		} else {
			return false;
		}
	}

	/**
	 * Inserts object $this->data[$index] into the database
	 * @param integer $index
	 * @return integer
	 */
	public function create($index = 0) {
		if (is_numeric($index) && ($index < count($this->data))) {
			return $this->data[$index]->create();
		}
		return FALSE;
	}

	/**
	 * Updates the value of $this->data[$index] in the database
	 * @param integer $index
	 * @return integer
	 */
	public function update($index = 0) {
		if (is_numeric($index) && ($index < count($this->data))) {
			return $this->data[$index]->update();
		}
		return FALSE;
	}

	/**
	 * Fills values from an array into $this->data
	 * @param array $arr
	 */
	public function fill($arr) {
		if (is_array($arr)) {
			for ($i = 0; $i < count($arr); $i++) {
				//$temp = new stdClass();
				$temp = new FabriqModelItem($this->attributes, $this->db_table, $this->id_name);
				foreach ($arr[$i] as $key => $val) {
					$temp->$key = $val;
				}
				$this->data[] = $temp;
			}
		}
	}

	/**
	 * Remove element from collection at given index
	 * @param $index
	 * @return object/boolean
	 */
	public function remove($index) {
		if ($index == 0) {
			return array_shift($this->data);
		} else if ($index < $this->count()) {
			$r = $this->data[$index];
			array_splice($this->data, $index, 1);
			return $r;
		}
		return FALSE;
	}

	/**
	 * Adds an element to the collection
	 * @param object $obj
	 * @param integer $index
	 */
	public function add($obj, $index = null) {
		if ($index == null) {
			$this->data[] = $obj;
		} else {
			$this->data[$index] = $obj;
		}
	}

	/**
	 * Returns the database fields for easier use of the Database::prepare_select()
	 * function
	 * @return array
	 */
	public function fields() {
		return array_merge(array('id'), $this->attributes, array('updated', 'created'));
	}

	/**
	 * Returns the string value of the database fields for easier use of the
	 * Database::prepare_select() function
	 * @return string;
	 */
	public function fieldsStr() {
		return '`' . implode("`, `", $this->fields()) . '`';
	}
}

/**
 * @class Controller
 * Core controller class that all controllers extend
 */
class Controller {
	// public variables
	// private variables

	/**
	 * Returns an array of available methods
	 * @return array
	 */
	public function controllerMethods() {
		return get_class_methods(get_class($this));
	}

	/**
	 * Returns boolean on whether or not method exists in controller
	 * @param string $method
	 * @return boolean
	 */
	public function hasMethod($method) {
		return method_exists($this, $method);
	}
}

