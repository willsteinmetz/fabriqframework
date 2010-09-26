<?php
/**
 * @file index.php
 * The index.php file includes the core required files
 * for running a Fabriq based app:
 *	 core/Fabriq.class.php
 *	 core/Database.class.php
 *	 core/Model.class.php
 * as well as the main config file:
 *	 config/config.inc.php
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

// set error displaying for testing purposes
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

// start sessions
session_start();

require_once('core/Fabriq.class.php');

// check to make sure application has been configured
Fabriq::installed();

// include core files
require_once('config/config.inc.php');
require_once('core/Database.class.php');
require_once('core/Controller.class.php');
require_once('core/Model.class.php');
require_once('core/BaseMapping.class.php');
require_once('app/PathMap.class.php');
require_once('core/Messaging.class.php');
require_once('app/helpers/application.helper.php');

// query variable
$q = explode('/', $_GET['q']);

// determine the controller and action to render
PathMap::map_path();

// initialize database
$db = new Database($_FDB['default']);

// include the controller, action, and helper files
require_once('app/controllers/application.controller.php');
if (!file_exists("app/controllers/" . PathMap::controller() . ".controller.php")) {
	header('Location: ' . PathMap::build_path(404));
} else {
	if (file_exists("app/helpers/" . PathMap::controller() . ".helper.php")) {
		require_once("app/helpers/" . PathMap::controller() . ".helper.php");
	}
	require_once("app/controllers/" . PathMap::controller() . ".controller.php");
	$c = PathMap::controller() . '_controller';
	$controller = new $c();
	$a = str_replace('.', '_', PathMap::action());
	
	if (!$controller->hasMethod($a)) {
		header('Location: ' . PathMap::build_path(404));
	} else {
		call_user_func(array($controller, $a));
		
		// run render controller if different from given controller
		if (PathMap::render_controller() != PathMap::controller()) {
			if (!file_exists("app/controllers/" . PathMap::render_controller() . ".controller.php")) {
				header('Location: ' . PathMap::build_path(404));
			} else {
				if (file_exists("app/helpers/" . PathMap::render_controller() . ".helper.php")) {
					require_once("app/helpers/" . PathMap::render_controller() . ".helper.php");
				}
				require_once("app/controllers/" . PathMap::render_controller() . ".controller.php");
				$c = PathMap::render_controller() . '_controller';
				$controller = new $c();
				
				$a = str_replace('.', '_', PathMap::render_action());
				if (!$controller->hasMethod($a)) {
					header('Location: ' . PathMap::build_path(404));
				} else {
					call_user_func(array($controller, $a));
				}
			}
		} else {
			// run render action if different from given action
			if (PathMap::render_action() != PathMap::action()) {
				$a = str_replace('.', '_', PathMap::render_action());
				if (!$controller->hasMethod($a)) {
					header('Location: ' . PathMap::build_path(404));
				} else {
					call_user_func(array($controller, $a));
				}
			}
		}
		
		// render view (if necessary)
		switch(Fabriq::render()) {
			case 'none':
				break;
			case 'view':
				if (!file_exists("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php")) {
					header('Location: ' . PathMap::build_path(404));
				} else {
					require_once("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php");
				}
				break;
			case 'layout': default:
				if (!file_exists("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php")) {
					header('Location: ' . PathMap::build_path(404));
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
}

// close the database connection
$db->close();
?>