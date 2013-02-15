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

// determine which site should be servied
FabriqStack::determineSite();

// check to make sure application has been configured
$installed = Fabriq::installed();

// register default __autoload function
spl_autoload_register('fabriq_default_autoload');

// include core files
if ($installed) {
	if ((file_exists('config/config.inc.php')) && (FabriqStack::site() == 'default')) {
		require_once('config/config.inc.php');
	} else {
		require_once('sites/' . FabriqStack::site() . '/config/config.inc.php');
	}
}
require_once('core/FabriqModules.core.php');
if (file_exists('sites/' . FabriqStack::site() . '/app/PathMap.class.php')) {
	require_once('sites/' . FabriqStack::site() . '/app/PathMap.class.php');
} else {
	require_once('app/PathMap.class.php');
}

// query variable
$q = explode('/', $_GET['q']);
if (trim($q[0]) == '') {
	array_shift($q);
}

// initialize database
if ($installed) {
	$db = new Database($_FDB['default']);
	// get module handlers
	FabriqModules::get_handlers();
	// check fabriqinstall
	FabriqModules::fabriqinstallReady();
} else {
	$_FAPP = array();
	$_FAPP['templates']['default'] = 'fabriqinstall';
	$appPath = '/';
	$aPath = substr($_SERVER['REQUEST_URI'], 1);
	$aPath = str_replace('index.php?q=', '', $aPath);
	$aPath = explode('/', $aPath);
	$i = 0;
	while (($aPath[$i] != 'fabriqinstall') && ($i < count($aPath))) {
		$appPath .= $aPath[$i] . '/';
		$i++;
	}
	$_FAPP['url'] = "http://{$_SERVER['HTTP_HOST']}";
	$_FAPP['apppath'] = str_replace('//', '/', $appPath);
}

// require the core files
FabriqStack::requireCore();

// check if user is logged in and if not give viewer
// unathenticated role
FabriqStack::checkUserStatus();

// determine the controller and action to render
PathMap::map_path();

// determine which template to set initially
FabriqTemplates::init();

// include the controller and action files
if (file_exists('sites/' . FabriqStack::site() . '/app/controllers/application.controller.php')) {
	require_once('sites/' . FabriqStack::site() . '/app/controllers/application.controller.php');
} else {
	require_once('app/controllers/application.controller.php');
}

FabriqStack::processQueue();

FabriqTemplates::render();

// close the database connection
if ($installed) {
	$db->close();
}
?>