<?php
/**
 * @files This file contains functions used throughout Fabriq applications
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

abstract class Fabriq {
  private static $cssqueue = array();
  private static $jsqueue = array();
  private static $title;
  private static $render = 'layout';
  private static $layout = 'application';
  private static $controller;
  private static $rendercontroller;
  private static $action;
  private static $renderaction;
  
  /**
   * Adds a stylesheet to the CSS queue for stylesheet includes
   * @param string $stylesheet
   * @param string $media
   * @param string $path
   */
  public function add_css($stylesheet, $media = 'screen', $path = 'public/stylesheets/') {
  	self::$cssqueue[] = array('css' => $stylesheet, 'media' => $media, 'path' => $path);
  }
  
  /**
   * Public getter for $cssqueue
   * @return array
   */
  public function cssqueue() {
    return self::$cssqueue;
  }
  
  /**
   * Adds a JavaScript file to the JavaScript queue for JavaScript includes
   * @param string $javascript
   * @param string $path
   * @param string $ext
   */
  public function add_js($javascript, $path = 'public/javascripts/', $ext = '.js') {
  	self::$jsqueue[] = array('js' => $javascript, 'path' => $path, 'ext' => $ext);
  }
  
  /**
   * Public getter for $jsqueue
   * @return array
   */
  public function jsqueue() {
    return self::$jsqueue;
  }
  
  /**
   * Creates a link to another page in the application
   * @param string $linktext
   * @param string $controller
   * @param string $action
   * @param array $queries
   * @param boolean $blank
   */
  public function link_to($linktext, $controller, $action = NULL, $queries = false, $blank = false, $title = NULL) {
  	global $_FAPP;
  	
    echo "<a href=\"";
  	if (!$_FAPP['cleanurls']) {
		  echo "index.php?q=";
  	} else {
  	  echo $_FAPP['apppath'];
  	}
		echo "{$controller}";
		if ($action != NULL) {
			echo "/{$action}";
		}
		if ($queries != false) {
			foreach($queries as $key => $val) {
				echo "/{$val}";
			}
		}
		echo "\"";
		if ($blank) {
			echo " target=\"_blank\"";
		}
		echo " title=\"";
		if ($title) {
			echo strip_tags($title);
		} else {
		  echo strip_tags($linktext);
		}
		
		echo "\">{$linktext}</a>";
  }
  
  /**
   * Includes the specified model
   * @param string $model
   */
  public function model($model) {
  	require_once("app/models/{$model}.model.php");
  }
  
  /**
   * page title getter/setter
   * if NULL, return the page title
   * @param string $title
   * @return string
   */
  public function title ($title = NULL) {
    if ($title != NULL) {
      self::$title = strip_tags($title);
    } else {
      return self::$title;
    }
  }
  
  /**
   * getter/setter for the $render variable
   * if NULL, return the $render variable
   * @param string $render
   * @return string
   */
  public function render($r = NULL) {
    if ($r != NULL) {
      switch($r) {
        case 'none':
          self::$render = 'none';
          break;
        case 'layout':
          self::$render = 'layout';
          break;
        case 'view': default:
          self::$render = 'view';
          break;
      }
    } else {
      return self::$render;
    }
  }
  
  /**
   * layout file getter/setter
   * if NULL, return the $layout variable
   * @param string $layout
   * @return string
   */
  public function layout($l = NULL) {
    if ($l != NULL) {
      self::$layout = $l;
    } else {
      return self::$layout;
    }
  }
  
  /**
   * Controller getter/setter
   * if NULL, return the $controller variable
   * @param string $c
   * @return string
   */
  public function controller($c = NULL) {
    if ($c != NULL) {
      self::$controller = $c;
    } else {
      return self::$controller;
    }
  }
  
  /**
   * Render controller getter/setter
   * if NULL, return the $rendercontroller variable
   * @param string $controller
   * @return string
   */
  public function render_controller($c = NULL) {
    if ($c != NULL) {
      self::$rendercontroller = $c;
    } else {
      return self::$rendercontroller;
    }
  }
  
  /**
   * Action getter/setter
   * if NULL, return the $action variable
   * @param string $a
   * @return string
   */
  public function action($a = NULL) {
    if ($a != NULL) {
      self::$action = $a;
    } else {
      return self::$action;
    }
  }
  
  /**
   * Render action getter/setter
   * if NULL, return the $renderaction variable
   * @param string $action
   * @return string
   */
  public function render_action($a = NULL) {
  	if ($a != NULL) {
      self::$renderaction = $a;
  	} else {
  	  return self::$renderaction;
  	}
  }
  
  /**
   * Issue a server error
   */
  public function fabriq_error() {
  	Fabriq::render('none');
  	require_once('public/500.html');
  }
  
  /**
   * turn on page javascript include
   */
  public function page_js_on() {
  	Fabriq::add_js(self::$rendercontroller . '.script', 'app/scripts/');
  }
  
  /**
   * Determines whether or not the configuration file has been
   * created yet
   */
  public function installed() {
  	if (!file_exists('config/config.inc.php')) {
  		header("Location: install.php");
  		exit();
  	}
  }
  
  /**
   * Argument getter/setter
   * @param integer $index
   * @param object $val
   * @return object
   */
  public function arg($index, $val = NULL) {
  	global $q;
  	
  	if ($val == NULL) {
    	if (count($q) > $index) {
    		return $q[$index];
    	} else {
    		return FALSE;
    	}
  	} else {
  	  $q[$index] = $val;
  	}
  }
  
  /**
   * getter for the base path for the application
   * @return string
   */
  public function base_path() {
    global $_FAPP;
    
    return $_FAPP['apppath'];
  }
  
  /**
   * Getter for if clean URLs are enabled
   * @return boolean
   */
  public function clean_urls() {
    global $_FAPP;
    
    return $_FAPP['cleanurls'];
  }
  
  /**
   * Getter for string value if clean URLs are enabled
   * @return boolean
   */
  public function clean_urls_str() {
    global $_FAPP;
    
    if ($_FAPP['cleanurls']) {
      return 'true';
    }
    return 'false';
  }
  
  /**
   * Builds a path
   * @return string
   */
  public function build_path() {
    $path = '';
    for ($i = 0; $i < func_num_args(); $i++) {
      $path .= func_get_arg($i);
      if ($i < (func_num_args() - 1)) {
        $path .= '/';
      }
    }
    if (self::clean_urls()) {
      return self::base_path() . $path;
    } else {
      return 'index.php?q=' . $path;
    }
  }
}