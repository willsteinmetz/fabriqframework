<?php
/**
 * @file fabriqinstall.controller.php
 * @author Will Steinmetz
 * Fabriq install and update framework functionality
 */

class fabriqinstall_controller extends Controller {
	function __construct() {
		global $installed;
		
		if (((PathMap::action() == 'install') || (PathMap::render_action() == 'install')) && $installed && (PathMap::arg(2) < 4)) {
			global $_FAPP;
			header("Location: " . PathMap::build_path($_FAPP['cdefault'], $_FAPP['adefault']));
			exit();
		}
		Fabriq::add_css('fabriqinstall', 'screen', 'core/');
	}
	
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
	
	private function install_step1() {
		Fabriq::title('Start');
	}
	
	private function install_step2() {
		Fabriq::title('Site configuration');
		
		if (isset($_POST['submit'])) {
			if (strlen(trim($_POST['title'])) == 0) {
				Messaging::message('You must enter a page title');
			}
			if (strlen(trim($_POST['title_sep'])) == 0) {
				Messaging::message('You must enter a page title separator');
			}
			if (!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9]+)$/', $_POST['cdefault'])) {
				Messaging::message('You must enter a default controller. The default controller can only contain alpha-numeric and underscore characters and must begin with a letter.');
			}
			if (!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9]+)$/', $_POST['adefault'])) {
				Messaging::message('You must enter a default action. The default action can only contain alpha-numeric and underscore characters and must begin with a letter.');
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
					'cdefault' => $_POST['cdefault'],
					'adefault' => $_POST['adefault'],
					'url' => $_POST['url'],
					'apppath' => $_POST['apppath'],
					'templating' => ($_POST['templating']) ? 1 : 0 
				);
				$_SESSION['FAB_INSTALL_site'] = serialize($siteConfig);
				
				// go to next step
				PathMap::arg(2, 3);
				$this->install_step3();
			}
			
			FabriqTemplates::set_var('submitted', true);
		}
	}

	private function install_step3() {
		Fabriq::title('Database configuration');
		
		// go back to site configuration step if the session isn't set
		if (!isset($_SESSION['FAB_INSTALL_site']) || ($_SESSION['FAB_INSTALL_site'] == '')) {
			PathMap::arg(2, 2);
			$this->install_step2();
		}
		
		if (isset($_POST['submit'])) {
			if (strlen(trim($_POST['db'])) == 0) {
				array_push($errors, 'You must enter a database name');
			}
			if (strlen(trim($_POST['user'])) == 0) {
				array_push($errors, 'You must enter a database user');
			}
			if (strlen(trim($_POST['pwd'])) == 0) {
				array_push($errors, 'You must enter a database user password');
			}
			if (strlen(trim($_POST['server'])) == 0) {
				array_push($errors, 'You must enter a database server');
			}
			
			if (!Messaging::has_messages()) {
				$dbConfig = array(
					'db' => $_POST['db'],
					'user' => $_POST['user'],
					'pwd' => $_POST['pwd'],
					'server' => $_POST['server']
				);
				$_SESSION['FAB_INSTALL_db'] = serialize($dbConfig);
				
				// go to next step
				PathMap::arg(2, 4);
				$this->install_step4();
			}
			
			FabriqTemplates::set_var('submitted', true);
		}
	}
	
	private function install_step4() {
		
	}
	
	private function install_step5() {
		
	}
	
	public function update() {
		
	}
} 
