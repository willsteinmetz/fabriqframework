<?php
/**
* @file FabriqModules.core.php
* @author Will Steinmetz
* This file contains functions and classes used throughout Fabriq modules - DO NOT EDIT
* 
* Copyright (c)2013, Ralivue.com
* Licensed under the BSD license.
* http://fabriqframework.com/license
*/

/**
 * @class FabriqModules
 * Provides the core functionality for interacting with modules in
 * the Fabriq platform
 */
abstract class FabriqModules {
	private static $modules = array();
	private static $body = '';
	private static $module_vars = array();
	private static $render_positions = array();
	private static $cssqueue = array();
	private static $jsqueue = array();
	private static $hasPermission = true;
	private static $stoppedMappedRender = false;
	private static $eventHandlers = array();

	/**
	 * Calls the install function to install a module for use in the
	 * Fabriq app
	 * @param string $module
	 * @return mixed
	 */
	public static function install($module) {
		// find the installer file
		$install = "modules/{$module}/{$module}.install.php";
		if (file_exists('sites/' . FabriqStack::site() . "/{$install}")) {
			require_once('sites/' . FabriqStack::site() . "/{$install}");
		} else if (file_exists($install)) {
			require_once($install);
		} else {
			throw new Exception("Module {$module} install file could not be found");
		}
		eval('$installer = new ' . $module . '_install();');
		return $installer->install();
	}

	/**
	 * Registers a module with the modules database table
	 * @param string $module
	 */
	public static function register_module($module) {
		$mod = new Modules();
		$mod->getModuleByName($module);
		if ($mod->count() == 0) {
			$file = "modules/{$module}/{$module}.info.json";
			if (file_exists('sites/' . FabriqStack::site() . "/{$file}")) {
				ob_start();
				readfile('sites/' . FabriqStack::site() . "/{$file}");
			} else if (file_exists($file)) {
				ob_start();
				readfile($file);
			} else {
				throw new Exception("Module {$module}'s information file does not exist");
			}
			$info = ob_get_clean();
			$info = json_decode($info, true);
			$mod->module = $module;
			$mod->enabled = 0;
			$mod->installed = 0;
			$mod->hasconfigs = 0;
			$mod->description = $info['description'];
			$mod->versioninstalled = $info['version'];
			if (isset($info['dependsOn'])) {
				$mod->dependson = implode(',', $info['dependsOn']);
			}
			$mod->id = $mod->create();

			// register configs if available
			if (isset($info['configs'])) {
				foreach ($info['configs'] as $con) {
					$config = new ModuleConfigs();
					$config->module = $mod->id;
					$config->var = $con;
					if (isset($info['configDefaults']) && array_key_exists($con, $info['configDefaults'])) {
						$config->val = $info['configDefaults'][$con];
					} else {
						$config->val = '';
					}
					$config->create();
				}
				$mod->hasconfigs = 1;
				$mod->update();
			}
		}

		return $mod->id;
	}

