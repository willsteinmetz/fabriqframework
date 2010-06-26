<?php
/**
 * @file Database connectivity file
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
class Database {
	// public variables
	public $db;
	public $last;
	public $result;
	public $affected_rows;
	public $insert_id;
	public $num_rows;
	public $total_queries = 0;
	
	// private variables
	
	/**
	 * Constructor
	 */
	public function __construct($db_info) {
	  // mysql
	  $this->db = new mysqli($db_info['server'], $db_info['user'], $db_info['pwd'], $db_info['db']) or die ("A database error occurred. Please contact the administrator.");
	}
	
	/**
	 * Executes a given query
	 * @param string $sql
	 */
	public function query($sql) {
	  // mysql
		$this->last = $sql;
		$this->result = $this->db->query($sql) or die (mysqli_error() . "<br />-----<br />$sql");
		$this->affected_rows = $this->db->affected_rows;
		$this->total_queries++;
	}
	
	/**
	 * Prepares and executes a query for create, update, and
	 * delete operations
	 * @param string $sql
	 * @param array $inputs
	 * @return boolean success
	 */
	public function prepare_cud($sql, $inputs) {
	  // mysql
	  $stmt = $this->db->stmt_init();
	  if ($stmt->prepare($sql)) {
	    $types = '';
      foreach ($inputs as $input) {
        if (is_int($input)) {
          $types .= 'i';
        } else if (is_float($input)) {
          $types .= 'd';
        } else {
          $types .= 's';
        }
      }
      $arg = array_merge(array($types), $inputs);
      // fix for call_user_func_array() taking both reference and value to
      // force it to be reference
      $args = array();
      foreach ($arg as $key => &$a) {
        $args[$key] = &$a;
      }
      call_user_func_array(array($stmt, "bind_param"), $args);
	    
	    $stmt->execute();
	    $this->affected_rows = $stmt->affected_rows;
	    $this->insert_id = $stmt->insert_id;
	    $stmt->close();
	    
	    return TRUE;
	  }
	  return FALSE;
	}
	
	/**
	 * Executes a prepared select sql query
	 * @param string $sql
	 * @param array $fields
	 * @param array $inputs
	 * @param array $attributes
	 * @return array/boolean
	 */
	public function prepare_select($sql, $fields, $inputs = array(), $attributes = NULL) {
	  // mysql
	  $stmt = $this->db->stmt_init();
	  
	  if ($stmt->prepare($sql)) {
	    if (count($inputs) > 0) {
  	    $types = '';
        foreach ($inputs as $input) {
          if (is_int($input)) {
            $types .= 'i';
          } else if (is_float($input)) {
            $types .= 'd';
          } else {
            $types .= 's';
          }
        }
        $arg2 = array_merge(array($types), $inputs);
        // fix for call_user_func_array() taking both reference and value to
        // force it to be reference
        $args = array();
        foreach ($arg2 as $key => &$a) {
          $args[$key] = &$a;
        }
        call_user_func_array(array($stmt, "bind_param"), $args);
	    }
	    
      $stmt->execute();
      $stmt->store_result();

	    $cols = $stmt->field_count;

	    $result = array();
	    $arg = array();
	    for ($i = 0; $i < $cols; $i++) {
	      $result[$fields[$i]] = '';
	      $arg[$i] = &$result[$fields[$i]];
	    }
	    
	    call_user_func_array(array($stmt, "bind_result"), $arg);

	    $results = array();
  	  if ($attributes == NULL) {
  	    while ($stmt->fetch()) {
  	      $r = array();
  	      for ($i = 0; $i < $cols; $i++) {
  	      	$r[$fields[$i]] = $result[$fields[$i]];
  	      }
  	      $results[] = $r;
  	    }
	    } else {
        while ($stmt->fetch()) {
          $obj = new stdClass();
          for ($i = 0; $i < $cols; $i++) {
            $obj->{$fields[$i]} = $result[$fields[$i]];
          }
          $results[] = $obj;
        }
	    }
	    
	    $this->num_rows = $stmt->num_rows;
	    
	    return $results;
	  }
	  return FALSE;
	}
	
	/**
	 * Escapes string for database call
	 * @param string $str
	 */
	public function escape_string($str) {
	  // mysql
	  return $this->db->real_escape_string($str);
	}
	
	/**
	 * Builds WHERE clause pieces for queries
	 * @param string $field
	 * @param string $operator
	 * @param string $test
	 * @param string $next
	 */
	public function where($field, $operator, $test, $next = NULL) {
	  // mysql
	  if ($next == NULL) {
      return $this->escape_string(trim($field)) . ' ' . $this->escape_string(trim($operator)) . " '" . $this->escape_string(trim($test)) . "' ";
	  } else {
	    return $this->escape_string(trim($field)) . ' ' . $this->escape_string(trim($operator)) . " '" . $this->escape_string(trim($test)) . "' " . $this->escape_string(trim($next));
	  }
	}
	
  /**
   * Builds WHERE clause pieces for queries that are used in
   * prepared statements
   * @param string $field
   * @param string $operator
   * @param string $next
   */
  public function where_prepared($field, $operator, $next = NULL) {
    // mysql
    if ($next == NULL) {
      return $this->escape_string(trim($field)) . ' ' . $this->escape_string(trim($operator)) . " ? ";
    } else {
      return $this->escape_string(trim($field)) . ' ' . $this->escape_string(trim($operator)) . " ? " . $this->escape_string(trim($next));
    }
  }
	
	/**
	 * Closes the database connection
	 */
	public function close() {
	  // mysql
		$this->db->close();
	}
}