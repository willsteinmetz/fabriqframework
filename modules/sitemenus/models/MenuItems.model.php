<?php
/**
 * @file MenuItems.model.php
 * @author Will Steinmetz
 */

class sitemenus_MenuItems extends ModuleModel {
	public $children;
	
	function __construct() {
		parent::__construct('sitemenus', array('itemName', 'path', 'menu', 'parentItem', 'weight', 'newWindow'), 'menuitems');
	}
	
	/**
	 * Get all of the children for this menu item
	 */
	public function getChildren() {
		global $db;
		
		$query = "SELECT *
			FROM {$this->db_table}
			WHERE `parentItem` = ?
			ORDER BY `weight`, `itemName`;";
		$data = $db->prepare_select($query, $this->fields(), array($this->id));
		
		$this->children = array();
		for ($i = 0; $i < count($data); $i++) {
			$this->children[$i] = FabriqModules::new_model('sitemenus', 'MenuItems');
			$this->children[$i]->fill(array($data[$i])); 
		}
	}
	
	/**
	 * Get all decendents for this item
	 */
	public function getAllDecendents() {
		$this->getChildren();
		
		for ($i = 0; $i < count($this->children); $i++) {
			$this->children[$i]->getAllDecendents();
		}
	}
	
	/**
	 * Override for the destroy function
	 */
	public function destroy($index = 0) {
		$this->children = null;
		$this->getChildren();
					
		for ($i = 0; $i < count($this->children); $i++) {
			$this->children[$i]->destroy();
		}
		$this->children = null;
		$this->getChildren();
		if (count($this->children) == 0) {
			parent::destroy();
		}
	}
	
	/**
	 * Get the items for a given menu
	 * @param int $menu
	 */
	public function getMenuItems($menu) {
		global $db;
		
		$query = "SELECT *
			FROM `{$this->db_table}`
			WHERE `menu` = ?
			AND (`parentItem` IS NULL OR `parentItem` = '')
			ORDER BY `weight`, `itemName`;";
		$data = $db->prepare_select($query, $this->fields(), array($menu));
		
		$items = array();
		for ($i = 0; $i < count($data); $i++) {
			$items[$i] = FabriqModules::new_model('sitemenus', 'MenuItems');
			$items[$i]->fill(array($data[$i]));
		}
		
		return $items;
	}
	
	/**
	 * Build the HTML for this menu item
	 */
	public function getItemHtml($admin = false) {
		$html = "<li>\n";
		if ($this->path != '') {
			$html .= "\t<a href=\"";
			if ($this->path[0] == '/') {
				$html .= call_user_func_array('PathMap::build_path', explode('/', substr($this->path, 1)));
			} else {
				$html .= $this->path;
			}
			$html .= "\"";
			if ($this->newWindow) {
				$html .= " target=\"_blank\"";
			}
			$html .= ">{$this->itemName}</a>";
		} else {
			$html .= "\t{$this->itemName}";
		}
		if ($admin) {
			$html .= " - <button onclick=\"window.location = '" . PathMap::build_path('sitemenus', 'items', 'update', $this->menu, $this->id) . "'\">Edit</button> <button onclick=\"window.location = '" . PathMap::build_path('sitemenus', 'items', 'destroy', $this->menu, $this->id) . "'\">Delete</button>";
		}
		$html .= "\n";
		if (count($this->children) > 0) {
			$html .= "\t\t<ul>\n";
			for ($i = 0; $i < count($this->children); $i++) {
				$html .= $this->children[$i]->getItemHtml($admin);
			}
			$html .= "\t\t</ul>\n";
		}
		$html .= "</li>\n";
		
		return $html;
	}
	
	/**
	 * Builds the option item for a select list
	 */
	public function getItemSelectOption($level = 0, $parentItem = NULL, $current = NULL) {
		if (is_null($current) || ($current != $this->id)) {
			$html = "<option value=\"{$this->id}\"";
			if ($parentItem == $this->id) {
				$html .= ' selected="selected"';
			}
			$html .= ">";
			for ($i = 0; $i < ($level * 2); $i++) {
				$html .= "-";
			}
			$html .= "{$this->itemName}";
			$html .= "</option>\n";
			if (count($this->children) > 0) {
				for ($i = 0; $i < count($this->children); $i++) {
					$html .= $this->children[$i]->getItemSelectOption(($level + 1), $parentItem, $current);
				}
			}
		}
		
		return $html;
	}
}
	