<?php
/**
 * @file index.php
 * The index.php file includes the core required files for running a Fabriq based app:
 * @author Will Steinmetz
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

// set error displaying for testing purposes
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

// start sessions
session_start();

// require base Fabriq class
require_once('core/Fabriq.class.php');

// check to make sure application has been configured
$installed = Fabriq::installed();

// register default __autoload function
spl_autoload_register('fabriq_default_autoload');

// include core files
if ($installed) {
	require_once('config/config.inc.php');
}
require_once('core/Database.class.php');
require_once('core/Controller.class.php');
require_once('core/Model.class.php');
require_once('core/BaseMapping.class.php');
require_once('app/PathMap.class.php');
require_once('core/Messaging.class.php');
require_once('core/FabriqLibs.class.php');
require_once('core/modules/Modules.model.php');
require_once('core/modules/Perms.model.php');
require_once('core/modules/FabriqModules.class.php');
require_once('core/modules/FabriqModule.class.php');
require_once('core/modules/ModuleModel.class.php');
require_once('core/modules/ModuleConfigs.class.php');

// DEPRECATED
// include the application helper file if available
// @TODO remove for 2.0 release candidate
if (file_exists('app/helpers/application.helper.php')) {
	require_once('app/helpers/application.helper.php');
}

// initialize database
if ($installed) {
	$db = new Database($_FDB['default']);
	// get module handlers
	FabriqModules::get_handlers();
} else {
	$_FAPP = array();
	$_FAPP['templating'] = true;
	$_FAPP['templates']['default'] = 'fabriqinstall';
}

// query variable
$q = explode('/', $_GET['q']);
if (trim($q[0]) == '') {
	array_shift($q);
}

// include core JavaScript libraries
FabriqLibs::js_lib('jquery-1.4.4.min', 'jquery');
Fabriq::add_js('fabriq', 'core/');
Fabriq::add_css('fabriq.base', 'screen', 'core/');

// determine the controller and action to render
PathMap::map_path();

// check if user is logged in and if not give viewer
// unathenticated role
if (FabriqModules::enabled('users') && (!isset($_SESSION['FABMOD_USERS_roles']) || ($_SESSION['FABMOD_USERS_roles'] == ''))) {
	$role = FabriqModules::new_model('roles', 'Roles');
	$role->getRole('unauthenticated');
	$_SESSION['FABMOD_USERS_roles'] = serialize(array(
		$role->id,
		$role->role
	));
}

// determine whether to use templating by default
if (!isset($_FAPP['templating'])) {
	$_FAPP['templating'] = false;
} else if ($_FAPP['templating']) {
	require_once('core/FabriqTemplates.class.php');
	if (!isset($_FAPP['templates']['default'])) {
		$_FAPP['templates']['default'] = 'application';
	}
	FabriqTemplates::template($_FAPP['templates']['default']);
}

// include the controller, action, and helper files
require_once('app/controllers/application.controller.php');
if (!file_exists("app/controllers/" . PathMap::controller() . ".controller.php")) {
	PathMap::controller('errors');
	PathMap::render_controller('errors');
	PathMap::action('fourohfour');
	PathMap::render_action('fourohfour');
}

if (file_exists("app/helpers/" . PathMap::controller() . ".helper.php")) {
	require_once("app/helpers/" . PathMap::controller() . ".helper.php");
}
require_once("app/controllers/" . PathMap::controller() . ".controller.php");
$c = PathMap::controller() . '_controller';
$controller = new $c();
$a = str_replace('.', '_', PathMap::action());

if (!$controller->hasMethod($a)) {
	$c = 'errors_controller';
	if (PathMap::controller() != 'errors') {
	require_once("app/controllers/errors.controller.php");
		PathMap::controller('errors');
		PathMap::render_controller('errors');
	}
	$controller = new $c();
	$a = 'fourohfour';
	PathMap::action('fourohfour');
	PathMap::render_action('fourohfour');
}

call_user_func(array($controller, $a));

// run render controller if different from given controller
if (PathMap::render_controller() != PathMap::controller()) {
	if (!file_exists("app/controllers/" . PathMap::render_controller() . ".controller.php")) {
		PathMap::render_controller('errors');
		PathMap::render_action('fourohfour');
	}
	if (file_exists("app/helpers/" . PathMap::render_controller() . ".helper.php")) {
		require_once("app/helpers/" . PathMap::render_controller() . ".helper.php");
	}
	require_once("app/controllers/" . PathMap::render_controller() . ".controller.php");
	$c = PathMap::render_controller() . '_controller';
	$controller = new $c();
	
	$a = str_replace('.', '_', PathMap::render_action());
	if (!$controller->hasMethod($a)) {
		$c = 'errors_controller';
		require_once("app/controllers/errors.controller.php");
		$controller = new $c();
		$a = 'fourohfour';
	}
	call_user_func(array($controller, $a));
} else {
	// run render action if different from given action
	if (PathMap::render_action() != PathMap::action()) {
		$a = str_replace('.', '_', PathMap::render_action());
		if (!$controller->hasMethod($a)) {
			$c = 'errors_controller';
			require_once("app/controllers/errors.controller.php");
			$controller = new $c();
			$a = 'fourohfour';
		}
		call_user_func(array($controller, $a));
	}
}

if ($_FAPP['templating']) {
	FabriqTemplates::render();
} else {
	// render view (if necessary)
	switch(Fabriq::render()) {
		case 'none':
			break;
		case 'view':
			if (!file_exists("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php")) {
				require_once("app/views/errors/fourohfour.view.php");
			} else {
				require_once("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php");
			}
			break;
		case 'layout': default:
			if (!file_exists("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php")) {
				require_once("app/views/errors/fourohfour.view.php");
			} else {
				if (!file_exists("app/views/layouts/" . Fabriq::layout() . ".view.php")) {
					require_once('app/views/layouts/application.view.php');
				} else {
					require_once("app/views/layouts/" . Fabriq::layout() . ".view.php");
				}
			}
			break;
	}
}

// close the database connection
if ($installed) {
	$db->close();
}
?>