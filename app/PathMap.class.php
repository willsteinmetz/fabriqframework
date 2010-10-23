<?php
/**
 * @files Path mapping class
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class PathMap extends BaseMapping {
	public static function map_path() {
		// all basic mapping is done by parent class
		parent::map_path();
		
		/**
		 * Define custom mapping below.
		 * Example: custom mapping may be used for a site containing user profiles.
		 *   A path to a user's profile might be http://example.com/profiles/username
		 *   For the above path, the controller would be profiles and the action
		 *   would like be read or show. The parent map_path() function will set
		 *   the controller properly, but will define the action as username. A
		 *   switch statement can be used to see if the controller is profile and if
		 *   so to set the 3rd argument of the arg parameter to username and the
		 *   action to read/show.
		 */
		switch(self::controller()) {
			case '404':
				self::controller('errors');
				self::action('fourohfour');
				self::render_controller('errors');
				self::render_action('fourohfour');
			break;
			case '500':
				self::controller('errors');
				self::action('fiveohoh');
				self::render_controller('errors');
				self::render_action('fiveohoh');
			break;
		}
	}
}