	/**
	 * Registers permissions available for setting for this module
	 * @param int $module_id
	 * @param array $perms
	 * @return array
	 */
	public static function register_perms($module_id, $perms) {
		$mod = new Modules($module_id);
		if (($mod->id == null) || ($mod->id == '')) {
			throw new Exception('Module does not exist');
		}
		$perm_ids = array();
		foreach ($perms as $p) {
			$perm = new Perms();
			$perm->permission = $p;
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
		if (file_exists('sites/' . FabriqStack::site() . "/{$uninstall}")) {
			require_once('sites/' . FabriqStack::site() . "/{$uninstall}");
		} else if (file_exists($uninstall)) {
			require_once($uninstall);
		} else {
			throw new Exception("Module {$module} install file could not be found");
		}
		eval('$installer = new ' . $module . '_install();');
		return $installer->uninstall();
	}

	/**
	 * Remove permissions for the given module
	 * @param int $module_id
	 */
	public static function remove_perms($module_id) {
		global $db;

		$sql = "DELETE FROM fabmods_perms WHERE `module` = ?";
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
		if (file_exists('sites/' . FabriqStack::site() . "/{$modfile}")) {
			require_once('sites/' . FabriqStack::site() . "/{$modfile}");
		} else if (file_exists($modfile)) {
			require_once($modfile);
		} else {
			throw new Exception("Module {$module} could not be loaded");
		}
		eval('$mod = new ' . $module . '_module();');
		self::$modules[$module] = $mod;
		self::$module_vars[$module] = array();
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

	/**
	 * Returns whether or the module is enabled
	 * @param string $module
	 * @return bool
	 */
	public static function enabled($module) {
		// make sure that fabriq is installed
		if (!Fabriq::installed()) {
			return false;
		}

		global $db;

		$sql = "SELECT enabled FROM fabmods_modules WHERE module = ?";
		$data = $db->prepare_select($sql, array('enabled'), array($module));
		if (count($data) == 0) {
			return FALSE;
		}
		return ($data[0]['enabled'] == 1) ? TRUE : FALSE;
	}

	/**
	 * Adds a module variable
	 * @param string $module
	 * @param string $name
	 * @param mixed $var
	 */
	public static function set_var($module, $name, $var) {
		self::$module_vars[$module][$name] = $var;
	}

	/**
	 * Adds a set of module variables at once
	 * @param string $module
	 * @param array $vars
	 */
	public static function set_vars($module, $vars) {
		if (count($vars) == 0) {
			return;
		}
		foreach ($vars as $key => $val) {
			self::$module_vars[$module][$key] = $val;
		}
	}

	/**
	 * Returns a module variable
	 * @param string $module
	 * @param string $module
	 * @return mixed
	 */
	public static function get_var($module, $var) {
		if (array_key_exists($var, self::$module_vars[$module])) {
			return self::$module_vars[$module][$var];
		}
		return false;
	}

	/**
	 * Returns the module variables for a module
	 * @param string $module
	 * @return array
	 */
	public static function get_vars($module) {
		if (array_key_exists($module, self::$module_vars)) {
			return self::$module_vars[$module];
		}
		return array();
	}

	/**
	 * Adds the output of this view to the body variable that is appended to the
	 * FabriqModules class' $body variable. For rendering one module at a time,
	 * use FabriqModules::render_now();
	 * @param string $module
	 * @param string $action
	 */
	public static function render($module, $action) {
		$render = new stdClass();
		$render->controller = $module;
		$render->action = $action;
		$render->type = 'module';
		
		FabriqTemplates::renderToBody($render);
	}

	/**
	 * Renders the module action's view content and returns it to be added at
	 * a specific place
	 * @param string $module
	 * @param string $action
	 */
	public static function render_now($module, $action) {
		$next = new stdClass();
		$next->controller = $module;
		$next->action = $action;
		$next->type = 'module';
		FabriqTemplates::renderToBody($next);
	}

	/**
	 * Add a module stylesheet to the CSS queue
	 * @param string $module
	 * @param string $stylesheet
	 * @param string $media
	 * @param string $path
	 * @param string $ext;
	 */
	public static function add_css($module, $stylesheet, $media = 'screen', $path = '', $ext = '.css') {
		if (file_exists('site/' . FabriqStack::site() . "/modules/{$module}/stylesheets/{$path}/{$stylesheet}.{$ext}")) {
			self::$cssqueue[] = array('css' => $stylesheet, 'media' => $media, 'path' => Pathmap::getUrl() . "sites/" . FabriqStack::site() . "/modules/{$module}/stylesheets/{$path}", 'ext' => $ext);
		} else {
			self::$cssqueue[] = array('css' => $stylesheet, 'media' => $media, 'path' => PathMap::getUrl() . "modules/{$module}/stylesheets/{$path}", 'ext' => $ext);
		}
	}

	/**
	 * Public getter for $cssqueue
	 * @return array
	 */
	public static function cssqueue() {
		return self::$cssqueue;
	}

	/**
	 * Add a module JavaScript to the JS queue
	 * @param string $module
	 * @param string $javascript
	 * @param string $path
	 * @param string $ext
	 */
	public static function add_js($module, $javascript, $path = '', $ext = '.js') {
		if (file_exists('site/' . FabriqStack::site() . "/modules/{$module}/javascrips/{$path}/{$javascript}.{$ext}")) {
			self::$jsqueue[] = array('js' => $javascript, 'path' => Pathmap::getUrl() . "sites/" . FabriqStack::site() . "/modules/{$module}/javascripts/{$path}", 'ext' => $ext);
		} else {
			self::$jsqueue[] = array('js' => $javascript, 'path' => Pathmap::getUrl() . "modules/{$module}/javascripts/{$path}", 'ext' => $ext);
		}
	}

	/**
	 * Public getter for $jsqueue
	 * @return array
	 */
	public static function jsqueue() {
		return self::$jsqueue;
	}

	/**
	 * Public getter and setter for hasPermission
	 * @param boolean $hasPerm
	 * @return boolean
	 */
	public static function has_permission($hasPerm = -1) {
		if ($hasPerm == -1) {
			return self::$hasPermission;
		} else {
			self::$hasPermission = $hasPerm;
		}
	}

	/**
	 * Creates a new instance of a module model
	 * @param string $module
	 * @param string $model
	 * @return object
	 */
	public static function new_model($module, $model) {
		$class = "{$module}_{$model}";
		if (!class_exists($class)) {
			$model = "modules/{$module}/models/{$model}.model.php";
			if (file_exists('sites/' . FabriqStack::site() . "/{$model}")) {
				require_once('sites/' . FabriqStack::site() . "/{$model}");
			} else {
				require_once($model);
			}
		}
		eval("\$item = new {$class}();");
		return $item;
	}

	/**
	 * Stops the mapped to module function's view from being rendered
	 * @param bool $stop
	 * @return bool
	 */
	public static function stopMappedRender($stop = null) {
		if ($stop != null) {
			self::$stoppedMappedRender = $stop;
		} else {
			return self::$stoppedMappedRender;
		}
	}

	/**
	 * Registers a handler for a module event
	 * @param string $eventModule
	 * @param string $eventAction
	 * @param string $eventName
	 * @param string $handlerModule
	 * @param string $handlerAction
	 */
	public static function register_handler($eventModule, $eventAction, $eventName, $handlerModule, $handlerAction) {
		global $db;

		$query = "INSERT INTO `fabmods_module_events`
			(`eventModule`, `eventAction`, `eventName`, `handlerModule`, `handlerAction`)
			VALUES (?, ?, ?, ?, ?)";
		$db->prepare_cud($query, array($eventModule, $eventAction, $eventName, $handlerModule, $handlerAction));
	}

	/**
	 * Removes a handler for a module event
	 * @param string $eventModule
	 * @param string $eventAction
	 * @param string $eventName
	 * @param string $handlerModule
	 * @param string $handlerAction
	 */
	public static function remove_handler($eventModule, $eventAction, $eventName, $handlerModule, $handlerAction) {
		global $db;

		$query = "DELETE FROM `fabmods_module_events`
			WHERE `eventModule` = ? AND `eventAction` = ? AND `eventName` = ? AND `handlerModule` = ? AND `handlerAction` = ?";
		$db->prepare_cud($query, array($eventModule, $eventAction, $eventName, $handlerModule, $handlerAction));
	}

	/**
	 * Get all module event handlers
	 */
	public static function get_handlers() {
		global $db;

		$query = "SELECT *
			FROM `fabmods_module_events`
			ORDER BY eventModule, eventAction, eventName";
		$data = $db->prepare_select($query, array('id', 'eventModule', 'eventAction', 'eventName', 'handlerModule', 'handlerAction'));
		for ($i = 0; $i < count($data); $i++) {
			if (!array_key_exists("{$data[$i]['eventModule']}_{$data[$i]['eventAction']}", self::$eventHandlers)) {
				self::$eventHandlers["{$data[$i]['eventModule']}_{$data[$i]['eventAction']}"] = array();
			}
			if (!is_array(self::$eventHandlers["{$data[$i]['eventModule']}_{$data[$i]['eventAction']}"][$data[$i]['eventName']])) {
				self::$eventHandlers["{$data[$i]['eventModule']}_{$data[$i]['eventAction']}"][$data[$i]['eventName']] = array();
			}
			self::$eventHandlers["{$data[$i]['eventModule']}_{$data[$i]['eventAction']}"][$data[$i]['eventName']][] = array(
				'module' => $data[$i]['handlerModule'],
				'action' => $data[$i]['handlerAction']
			);
		}
	}

	/**
	 * Triggers an event so that handlers can take action if necessary
	 * @param string $module
	 * @param string $action
	 * @param string $name
	 * @param mixed $data
	 */
	public static function trigger_event($module, $action, $name, $data = null) {
		if (array_key_exists("{$module}_{$action}", self::$eventHandlers)) {
			if (array_key_exists($name, self::$eventHandlers["{$module}_{$action}"])) {
				for ($i = 0; $i < count(self::$eventHandlers["{$module}_{$action}"][$name]); $i++) {
					FabriqModules::module(self::$eventHandlers["{$module}_{$action}"][$name][$i]['module'])->{self::$eventHandlers["{$module}_{$action}"][$name][$i]['action']}($data);
				}
			}
		}
	}
	
	/**
	 * Check that the given module is installed
	 * @param string $module
	 * @return boolean
	 */
	public static function installed($module) {
		global $db;
		
		$query = "SELECT COUNT(*) AS num FROM `fabmods_modules` WHERE module =? AND installed = ?";
		$data = $db->prepare_select($query, array('num'), array($module, 1));
		return ($data[0]['num'] > 0) ? true : false;
	}
	
	/**
	 * Check that the fabriqinstall module is installed
	 */
	public static function fabriqinstallReady() {
		// check that the fabriqinstall module is installed
		if (!FabriqModules::installed('fabriqinstall')) {
			FabriqModules::register_module('fabriqinstall');
			FabriqModules::install('fabriqinstall');
			$module = new Modules();
			$module->getModuleByName('fabriqinstall');
			$module->enabled = 1;
			$module->update();
			Messaging::message('Installed fabriqinstall module', 'success');
		}
	}
}

/**
 * @class Modules
 * Model for storing the info about a module
 */
class Modules extends Model {
	/**
	 * Constructor to set up the Module model
	 * @param int $id
	 */
	function __construct($id = NULL) {
		parent::__construct(array('module', 'enabled', 'hasconfigs', 'installed', 'versioninstalled', 'description', 'dependson'), 'fabmods_modules');
		if ($id != NULL) {
			$this->find($id);
		}
	}

	/**
	 * Get a module by its name
	 * @param int $module
	 */
	public function getModuleByName($module) {
		global $db;

		$sql = "SELECT * FROM fabmods_modules WHERE module=?";
		$this->fill($db->prepare_select($sql, $this->fields(), $module));
	}

	/**
	 * Get the modules that are enabled
	 */
	public function getEnabled() {
		global $db;

		$sql = "SELECT * FROM {$this->db_table} WHERE enabled = ? ORDER BY module";
		$this->fill($db->prepare_select($sql, $this->fields(), 1));
	}

	/**
	 * Get all of the modules
	 */
	public function getAll() {
		global $db;

		$sql = "SELECT * FROM {$this->db_table} ORDER BY module";
		$this->fill($db->prepare_select($sql, $this->fields()));
	}

	/**
	 * Enable the module assigned to this model instance
	 */
	public function enable() {
		global $db;

		$sql = "UPDATE {$this->db_table} SET enabled = ? WHERE {$this->id_name} = ?";
		$db->prepare_cud($sql, array(1, $this->id));
	}

	/**
	* Disable the module assigned to this model instance
	*/
	public function disable() {
		global $db;

		$sql = "UPDATE {$this->db_table} SET enabled = ? WHERE {$this->id_name} = ?";
		$db->prepare_cud($sql, array(0, $this->id));
	}

	/**
	 * Determine whether or not the given module is installed
	 * @param string $module
	 * @return bool
	 */
	public function installed($module) {
		foreach ($this as $mod) {
			if ($mod->module == $module) {
				return TRUE;
			}
		}
		return FALSE;
	}
}

/**
 * @class ModuleConfigs
 * Configuration model for modules
 */
class ModuleConfigs extends Model {
	public $configs = array();

	/**
	 * Constructor
	 * @param int $id
	 */
	function __construct($id = NULL) {
		parent::__construct(array('module', 'var', 'val'), 'fabmods_module_configs');
		if ($id != NULL) {
			$this->find($id);
		}
	}

	/**
	 * Get the configuration options for the given module
	 * @param int/string $module
	 */
	function getForModule($module) {
		global $db;

		if (is_numeric($module)) {
			$sql = "SELECT * FROM {$this->db_table} WHERE module = ? ORDER BY var";
		} else {
			$sql = "SELECT * FROM {$this->db_table} WHERE module = (SELECT id FROM fabmods_modules WHERE module = ?) ORDER BY var";
		}
		$this->fill($db->prepare_select($sql, $this->fields(), $module));

		for ($i = 0; $i < $this->count(); $i++) {
			$this->configs[$this[$i]->var] = $i;
		}
	}

	/**
	 * Get a specific configuration object for the given module
	 * @param int/string $module
	 * @param string $config
	 */
	function getConfig($module, $config) {
		global $db;

		if (is_numeric($module)) {
			$sql = "SELECT * FROM {$this->db_table} WHERE module = ? AND var = ? ORDER BY var";
		} else {
			$sql = "SELECT * FROM {$this->db_table} WHERE module = (SELECT id FROM fabmods_modules WHERE module = ?) AND var = ? ORDER BY var";
		}
		$this->fill($db->prepare_select($sql, $this->fields(), array($module, $config)));
	}
}

/**
 * @class Perms
 * Model for getting and using the values of a module permission
 */
class Perms extends Model {
	public $modules = array();

	/**
	 * Constructor for the module
	 * @param int $id
	 */
	function __construct($id = NULL) {
		parent::__construct(array('permission', 'module'), 'fabmods_perms');
		if ($id != NULL) {
			$this->find($id);
		}
	}

	/**
	 * Get the permissions for a module
	 * @param int $module_id
	 */
	public function getModulePerms($module_id) {
		global $db;

		$sql = "SELECT * FROM {$this->db_table} WHERE module=?";
		$this->fill($db->prepare_select($sql, $this->fields(), $module_id));
	}

	/**
	 * Get all of the permissions
	 */
	public function getAll() {
		global $db;

		$sql = "SELECT * FROM {$this->db_table} ORDER BY module, permission";
		$this->fill($db->prepare_select($sql, $this->fields()));

		// organize perms
		for ($i = 0; $i < $this->count(); $i++) {
			if (!array_key_exists($this[$i]->module, $this->modules)) {
				$this->modules[$this[$i]->module] = array();
			}
			$this->modules[$this[$i]->module][] = $i;
		}
	}
}

/**
 * @class FabriqModule
 * Core module controller class that all modules are extended from
 */
class FabriqModule extends Controller {
	public $name;
	public static $mname;

	function __construct() {
		$this->name = str_replace('_module', '', get_class($this));
		self::$mname = $this->name;
	}
}

/**
 * @class FabriqModules
 * Core model class that all module models are extended from
 */
class ModuleModel extends Model {
	public $module;

	/**
	 * Module model base class constructor
	 * Module tables must be prefixed with fabmod_[modulename]_.
	 * When defining the model, do not provide fabmod_[modulename]_
	 * @param array $attributes
	 * @param string $db_table
	 * @param string $id_name
	 */
	function __construct($module, $attributes, $db_table, $id_name = 'id') {
		$this->module = $module;
		$db_table = "fabmod_{$module}_{$db_table}";
		parent::__construct($attributes, $db_table, $id_name);
	}
}
