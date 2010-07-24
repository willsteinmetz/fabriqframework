<?php
/**
 * @file Messaging class - DO NOT EDIT
 * @author Will Steinmetz
 * --
 * Copyright (c)2010, Ralivue.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Ralivue.com nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
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
			$output = "<div class=\"message \">\n";
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