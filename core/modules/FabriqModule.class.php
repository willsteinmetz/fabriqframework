<?php
/**
 * @file Module base file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
class FabriqModule extends Controller {
	public $name;
	public static $mname;
	
	function __construct() {
		$this->name = str_replace('_module', '', get_class($this));
		self::$mname = $this->name;
	}
}
	