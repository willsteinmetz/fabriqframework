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

// require core Fabriq base classes
require_once('core/Fabriq.core.php');

// include bootstrap to get started
require_once('core/bootstrap.class.php');
Fabriq\Core\Bootstrap::init();

// determine which site should be servied
// FabriqStack::determineSite();

// // check to make sure application has been configured
//$installed = Fabriq::installed();
$installed = Fabriq\Core\Config::installed();

require_once('core/FabriqModules.core.php');
// if (file_exists('sites/' . FabriqStack::site() . '/app/PathMap.class.php')) {
  // require_once('sites/' . FabriqStack::site() . '/app/PathMap.class.php');
// } else {
  require_once('app/PathMap.class.php');
// }

// initialize database
if ($installed) {
  $db = new Database(Fabriq\Core\Databases::db_config('default'));
  // get module handlers
  FabriqModules::get_handlers();
  // check fabriqinstall
  FabriqModules::fabriqinstallReady();
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
