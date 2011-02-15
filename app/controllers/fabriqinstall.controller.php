<?php
/**
 * @file fabriqinstall.controller.php
 * @author Will Steinmetz
 * Fabriq install and update framework functionality
 */

class fabriqinstall_controller extends Controller {
	function __construct() {
		Fabriq::add_css('fabriqinstall', 'screen', 'core/');
	}
	
	public function install() {
		switch (PathMap::arg(2)) {
			case 2:
				$this->install_step2();
			break;
			case 1: default:
				$this->install_step1();
			break;
		}
	}
	
	private function install_step1() {
		
	}
	
	private function install_step2() {
		
	}
	
	public function update() {
		
	}
} 
