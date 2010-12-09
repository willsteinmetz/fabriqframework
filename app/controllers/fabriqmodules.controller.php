<?php
/**
 * @file Module display file - DO NOT EDIT
 * @author Will Steinmetz
 * This controller is used whenever a page's content is rendered
 * completely by one or more modules
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class fabriqmodules_controller extends Controller {
	function index() {
		
	}
	
	public function manage() {
		global $_FAPP;
		// @TODO require role
		Fabriq::title('Admin | Manage modules');
		if (!$_FAPP['templating']) {
			global $modules;
		}
		$modules = new Modules();
		$modules->getAll();
		if ($_FAPP['templating']) {
			FabriqTemplates::set_var('modules', $modules);
		}
	}
	
	public function enable() {
		
	}
	
	public function disable() {
		
	}
	
	public function configure() {
		
	}
}
		