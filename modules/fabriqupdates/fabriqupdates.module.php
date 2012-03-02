<?php
/**
 * @file fabriqupdates module file
 * @author Will Steinmetz
 */
 
class fabriqupdates_module extends FabriqModule {
	function index() {
		if (FabriqModules::module('roles')->hasRole('administrator')) {
			global $db;
			
			Fabriq::title('Fabriq Updates');
			
			// get the currently installed version
			$query = "SELECT version FROM fabriq_config ORDER BY installed DESC, version DESC LIMIT 1";
			$db->query($query);
			$data = mysqli_fetch_array($db->result);
			$installedVersion = $data['version'];
			FabriqModules::set_var('fabriqupdates', 'installedVersion', $installedVersion);
			
			// get the list of updates from the site
			try {
				$versions = json_decode(file_get_contents('http://fabriqframework.com/changelog/json'), TRUE);
				
				$available = array();
				$upToDate = false;
				if (is_array($versions) && (count($versions) > 0)) {
					foreach ($versions as $version => $info) {
						if ($version > $installedVersion) {
							$available[$version] = $info;
						}
					}
					
					if (count($available) == 0) {
						$upToDate = true;
					}
					
					FabriqModules::set_var('fabriqupdates', 'available', $available);
					FabriqModules::set_var('fabriqupdates', 'connected', true);
				} else {
					FabriqModules::set_var('fabriqupdates', 'connected', false);
				}
				FabriqModules::set_var('fabriqupdates', 'upToDate', $upToDate);
				
			} catch (Exception $e) {
				FabriqModules::set_var('fabriqupdates', 'connected', false);
			}
		}
	}
}
	