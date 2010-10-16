<?php
/**
 * @file PostgreSQL Database connectivity file - DO NOT EDIT
 * @author Will Steinmetz
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
abstract class FabriqLibs {
	private static $phpqueue = array();
	/**
	 * Add a JavaScript library file to the JavaScript queue
	 * @param string $file
	 * @param string $libdir
	 * @param string $ext
	 */
	public static function js_lib($file, $libdir = '', $ext = 'js') {
		Fabriq::add_js($file, 'libs/javascript/' . $libdir . '/', $ext);
	}
	
	/**
	 * Add a CSS library file to the CSS queue
	 * @param string $file
	 * @param string $libdir
	 * @param string $ext
	 * @param string $media
	 */
	public static function css_lib($file, $libdir = '', $ext = 'js', $media = 'screen') {
		Fabriq::add_css($file, $media, '/libs/css/' . $libdir . '/', $ext);
	}
	
	/**
	 * Returns the number of php libraries in the queue
	 * @return integer
	 */
	public static function php_lib_count() {
		return count(self::$phpqueue);
	}
	
	/**
	 * Returns the PHP library queue
	 * @return array
	 */
	public static function phpqueue() {
		return self::$phpqueue;
	}
	
	/**
	 * Include external PHP libraries to be used with Fabriq
	 * @param unknown_type $file
	 * @param unknown_type $libdir
	 */
	public static function php_lib($file, $libdir = '') {
		self::$phpqueue[] = 'libs/php/' . $libdir . '/' . $file;
	}
}
