<?php
/**
 * @file Messaging class - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

class Messaging {
	private static $errors = array();
	private static $messages = array();
	private static $warnings = array();
	private static $successes = array();
	
	public static function errors() {
		return self::$errors;
	}
	
	public static function messages() {
		return self::$messages;
	}
	
	public static function warnings() {
		return self::$warnings;
	}
	
	public static function successes() {
		return self::$successes;
	}
	
	public static function message($message, $type = 'error') {
		$message = trim($message);
		if ($message == '') {
			return FALSE;
		}
		switch ($type) {
			case 'message':
				self::$messages[] = $message;
			break;
			case 'warning':
				self::$warnings[] = $message;
			break;
			case 'success':
				self::$successes[] = $message;
			break;
			case 'error': default:
				self::$errors[] = $message;
			break;
		}
		return TRUE;
	}
	
	public static function display_messages($type = 'errors') {
		if (count(self::$$type) > 0) {
			$output = "<div class=\"message \n";
			switch ($type) {
				case 'messages':
					$output .= "messages\">\n";
				break;
				case 'warnings':
					$output .= "warnings\">\n";
				break;
				case 'successes':
					$output .= "successes\">\n";
				break;
				case 'errors': default:
					$output .= "errors\">\n";
					$output .= "\t<p>Before continuing, you must fix the following errors:</p>\n";
				break;
			}
			$output .= "\t<ul>\n";
			foreach (self::$$type as $msg) {
				$output .= "\t\t<li>{$msg}</li>\n";
			}
			$output .= "\t</ul>\n";
			$output .= "</div>\n";
			
			echo $output;
			
			return TRUE;
		}
		return FALSE;
	}
	
	public static function has_messages($type = 'errors') {
		return count(self::$$type);
	}
}