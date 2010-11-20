<?php
/**
 * @file PostgreSQL Database connectivity file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
class DatabasepgSQL implements Database {
	// public variables
	public $db;
	public $last;
	public $result;
	public $affected_rows;
	public $insert_id;
	public $num_rows;
	public $total_queries = 0;
	private $error; 
	public $type = 'pgSQL';
	public $delim = '"';
	
	// private variables
	
	/**
	 * Constructor
	 */
	public function __construct($db_info) {
		$this->db = pg_connect("host={$db_info['server']} port=5432 dbname={$db_info['db']} user={$db_info['user']} password={$db_info['pwd']}", PGSQL_CONNECT_FORCE_NEW);
	}
	
	/**
	 * Executes a given query
	 * @param string $sql
	 */
	public function query($sql) {
		$this->last = $sql;
		$this->result = pg_query($this->db, $sql);
		$this->affected_rows = pg_affected_rows($this->result);
		$this->error = pg_last_error($this->db);
		$this->total_queries++;
	}
	
	/**
	 * Prepares and executes a query for create, update, and
	 * delete operations
	 * @param string $sql
	 * @param array $inputs
	 * @return boolean success
	 */
	public function prepare_cud($sql, $inputs, $tableName = NULL) {
		if ($this->result = pg_prepare($this->db, "", $sql)) {
			if (!is_array($inputs)) {
				$inputs = array($inputs);
			}
			$this->result = pg_execute($this->db, "", $inputs);
			
			$this->affected_rows = pg_affected_rows($this->result);
			$this->error = pg_last_error($this->db);
			
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
		if ($this->result = pg_prepare($this->db, "", $sql)) {
			if (!is_array($inputs)) {
				$inputs = array($inputs);
			}
			$this->result = pg_execute($this->db, "", $inputs);
			$results = array();
			if ($attributes == NULL) {
				while ($row = pg_fetch_assoc($this->result)) {
					$r = array();
					foreach ($row as $key => $val) {
						$r[$key] = $val;
					}
					$results[] = $r;
				}
			} else {
				while ($row = pg_fetch_assoc($this->result)) {
					$obj = new stdClass();
					foreach ($row as $key => $val) {
						$obj->$key = $val;
					}
					$results[] = $obj;
				}
			}
			
			$this->num_rows = count($result);
			$this->error = pg_last_error($this->db);
			
			return $results;
		}
		return FALSE;
	}
	
	/**
	 * Escapes string for database call
	 * @param string $str
	 */
	public function escape_string($str) {
		return pg_escape_string($this->db, $str);
	}
	
	/**
	 * Closes the database connection
	 */
	public function close() {
		pg_close($this->db);
	}
	
	/**
	 * Returns the database error string
	 * @return string/boolean
	 */
	public function error() {
		if ($this->error !== NULL) {
			return $this->error;
		}
		return FALSE;
	}
}
