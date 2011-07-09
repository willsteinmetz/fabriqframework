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
		
		switch ($path->modpage) {
			case 'module':
				if (FabriqModules::enabled($path->controller)) {
					PathMap::controller('fabriqmodules');
					PathMap::render_controller('fabriqmodules');
					PathMap::arg(0, 'fabriqmodules');
					PathMap::action('index');
					PathMap::render_action('index');
					PathMap::arg(1, 'index');
					$mod = &FabriqModules::module($path->controller);
					$extra = explode('/', $path->extra);
					call_user_func_array(array($mod, $path->action), $extra);
					if ((Fabriq::render() != 'none') && FabriqModules::has_permission() && (!FabriqModules::stopMappedRender())) {
						FabriqModules::render($path->controller, $path->action);
					}
				} else {
					PathMap::controller('errors');
					PathMap::render_controller('errors');
					PathMap::action('fourohfour');
					PathMap::render_action('fourohfour');
				}
			break;
			case 'page': default:
				PathMap::controller($path->controller);
				PathMap::render_controller($path->controller);
				PathMap::arg(0, $path->controller);
				PathMap::action($path->action);
				PathMap::render_action($path->action);
				PathMap::arg(1, $path->action);
				if ($path->extra != '') {
					$extra = explode('/', $path->extra);
					if ($path->wildcard) {
						$p = explode('/', $_GET['q']);
						array_unshift($extra, $p[$path->wildcard]);
					}
					print_r($path);
					print_r($extra);
					for ($i = 0; $i < count($extra); $i++) {
						PathMap::arg(($i + 2), $extra[$i]);
					}
				} else {
					if ($path->wildcard) {
						$p = explode('/', $_GET['q']);
						PathMap::arg(2, $p[$path->wildcard]);
					}
				}
			break;
		}
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
}
	