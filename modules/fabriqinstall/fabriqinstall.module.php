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
			$query = "SELECT version FROM fabriq_config ORDER BY installed DESC, version DESC LIMIT 1";
			$db->query($query);
			$data = mysqli_fetch_array($db->result);
			$this->version = $data['version'];
			
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
			if ((substr($method, 0, 7) == 'update_') && (substr($method, 0, 11) != 'update_step')) {
				$version = str_replace('_', '.', str_replace('update_', '', $method));
				if ($version > $this->installVersion) {
					$this->installVersion = $version;
				}
			}
		}
		
		// set up display elements
		Fabriq::empty_css_queue();
		FabriqModules::add_css('fabriqinstall', 'fabriqinstall');
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
				PathMap::arg(2, 1);
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
	
	/**
	 * Install step 2
	 * Website configuration details
	 */
	protected function install_step2($continue = TRUE) {
		Fabriq::title('Site configuration');
		
		if (isset($_POST['submit'])) {
			if (strlen(trim($_POST['title'])) == 0) {
				Messaging::message('You must enter a page title');
			}
			if (strlen(trim($_POST['title_sep'])) == 0) {
				Messaging::message('You must enter a page title separator');
			}
			if (strlen(trim($_POST['apppath'])) == 0) {
				Messaging::message('You must enter an application path');
			}
			if (!filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
				Messaging::message('You must enter a valid URL');
			}
			
			if (!Messaging::has_messages()) {
				$siteConfig = array(
					'title' => $_POST['title'],
					'title_pos' => $_POST['title_pos'],
					'title_sep' => $_POST['title_sep'],
					'cleanurls' => $_POST['cleanurls'],
					'url' => $_POST['url'],
					'apppath' => $_POST['apppath']
				);
				$_SESSION['FAB_INSTALL_site'] = serialize($siteConfig);
				
				if ($continue) {
					// go to next step
					header("Location: " . PathMap::build_path('fabriqinstall', 'install', 3));
					exit();
				}
			}
			
			FabriqModules::set_var('fabriqinstall', 'submitted', true);
		}
	}

	/**
	 * Install step 3
	 * Database configuration details
	 */
	protected function install_step3($continue = TRUE) {
		Fabriq::title('Database configuration');
		
		// go back to site configuration step if the session isn't set
		if (!isset($_SESSION['FAB_INSTALL_site']) || ($_SESSION['FAB_INSTALL_site'] == '')) {
			PathMap::arg(2, 2);
			$this->install_step2();
		}
		
		if (isset($_POST['submit'])) {
			if (strlen(trim($_POST['db'])) == 0) {
				Messaging::message('You must enter a database name');
			}
			if (strlen(trim($_POST['user'])) == 0) {
				Messaging::message('You must enter a database user');
			}
			if (strlen(trim($_POST['pwd'])) == 0) {
				Messaging::message('You must enter a database user password');
			}
			if (strlen(trim($_POST['server'])) == 0) {
				Messaging::message('You must enter a database server');
			}
			// test database connectivity
			$mysqli = @mysqli_connect(trim($_POST['server']), trim($_POST['user']), trim($_POST['pwd']), trim($_POST['db']));
			if (!$mysqli) {
				Messaging::message('Error connecting to the database. Please check your database settings and try again.');
			} else {
				mysqli_close($mysqli);
			}
			
			if (!Messaging::has_messages()) {
				$dbConfig = array(
					'db' => trim($_POST['db']),
					'user' => trim($_POST['user']),
					'pwd' => trim($_POST['pwd']),
					'server' => trim($_POST['server'])
				);
				$_SESSION['FAB_INSTALL_db'] = serialize($dbConfig);
				
				// write out configuration file
				$siteConfig = unserialize($_SESSION['FAB_INSTALL_site']);
				$confFile = 'sites/' . FabriqStack::site() . '/config/config.inc.php';
				$fh = fopen($confFile, 'w');
				fwrite($fh, "<?php\n");
				fwrite($fh, "/**\n");
				fwrite($fh, " * @file\n");
				fwrite($fh, " * Base config file for a Fabriq app.\n");
				fwrite($fh, " */\n\n");
				fwrite($fh, "\$_FAPP = array(\n");
				fwrite($fh, "	'title' => \"{$siteConfig['title']}\",\n");
				fwrite($fh, "	'title_pos' => '{$siteConfig['title_pos']}',\n");
				fwrite($fh, "	'title_sep' => \"{$siteConfig['title_sep']}\",\n");
				fwrite($fh, "	'cleanurls' => {$siteConfig['cleanurls']},\n");
				fwrite($fh, "	'cdefault' => 'homepage',\n");
				fwrite($fh, "	'adefault' => 'index',\n");
				fwrite($fh, "	'url' => '{$siteConfig['url']}',\n");
				fwrite($fh, "	'apppath' => '{$siteConfig['apppath']}',\n");
				fwrite($fh, "	'templates' => array(\n");
				fwrite($fh, "		'default' => 'application'\n");
				fwrite($fh, "	)\n");
				fwrite($fh, ");\n\n");
				fwrite($fh, "\$_FDB['default'] = array(\n");
				fwrite($fh, "	'user' => '{$_POST['user']}',\n");
				fwrite($fh, "	'pwd' => '{$_POST['pwd']}',\n");
				fwrite($fh, "	'db' => '{$_POST['db']}',\n");
				fwrite($fh, "	'server' => '{$_POST['server']}'\n");
				fwrite($fh, ");\n");
				fclose($fh);
				
				// write default controller if the file isn't already there
				// file may exist from being created in a dev environment or this is
				// a distributed web app
				$contFile = 'sites/' . FabriqStack::site() . "/app/controllers/homepage.controller.php";
				if (!file_exists($contFile)) {
					$fh = fopen($contFile, 'w');
					fwrite($fh, "<?php\n");
					fwrite($fh, "class homepage_controller extends Controller {\n");
					fwrite($fh, "\tfunction index() {\n");
					fwrite($fh, "\t\tFabriq::title('Welcome to {$siteConfig['title']}');\n");
					fwrite($fh, "\t}\n");
					fwrite($fh, "}\n");
					fclose($fh);
				}
				
				// write default action if it doesn't already exist
				// may already exist from being created in a dev environmentor this is
				// a distributed web app
				if (!is_dir('sites/' . FabriqStack::site() . "/app/views/homepage")) {
					mkdir('sites/' . FabriqStack::site() . "/app/views/homepage");
				}
				$actionFile = 'sites/' . FabriqStack::site() . "/app/views/homepage/index.view.php";
				if (!file_exists($actionFile)) {
					$fh = fopen($actionFile, 'w');
					fwrite($fh, "<h1>homepage#index</h1>\n");
					fclose($fh);
				}
				
				// create the framework database tables
				global $db;
				$db_info = array(
					'server' => trim($_POST['server']),
					'user' => trim($_POST['user']),
					'pwd' => trim($_POST['pwd']),
					'db' => trim($_POST['db'])
				);
				$db = new Database($db_info);
				// install config table
				$query = "CREATE TABLE IF NOT EXISTS  `fabriq_config` (
						`version` VARCHAR(10) NOT NULL,
						`installed` DATETIME NOT NULL,
						PRIMARY KEY (`version`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->query($query);
				$query = "INSERT INTO fabriq_config (version, installed) VALUES (?, ?)";
				$db->prepare_cud($query, array($this->installVersion, date('Y-m-d H:i:s')));
				// modules table
				$query = "CREATE TABLE IF NOT EXISTS `fabmods_modules` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`module` varchar(100) NOT NULL,
						`enabled` tinyint(4) NOT NULL,
						`hasconfigs` tinyint(1) NOT NULL,
						`installed` tinyint(1) NOT NULL,
						`versioninstalled` varchar(20) NOT NULL,
						`description` text NOT NULL,
						`dependson` text,
						`created` datetime NOT NULL,
						`updated` datetime NOT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$db->query($query);
				// module configs table
				$query = "CREATE TABLE IF NOT EXISTS `fabmods_module_configs` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`module` int(11) NOT NULL,
						`var` varchar(100) NOT NULL,
						`val` text NOT NULL,
						`created` datetime NOT NULL,
						`updated` datetime NOT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$db->query($query);
				// module perms table
				$query = "CREATE TABLE IF NOT EXISTS `fabmods_perms` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`permission` varchar(100) NOT NULL,
						`module` int(11) NOT NULL,
						`created` datetime NOT NULL,
						`updated` datetime NOT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
				$db->query($query);
				// install the module events table
				$query = "CREATE TABLE IF NOT EXISTS `fabmods_module_events` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`eventModule` VARCHAR(50) NOT NULL,
					`eventAction` VARCHAR(50) NOT NULL,
					`eventName` VARCHAR(100) NOT NULL,
					`handlerModule` VARCHAR(50) NOT NULL,
					`handlerAction` VARCHAR(50) NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->query($query);
				
				if (!isset($_SESSION['FAB_INSTALL_mods_installed'])) {
					Messaging::message('Configuration file has been written', 'success');
					Messaging::message('Core database tables have been created', 'success');
					FabriqModules::register_module('pathmap');
					FabriqModules::register_module('roles');
					FabriqModules::register_module('users');
					FabriqModules::register_module('fabriqupdates');
					FabriqModules::register_module('sitemenus');
					FabriqModules::register_module('fabriqmodules');
					FabriqModules::register_module('fabriqinstall');
					FabriqModules::install('pathmap');
					$module = new Modules();
					$module->getModuleByName('pathmap');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed pathmap module', 'success');
					FabriqModules::install('roles');
					$module = new Modules();
					$module->getModuleByName('roles');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed roles module', 'success');
					FabriqModules::install('users');
					$module = new Modules();
					$module->getModuleByName('users');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed users module', 'success');
					FabriqModules::install('fabriqupdates');
					$module = new Modules();
					$module->getModuleByName('fabriqupdates');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed fabriqupdates module', 'success');
					FabriqModules::register_module('sitemenus');
					FabriqModules::install('sitemenus');
					$module = new Modules();
					$module->getModuleByName('sitemenus');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed sitemenus module', 'success');
					FabriqModules::register_module('fabriqmodules');
					FabriqModules::install('fabriqmodules');
					$module = new Modules();
					$module->getModuleByName('fabriqmodules');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed fabriqmodules module', 'success');
					FabriqModules::register_module('fabriqinstall');
					FabriqModules::install('fabriqinstall');
					$module = new Modules();
					$module->getModuleByName('fabriqinstall');
					$module->enabled = 1;
					$module->update();
					Messaging::message('Installed fabriqinstall module', 'success');
					
					// get admin role and give it all perms so that the admin can actually set
					// things up
					$role = FabriqModules::new_model('roles', 'Roles');
					$role->getRole('administrator');
					$perms = new Perms();
					$perms->getAll();
					foreach ($perms as $perm) {
						$modPerm = FabriqModules::new_model('roles', 'ModulePerms');
						$modPerm->permission = $perm->id;
						$modPerm->role = $role->id;
						$modPerm->create();
					}
					$_SESSION['FAB_INSTALL_mods_installed'] = true;
				}
				
				if ($continue) {
					// go to next step
					header("Location: " . PathMap::build_path('fabriqinstall', 'install', 4));
					exit();
				}
			}
			
			FabriqModules::set_var('fabriqinstall', 'submitted', true);
		}
	}
	
	/**
	 * Install step 4
	 * Install the core database tables and modules and create the
	 * default administrator
	 */
	protected function install_step4($continue = TRUE) {
		Fabriq::title('Core module configuration');
		FabriqTemplates::template('fabriqinstall');
		
		Messaging::message('Be sure to continue with module set up in order to complete the install process', 'warning');
		
		if (isset($_POST['submit'])) {
			$emailPattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
			$displayPattern = '/([A-z0-9]){6,24}/';
			$user = FabriqModules::new_model('users', 'Users');
			$user->display = $_POST['display'];
			$user->email = $_POST['email'];
			$user->encpwd = $_POST['pwd'];
			
			if (!preg_match($displayPattern, $user->display)) {
				Messaging::message("Display name is invalid");
			}
			if (!preg_match($emailPattern, $user->email)) {
				Messaging::message("e-mail address is invalid");
			}
			if ((strlen($user->encpwd) < 8) || ($user->encpwd == $user->display) || ($user->encpwd == $user->email) || ($user->encpwd != $_POST['confpwd'])) {
				Messaging::message("Password is invalid");
			}
			
			if (!Messaging::has_messages()) {
				$user->status = 1;
				$user->banned = 0;
				$user->forcepwdreset = 0;
				$user->id = $user->create();
				$user->encpwd = crypt($user->encpwd, $user->id);
				$user->update();
				
				$role = FabriqModules::new_model('roles', 'Roles');
				$role->getRole('administrator');
				$userRole = FabriqModules::new_model('users', 'UserRoles');
				$userRole->user = $user->id;
				$userRole->role = $role->id;
				$userRole->id = $userRole->create();
				
				global $_FAPP;
				$url = $_FAPP['url'] . PathMap::build_path('users', 'login');
				$message = <<<EMAIL
Hello {$user->display},

Your account has been created on the {$_FAPP['title']} website.

You can log in by navigating to {$url} in your browser.

Thanks,
The {$_FAPP['title']} team


NOTE: Do not reply to this message. It was automatically generated.
EMAIL;
				mail(
					$user->email,
					"Your account at {$_FAPP['title']}",
					$message,
					'From: noreply@' . str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $_FAPP['url'])))
				);
				
				if ($continue) {
					// go to next step
					header("Location: " . PathMap::build_path('fabriqinstall', 'install', 5));
					exit();
				}
			}
			
			FabriqModules::set_var('fabriqinstall', 'submitted', true);
		}
	}
	
	/**
	 * Install step 5
	 * Display message about end of install and finish installation
	 */
	protected function install_step5() {
		Fabriq::title('Install complete');
		FabriqTemplates::template('fabriqinstall');
		
		// delete session variables
		unset($_SESSION['FAB_INSTALL_site']);
		unset($_SESSION['FAB_INSTALL_db']);
		unset($_SESSION['FAB_INSTALL_mods_installed']);
	}
	
	/**
	 * Determine which update step to process
	 */
	public function update() {
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			switch (PathMap::arg(2)) {
				case 2:
					$this->update_step2();
				break;
				case 3:
					$this->update_step3();
				break;
				case 4:
					$this->update_step4();
				break;
				case 1: default:
					$this->update_step1();
					PathMap::arg(2, 1);
				break;
			}
		}
	}
	
	/**
	 * Update step 1
	 * Display the overview of the update
	 */
	protected function update_step1() {
		Fabriq::title('Fabriq Update');
	}
	
	/**
	 * Update step2
	 * Apply the updates to the framework
	 */
	protected function update_step2($continue = TRUE) {
		Fabriq::title('Framework updates');
		
		// get the list of updates
		$methods = get_class_methods('fabriqinstall_module');
		$available = array();
		foreach ($methods as $method) {
			if ((substr($method, 0, 7) == 'update_') && (substr($method, 0, 11) != 'update_step') || ($this->version == null)) {
				$version = str_replace('_', '.', str_replace('update_', '', $method));
				if ($version > $this->version) {
					$available[] = $method;
				}
			}
		}
		
		$toInstall = array();
		for ($i = 0; $i < count($available); $i++) {
			$toInstall[] = $this->{$available[$i]}();
		}
		$submitted = false;
		
		if (isset($_POST['submit'])) {
			if (!Messaging::has_messages()) {
				if ($continue) {
					header("Location: " . PathMap::build_path('fabriqinstall', 'update', 3));
					exit();
				}
			} else {
				$submitted = true;
			}
		}
		FabriqModules::set_var('fabriqinstall', 'toInstall', $toInstall);
		FabriqModules::set_var('fabriqinstall', 'submitted', $submitted);
	}

	/**
	 * Update step 3
	 * Module updates
	 */
	protected function update_step3($continue = TRUE) {
		Fabriq::title('Module Updates');
		global $db;
		
		// get modules and versions that are in the database
		$query = "SELECT `id`, `module`, `versionInstalled` FROM `fabmods_modules` ORDER BY `module`;";
		$installed = $db->prepare_select($query, array('id', 'module', 'versionInstalled'));
		
		// look for updates to the installed modules in the code
		$available = array();
		$installs = array();
		for ($i = 0; $i < count($installed); $i++) {
			if (!array_key_exists($installed[$i]['module'], $available)) {
				$available[$installed[$i]['module']] = array();
			}
			$install = "modules/{$installed[$i]['module']}/{$installed[$i]['module']}.install.php";
			if (file_exists($install)) {
				require_once($install);
				eval('$installer = new ' . $installed[$i]['module'] . '_install();');
				$installs[$installed[$i]['module']] = $installer;
				
				$methods = get_class_methods($installer);
				foreach ($methods as $method) {
					if ((substr($method, 0, 7) == 'update_') && ((str_replace('_', '.', str_replace('update_', '', $method)) > $installed[$i]['versionInstalled']) || ($installed[$i]['versionInstalled'] == null))) {
						$available[$installed[$i]['module']][] = array('method' => $method, 'version' => str_replace('_', '.', str_replace('update_', '', $method)));
					}
				}
			}
		}
		
		if (isset($_POST['submit'])) {
			foreach ($available as $module => $updates) {
				if (count($updates) > 0) {
					foreach ($updates as $update) {
						$installs[$module]->{$update['method']}();
					}
				}
			}
			if ($continue) {
				header("Location: " . PathMap::build_path('fabriqinstall', 'update', 4));
				exit();
			}
		} else {
			FabriqModules::set_var('fabriqinstall', 'installed', $installed);
			FabriqModules::set_var('fabriqinstall', 'available', $available);
		}
	}
	
	/**
	 * Update step4
	 * Finish the updating process
	 */
	protected function update_step4() {
		Fabriq::title('Updates Complete');
		unset($_SESSION['FAB_UPDATES']);
	}
	
	protected function update_2_0_1() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.0.1']) || !$installed['2.0.1']) {
				// mark the update as done
				$installed['2.0.1'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.0.1', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.0.1',
			'description' => 'Fixed an install bug',
			'hasDisplay' => false
		);
	}

	protected function update_2_1() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1']) || !$installed['2.1']) {
				// mark the update as done
				$installed['2.1'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1',
			'description' => 'Starting development branch 2.1, initial clean up of files',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_1() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.1']) || !$installed['2.1.1']) {
				// mark the update as done
				$installed['2.1.1'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.1', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.1',
			'description' => 'Cleaned up .htaccess file, moved errors to pathmap module',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_2() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.2']) || !$installed['2.1.2']) {
				FabriqModules::register_module('fabriqmodules');
				FabriqModules::install('fabriqmodules');
				$module = new Modules();
				$module->getModuleByName('fabriqmodules');
				$module->enabled = 1;
				$module->update();
				
				// mark the update as done
				$installed['2.1.2'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.2', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.2',
			'description' => 'Cleaned up rendering and revamped module execution',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_3() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.3']) || !$installed['2.1.3']) {
				// mark the update as done
				$installed['2.1.3'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.3', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.3',
			'description' => 'Fabriq install and update now a module, fixed a rendering bug',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_4() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.4']) || !$installed['2.1.4']) {
				// mark the update as done
				$installed['2.1.4'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.4', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.4',
			'description' => 'Multiple sites on one Fabriq codebase',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_5() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.5']) || !$installed['2.1.5']) {
				// mark the update as done
				$installed['2.1.5'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.5', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.5',
			'description' => 'Fixing missing paths for the pathmap module',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_6() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.6']) || !$installed['2.1.6']) {
				// mark the update as done
				$installed['2.1.6'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.6', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.6',
			'description' => 'Fixing a bug for stylesheet and script includes in sites',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_7() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.7']) || !$installed['2.1.7']) {
				// mark the update as done
				$installed['2.1.7'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.7', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.7',
			'description' => 'Fixing bugs in fabriqmodules and BaseMapp::map_path()',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_8() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.8']) || !$installed['2.1.8']) {
				// mark the update as done
				$installed['2.1.8'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.8', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.8',
			'description' => 'Bug fixes',
			'hasDisplay' => false
		);
	}
	
	protected function update_2_1_9() {
		if (isset($_POST['submit'])) {
			global $db;
			$installed = unserialize($_SESSION['FAB_UPDATES']);
			if (!is_array($installed)) {
				$installed = array();
			}
			if (!isset($installed['2.1.9']) || !$installed['2.1.9']) {
				// mark the update as done
				$installed['2.1.9'] = true;
				$_SESSION['FAB_UPDATES'] = serialize($installed);
				$query = "INSERT INTO `fabriq_config`
					(`version`, `installed`)
					VALUES
					(?, ?)";
				$db->prepare_cud($query, array('2.1.9', date('Y-m-d H:i:s')));
			}
		}
		return array(
			'version' => '2.1.9',
			'description' => 'Bug fixes',
			'hasDisplay' => false
		);
	}
}
	