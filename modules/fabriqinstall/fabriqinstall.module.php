<?php
/**
 * @file fabriqinstall module file
 * @author Will Steinmetz
 */
 
class fabriqinstall_module extends FabriqModule {
	protected $installVersion = null;
	
	function __construct() {
		parent::__construct();
		
		global $installed;
		global $_FAPP;
		
		$processing = FabriqStack::processing();
		
		if (($processing->action == 'install') && $installed && (PathMap::arg(2) < 4)) {
			header("Location: " . PathMap::build_path($_FAPP['cdefault'], $_FAPP['adefault']));
			exit();
		} else if (($processing->action == 'install') && $installed && (PathMap::arg(2) == 4)) {
			// determine which version is installed
			if (!isset($_POST['submit'])) {
				global $db;
				$query = "SHOW TABLES;";
				$db->query($query);
				$tables = array();
				while ($row = $db->result->fetch_array()) {
					$tables[] = $row[0];
				}
				if (in_array('fabmod_users_users', $tables)) {
					$query = "SELECT COUNT(*) AS num FROM fabmod_users_users";
					$db->query($query);
					$row = $db->result->fetch_array();
					if ($row['num'] > 0) {
						header("Location: " . PathMap::build_path($_FAPP['cdefault'], $_FAPP['adefault']));
						exit();
					}
				}
			}
		} else if ($processing->action == 'update') {
			// figure out what updates are available
			global $db;
			$query = "SHOW TABLES;";
			$db->query($query);
			$tables = array();
			while ($row = $db->result->fetch_array()) {
				$tables[] = $row[0];
			}
			if (!in_array('fabriq_config', $tables)) {
				$this->version = null;
				$_SESSION['FAB_INSTALL_nomods'] = true;
			} else {
				$query = "SELECT version FROM fabriq_config ORDER BY installed DESC, version DESC LIMIT 1";
				$db->query($query);
				$data = mysqli_fetch_array($db->result);
				$this->version = $data['version'];
			}
			
			if (!FabriqModules::module('roles')->hasRole('administrator')) {
				if ($this->version != null) {
					header('Location: ' . PathMap::build_path('users', 'login', 'fabriqinstall', 'update'));
					exit();
				}
			}
		}
		Fabriq::empty_css_queue();
		Fabriq::add_css('fabriqinstall', 'screen', 'core/');
		FabriqTemplates::template('fabriqinstall');
	}
}
	