<?php

class Modules extends Model {
	function __construct($id = NULL) {
		parent::__construct(array('module', 'enabled', 'hasconfigs', 'installed', 'versioninstalled', 'description', 'dependson'), 'fabmods_modules');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	public function getModuleByName($module) {
		global $db;
		
		$sql = "SELECT * FROM fabmods_modules WHERE module=?";
		$this->fill($db->prepare_select($sql, $this->fields(), $module));
	}
	
	public function getEnabled() {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} WHERE enabled = ? ORDER BY module";
		$this->fill($db->prepare_select($sql, $this->fields(), 1));
	}
	
	public function getAll() {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} ORDER BY module";
		$this->fill($db->prepare_select($sql, $this->fields()));
	}
	
	public function enable() {
		global $db;
		
		$sql = "UPDATE {$this->db_table} SET enabled = ? WHERE {$this->id_name} = ?";
		$db->prepare_cud($sql, array(1, $this->id));
	}
	
	public function disable() {
		global $db;
		
		$sql = "UPDATE {$this->db_table} SET enabled = ? WHERE {$this->id_name} = ?";
		$db->prepare_cud($sql, array(0, $this->id));
	}
	
	public function installed($module) {
		foreach ($this as $mod) {
			if ($mod->module == $module) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
	