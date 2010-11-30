<?php
/**
 * @file Paths model for pathmap module - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class Paths_mm extends ModuleModel {
	function __construct($path = null) {
		parent::__construct('pathmap', array('path', 'modpage', 'controller', 'action', 'extra', 'wildcard'), 'paths');
		if ($path != null) {
			$this->get_by_path($path);
		}
	}
	
	public function get_by_path($path) {
		global $db;
		
		// check for specific path
		$sql = "SELECT * FROM {$this->db_table} WHERE path=" . (($db->type == 'MySQL') ? '?' : '$1');
		$this->fill($db->prepare_select($sql, $this->fields(), $path));
		
		if ($this->count() > 0) {
			return;
		}
		
		// get wildcard path from most to least specific
		$paths = array();
		$path = explode('/', $path);
		
		for ($i = 0; $i < count($path); $i++) {
			$p = '';
			for ($j = 0; $j < $i; $j++) {
				$p .= $path[$j] . '/';
			}
			$paths[] = $p . '%';
		}
		
		array_shift($paths);
		$paths = array_reverse($paths);
		
		$likes = '';
		for ($i = 0; $i < count($paths); $i++) {
			$likes .= "path LIKE " . (($db->type == 'MySQL') ? '?' : '$' . ($i + 1)) . ' ';
			if ($i < (count($paths) - 1)) {
				$likes .= 'OR ';
			}
		}
		
		$sql = "SELECT * FROM {$this->db_table} WHERE {$likes} LIMIT 1";
		$this->fill($db->prepare_select($sql, $this->fields(), $paths));
	}
	
	public function get_by_details($controller, $action, $extra) {
		global $db;
		
		if ($db->type == 'MySQL') {
			$sql = "SELECT * FROM {$this->db_table} WHERE controller = ? AND action = ? AND extra = ?";
		} else {
			$sql = "SELECT * FROM {$this->db_table} WHERE controller = \$1 AND action = \$2 AND extra = \$3";
		}
		$this->fill($db->prepare_select($sql, $this->fields(), array($controller, $action, $extra)));
	}
}
	