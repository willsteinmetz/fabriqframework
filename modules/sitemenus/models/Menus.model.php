<?php
/**
 * @file Menus.model.php
 * @author Will Steinmetz
 */

class sitemenus_Menus extends ModuleModel {
	public $items;
	
	function __construct() {
		parent::__construct('sitemenus', array('menuName', 'description'), 'menus');
	}
	
	/**
	 * Get a list of all menus
	 */
	public function getAll() {
		global $db;
		
		$query = "SELECT *
			FROM {$this->db_table}
			ORDER BY menuName;";
		$this->fill($db->prepare_select($query, $this->fields()));
	}
	
	/**
	 * Build this menu
	 */
	public function buildMenu() {
		//$this->items = FabriqModules::new_model('sitemenus', 'MenuItems');
		//$this->items->getMenuItems($this->id);
		$this->items = FabriqModules::new_model('sitemenus', 'MenuItems')->getMenuItems($this->id);
		
		// flesh out all of the menu children
		for ($i = 0; $i < count($this->items); $i++) {
			$this->getItemChildren($this->items[$i]);
		}
	}
	
	/**
	 * Flesh out the menu children
	 * @param mixed $menuItem
	 */
	private function getItemChildren(&$menuItem) {
		$menuItem->getChildren();
		
		for ($i = 0; $i < count($menuItem->children); $i++) {
			$this->getItemChildren($menuItem->children[$i]);
		}
	}
	
	/**
	 * Get a menu by its name
	 * @param string $name
	 */
	public function getMenuByName($menuName) {
		global $db;
		
		$result = $db->prepare_select("SELECT `id` FROM {$this->db_table} WHERE menuName = ?", array('id'), array($menuName));
		$this->find($result[0]['id']);
	}
}
	