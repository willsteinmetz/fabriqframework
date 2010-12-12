<?php

class fabriqmodules_helper {
	public static function scan_modules() {
		$modules = array();
		if ($handle = opendir('modules')) {
			while (false !== ($file = readdir($handle))) {
				if (strpos($file, '.') === FALSE) {
					$modules[] = $file;
				}
			}
			closedir($handle);
		} else {
			throw new Exception('Modules directory could not be found/read');
		}
		return $modules;
	}
	
	public static function to_install($installed, $available) {
		$toInstall = array();
		
		foreach ($available as $mod) {
			if (!$installed->installed($mod)) {
				$toInstall[] = $mod;
			}
		}
		
		return $toInstall;
	}
}
	