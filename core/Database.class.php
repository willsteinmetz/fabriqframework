<?php
/**
 * @file MySQL Database connectivity file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
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
	private $errorNo;
	private $errorStr;
	
	// private variables
	
	/**
	 * Constructor
	 */
	public function __construct($db_info) {
		$this->db = new mysqli($db_info['server'], $db_info['user'], $db_info['pwd'], $db_info['db']) or die ("A database error occurred. Please contact the administrator.");
	}
	
	/**
	 * Executes a given query
	 * @param string $sql
	 */
	public function query($sql) {
		$this->last = $sql;
		$this->result = $this->db->query($sql) or die (mysqli_error() . "<br />-----<br />$sql");
		$this->affected_rows = $this->db->affected_rows;
		$this->errorNo = $this->db->errno;
		$this->errorStr = $this->db->error;
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
		$stmt = $this->db->stmt_init();
		if ($stmt->prepare($sql)) {
			$types = '';
			if (!is_array($inputs)) {
				$inputs = array($inputs);
			}
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
			$this->errorNo = $stmt->errno;
			$this->errorStr = $stmt->error;
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
		$stmt = $this->db->stmt_init();
		
		if ($stmt->prepare($sql)) {
			if (!is_array($inputs)) {
				$inputs = array($inputs);
			}
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
					foreach ($result as $key => $val) {
						$r[$key] = $val;
					}
					$results[] = $r;
				}
			} else {
				while ($stmt->fetch()) {
					$obj = new stdClass();
					foreach ($result as $key => $val) {
						$obj->$key = $val;
					}
					$results[] = $obj;
				}
			}
			
			$this->num_rows = $stmt->num_rows;
			$this->errorNo = $stmt->errno;
			$this->errorStr = $stmt->error;
			
			return $results;
		}
		return FALSE;
	}
	
	/**
	 * Escapes string for database call
	 * @param string $str
	 */
	public function escape_string($str) {
		return $this->db->real_escape_string($str);
	}
	
	/**
	 * Builds WHERE clause pieces for queries
	 * @param string $field
	 * @param string $operator
	 * @param string $test
	 * @param string $next
	 */
	// DEPRECATED - will be removed in version 2.0 RC
	public function where($field, $operator, $test, $next = NULL) {
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
	// DEPRECATED - will be removed in version 2.0 RC
	public function where_prepared($field, $operator, $next = NULL) {
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
		$this->db->close();
	}
	
	/**
	 * Returns the database error number
	 * @return integer/boolean
	 */
	public function errno() {
		if ($this->errorNo !== NULL) {
			return $this->errorNo;
		}
		return FALSE;
	}
	
	/**
	 * Returns the database error string
	 * @return string/boolean
	 */
	public function error() {
		if ($this->errorStr !== NULL) {
			return $this->errorStr;
		}
		return FALSE;
	}
	
	/**
	 * Returns string version of the database error with the error number and string
	 * @return string
	 */
	public function error_str() {
		return $this->errorNo . ': ' . $this->errorStr;
	}
	
	/**
	 * Builds a question mark string to be used for prepared statements
	 * @param int $num
	 * @return string
	 */
	public function qmarks($num) {
		$qmarks = '';
		for ($i = 0; $i < $num; $i++) {
			$qmarks .= '?';
			if ($i != ($num - 1)) {
				$qmarks .= ', ';
			}
		}
		
		return $qmarks;
	}
}