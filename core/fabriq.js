/**
 * @file - DO NOT EDIT
 * Fabriq javascript functionality
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
var Fabriq;
if (!Fabriq) Fabriq = {'settings': {}, 'custom': {}};

/**
 * Returns the base path for the app
 */
Fabriq.base_path = function() {
	return this.settings.basePath;
}

/**
 * Returns whether or not clean URLs are used
 */
Fabriq.clean_urls = function() {
	return this.settings.cleanURLs;
}

/**
 * Returns the string for an ajax path
 */
Fabriq.ajax_path = function() {
	var path = '';
	if (!this.clean_urls()) {
		path += 'index.php?q=';
	} else {
		path += this.base_path();
	}
	args = arguments;
	path += args[0];
	if (args.length > 1) {
		path += '/' + args[1]
	}
	
	return path;
}

/**
 * Returns the string for a path
 */
Fabriq.build_path = function() {
	var path = '';
	if (!this.clean_urls()) {
		path += 'index.php?q=';
	} else {
		path += this.base_path();
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
