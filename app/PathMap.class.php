<?php
/**
 * @files Base Controller class
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