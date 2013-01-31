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
		
		// make sure that we're good to run the requested action
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
			/*$query = "SHOW TABLES;";
			$db->query($query);
			$tables = array();
			while ($row = $db->result->fetch_array()) {
				$tables[] = $row[0];
			}*/
			/*if (!in_array('fabriq_config', $tables)) {
				$this->version = null;
				$_SESSION['FAB_INSTALL_nomods'] = true;
			} else {*/
				$query = "SELECT version FROM fabriq_config ORDER BY installed DESC, version DESC LIMIT 1";
				$db->query($query);
				$data = mysqli_fetch_array($db->result);
				$this->version = $data['version'];
			//}
			
			if (!FabriqModules::module('roles')->hasRole('administrator')) {
				if ($this->version != null) {
					header('Location: ' . PathMap::build_path('users', 'login', 'fabriqinstall', 'update'));
					exit();
				}
			}
		}
		
		// set the install version
		$this->installVersion = '0.0';
		$updates = get_class_methods('fabriqinstall_module');
		foreach ($updates as $method) {
			if (substr($method, 0, 6) == 'update') {
				$version = str_replace('update_', '', str_replace('_', '.', $method));
				if ($version > $this->installVersion) {
					$this->installVersion = $version;
				}
			}
		}
		
		// set up display elements
		Fabriq::empty_css_queue();
		Fabriq::add_css('fabriqinstall', 'screen', 'core/');
		FabriqTemplates::template('fabriqinstall');
	}

	/**
	 * Determine which install step to go to
	 */
	public function install() {
		switch (PathMap::arg(2)) {
			case 2:
				$this->install_step2();
			break;
			case 3:
				$this->install_step3();
			break;
			case 4:
				$this->install_step4();
			break;
			case 5:
				$this->install_step5();
			break;
			case 1: default:
				$this->install_step1();
			break;
		}
	}
	
	/**
	 * Install step 1
	 * Displays the overview of starting the install of
	 * the framework
	 */
	protected function install_step1() {
		Fabriq::title('Start');
	}
}
	