<?php
/**
 * @files Base Controller class - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class Controller {
	// public variables
	// private variables
	
	/**
	 * Returns an array of available methods
	 * @return array
	 */
	public function controllerMethods() {
		return get_class_methods(get_class($this));
	}
	
	/**
	 * Returns boolean on whether or not method exists in controller
	 * @param string $method
	 * @return boolean
	 */
	public function hasMethod($method) {
		return method_exists($this, $method);
	}
}