<?php
/**
 * @file Paths model for pathmap module - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class pathmap_Paths extends ModuleModel {
	function __construct($path = null) {
		parent::__construct('pathmap', array('path', 'modpage', 'controller', 'action', 'extra', 'wildcard'), 'paths');
		if ($path != null) {
			$this->get_by_path($path);
		}
	}
	
	public function get_by_path($path) {
		global $db;
		
		// check for specific path
		$sql = "SELECT * FROM {$this->db_table} WHERE path=?";
		$this->fill($db->prepare_select($sql, $this->fields(), $path));
		
		if ($this->count() > 0) {
			return;
		}
		
		// get wildcard path from most to least specific
		$paths = array();
		$path = explode('/', $path);
		
		for ($i = 0; $i <= count($path); $i++) {
			$p = '';
			for ($j = 0; $j < $i; $j++) {
				$p .= $path[$j] . '/';
			}
			$paths[] = $p . '!#';
		}
		
		array_shift($paths);
		$paths = array_reverse($paths);

		$q = '';
		for ($i = 0; $i < count($paths); $i++) {
			$q .= '?';
			if ($i < (count($paths) - 1)) {
				$q .= ', ';
			}
		}
		
		$sql = "SELECT * FROM {$this->db_table} WHERE path IN ({$q}) ORDER BY path DESC LIMIT 1";
		$this->fill($db->prepare_select($sql, $this->fields(), $paths));
	}
	
	public function get_by_details($controller, $action, $extra) {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} WHERE controller = ? AND action = ? AND extra = ?";
		$this->fill($db->prepare_select($sql, $this->fields(), array($controller, $action, $extra)));
	}
}
	