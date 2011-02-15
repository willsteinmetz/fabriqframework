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
			if (strlen(trim($_POST['pagetitle'])) == 0) {
				Messaging::message('You must enter a page title');
			}
			if (strlen(trim($_POST['titleseparator'])) == 0) {
				Messaging::message('You must enter a page title separator');
			}
			if (!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9]+)$/', $_POST['defaultcontroller'])) {
				Messaging::message('You must enter a default controller. The default controller can only contain alpha-numeric and underscore characters and must begin with a letter.');
			}
			if (!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9]+)$/', $_POST['defaultaction'])) {
				Messaging::message('You must enter a default action. The default action can only contain alpha-numeric and underscore characters and must begin with a letter.');
			}
			if (strlen(trim($_POST['apppath'])) == 0) {
				Messaging::message('You must enter an application path');
			}
			
			if (!Messaging::has_messages()) {
				$siteConfig = array(
					'pagetitle' => $_POST['pagetitle'],
					'titleseparator' => $_POST['titleseparator'],
					'defaultcontroller' => $_POST['defaultcontroller'],
					'defaultaction' => $_POST['defaultaction'],
					'apppath' => $_POST['apppath']
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
	}
	
	private function install_step4() {
		
	}
	
	private function install_step5() {
		
	}
	
	public function update() {
		
	}
} 
