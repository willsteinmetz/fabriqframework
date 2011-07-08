/**
 * @file - DO NOT EDIT
 * Fabriq javascript functionality
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

Fabriq = {
	settings: {},
	custom: {},
	
	/**
	 * Returns the base path for the app
	 */
	base_path: function() {
		return Fabriq.settings.basePath;
	},
	
	/**
	 * Returns whether or not clean URLs are used
	 */
	clean_urls: function() {
		return Fabriq.settings.cleanURLs;
	},
	
	/**
	 * Returns the string for an ajax path
	 */
	ajax_path: function() {
		var path = '';
		if (!Fabriq.clean_urls()) {
			path += 'index.php?q=';
		} else {
			path += Fabriq.base_path();
		}
		args = arguments;
		path += args[0];
		if (args.length > 1) {
			path += '/' + args[1]
		}
		
		return path;
	},
	
	/**
	 * Returns the string for a path
	 */
	build_path: function() {
		var path = '';
		if (!Fabriq.clean_urls()) {
			path += 'index.php?q=';
		} else {
			path += Fabriq.base_path();
		}
		args = arguments;
		path += args[0];
		if (args.length > 1) {
			for (var i = 1; i < args.length; i++) {
				path += '/' + args[i];
			}
		}
		
		return path;
	}
}
