/**
 * @file - DO NOT EDIT
 * Fabriq javascript functionality
 * functions
 * --
 * Copyright (c)2010, Ralivue.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *		 * Redistributions of source code must retain the above copyright
 *			 notice, this list of conditions and the following disclaimer.
 *		 * Redistributions in binary form must reproduce the above copyright
 *			 notice, this list of conditions and the following disclaimer in the
 *			 documentation and/or other materials provided with the distribution.
 *		 * Neither the name of the Ralivue.com nor the
 *			 names of its contributors may be used to endorse or promote products
 *			 derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Ralivue.com BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * --
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
