<?php
/**
 * @file pathmap module file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
 
class pathmap_module extends FabriqModule {
	public function redirect($p) {
		$path = FabriqModules::new_model('pathmap', 'Paths');
		$path->get_by_path($p);
		
		if (count($path)) {
			switch ($path->modpage) {
				case 'module':
					if (FabriqModules::enabled($path->controller)) {
						$extra = explode('/', $path->extra);
						if (count($extra) == 1) {
							if ($extra[0] == '') {
								unset($extra[0]);
							}
						}
						PathMap::arg(0, $path->controller);
						PathMap::arg(1, $path->action);
						for ($i = 0; $i < count($extra); $i++) {
							PathMap::arg($i + 2, $extra[$i]);
						}
						FabriqStack::enqueue($path->controller, $path->action, 'module', $extra);
						return true;
					} else {
						FabriqStack::error(404);
					}
				break;
				case 'page': default:
					PathMap::arg(0, $path->controller);
					PathMap::arg(1, $path->action);
					FabriqStack::enqueue($path->controller, $path->action);
					if ($path->extra != '') {
						$extra = explode('/', $path->extra);
						if ($path->wildcard) {
							$p = explode('/', $_GET['q']);
							array_unshift($extra, $p[$path->wildcard]);
						}
						for ($i = 0; $i < count($extra); $i++) {
							PathMap::arg(($i + 2), $extra[$i]);
						}
					} else {
						if ($path->wildcard) {
							$p = explode('/', $_GET['q']);
							PathMap::arg(2, $p[$path->wildcard]);
						}
					}
					return true;
				break;
			}
		}
		return false;
	}
	
	public function register_path($path, $controller, $action, $modpage = 'page', $extra = null, $wildcard = null) {
		$map = FabriqModules::new_model('pathmap', 'Paths');
		$map->path = $path;
		$map->controller = $controller;
		$map->action = $action;
		$map->modpage = $modpage;
		$map->extra = $extra;
		$map->wildcard = $wildcard;
		$map->id = $map->create();
	}
	
	public function remove_path($path) {
		$map = FabriqModules::new_model('pathmap', 'Paths');
		$map->get_by_path($path);
		$map->destroy(); // @TODO add confirmation
	}
	
	public function create() {
		if ($_POST['add_path'] == 1) {
			$map = FabriqModules::new_model('pathmap', 'Paths');
			$map->path = $_POST[$this->name . '_path'];
			$map->controller = $_POST[$this->name . '_controller'];
			$map->action = $_POST[$this->name . '_action'];
			$map->modpage = $_POST[$this->name . '_modpage'];
			$map->extra = $_POST[$this->name . '_extra'];
			$map->wildcard = $_POST[$this->name . '_wildcard'];
			
			if (!preg_match('/^([a-zA-Z0-9_\-\/]{1}){1,100}$/', $map->path)) {
				Messaging::message('Paths can only contain letters, numbers, the underscore character, and dashes');
			}
			if (Messaging::has_messages() == 0) {
				$map->id = $map->create();
			}
			
			FabriqModules::set_var($this->name, 'submitted', true);
			FabriqModules::set_var($this->name, 'map', $map);
		}
	}
	
	public function start_update($controller, $action, $extra) {
		$map = FabriqModules::new_model('pathmap', 'Paths');
		$map->get_by_details($controller, $action, $extra);
		FabriqModules::set_var($this->name, 'map', $map);
	}
	
	public function update($controller, $action, $extra) {
		$map = FabriqModules::new_model('pathmap', 'Paths');
		$map->get_by_details($controller, $action, $extra);
		if ($_POST['update_path'] == 1) {
			if ($_POST['destroy_path'] == 1) {
				$map->destroy();
			} else {
				$map->path = $_POST[$this->name . '_path'];
				
				if (!preg_match('/^([a-zA-Z0-9_\-\/]{1}){1,100}$/', $map->path)) {
					Messaging::message('Paths can only contain letters, numbers, the underscore character, and dashes');
				}
				if (Messaging::has_messages() == 0) {
					$map->update();
				}
			}
		} else if ($_POST['add_path'] == 1) {
			$map = FabriqModules::new_model('pathmap', 'Paths');
			$map->path = $_POST[$this->name . '_path'];
			$map->controller = $_POST[$this->name . '_controller'];
			$map->action = $_POST[$this->name . '_action'];
			$map->modpage = $_POST[$this->name . '_modpage'];
			$map->extra = $_POST[$this->name . '_extra'];
			$map->wildcard = $_POST[$this->name . '_wildcard'];
			
			if (!preg_match('/^([a-zA-Z0-9_\-\/]{1}){1,100}$/', $map->path)) {
				Messaging::message('Paths can only contain letters, numbers, the underscore character, and dashes');
			}
			if (Messaging::has_messages() == 0) {
				$map->id = $map->create();
			}
			
			FabriqModules::set_var($this->name, 'submitted', true);
		}
		FabriqModules::set_var($this->name, 'map', $map);
	}
	
	public function destroy($map_id) {
		$map = FabriqModules::new_model('pathmap', 'Paths');
		$map->find($map_id);
		$map->destroy();
	}
	
	public function register_vars($controller, $action, $modpage = 'page') {
		$vars = array(
			'pathmap_controller' => $controller,
			'pathmap_action' => $action,
			'pathmap_modpage' => $modpage
		);
		FabriqModules::set_vars($this->name, $vars);
	}
	
	/**
	 * 403 permission denied
	 */
	public function _403() {
		header("HTTP/1.0 403 Forbidden");
		Fabriq::title('403 Forbidden');
	}
	
	/**
	 * 404 not found
	 */
	public function _404() {
		header("HTTP/1.0 404 Not Found");
		Fabriq::title('404 Not Found');
	}
	
	/**
	 * 500 server error
	 */
	public function _500() {
		header("HTTP/1.0 500 Internal Server Error");
		Fabriq::title('500 Internal Server Error');
	}
}
	