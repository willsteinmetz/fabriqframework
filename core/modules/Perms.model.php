<?php

class Perms extends Model {
	public $modules = array();
	
	function __construct($id = NULL) {
		parent::__construct(array('permission', 'module'), 'fabmods_perms');
		if ($id != NULL) {
			$this->find($id);
		}
	}
	
	public function getModulePerms($module_id) {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} WHERE module=?";
		$this->fill($db->prepare_select($sql, $this->fields(), $module_id));
	}
	
	public function getAll() {
		global $db;
		
		$sql = "SELECT * FROM {$this->db_table} ORDER BY module, permission";
		$this->fill($db->prepare_select($sql, $this->fields()));
		
		// organize perms
		for ($i = 0; $i < $this->count(); $i++) {
			if (!array_key_exists($this[$i]->module, $this->modules)) {
				$this->modules[$this[$i]->module] = array();
			}
			$this->modules[$this[$i]->module][] = $i;
		}
	}
}
	