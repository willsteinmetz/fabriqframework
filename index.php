<?php
/**
 * @file index.php
 * The index.php file includes the core required files for running a Fabriq based app:
 * @author Will Steinmetz
 * 
 * Copyright (c)2013, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

// set error displaying for testing purposes
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

// start sessions
session_start();

// require core Fabriq base classes
require_once('core/Fabriq.core.php');

// check to make sure application has been configured
$installed = Fabriq::installed();

// register default __autoload function
spl_autoload_register('fabriq_default_autoload');

// include core files
if ($installed) {
	require_once('config/config.inc.php');
}
require_once('core/FabriqModules.core.php');
require_once('app/PathMap.class.php');

// require the core files
FabriqStack::requireCore();

// initialize database
if ($installed) {
	$db = new Database($_FDB['default']);
	// get module handlers
	FabriqModules::get_handlers();
} else {
	$_FAPP = array();
	$_FAPP['templates']['default'] = 'fabriqinstall';
}

// query variable
$q = explode('/', $_GET['q']);
if (trim($q[0]) == '') {
	array_shift($q);
}

// check if user is logged in and if not give viewer
// unathenticated role
FabriqStack::checkUserStatus();

// determine the controller and action to render
PathMap::map_path();

// determine which template to set initially
FabriqTemplates::init();

// include the controller and action files
require_once('app/controllers/application.controller.php');

FabriqStack::processQueue();

FabriqTemplates::render();

// close the database connection
if ($installed) {
	$db->close();
}
?>