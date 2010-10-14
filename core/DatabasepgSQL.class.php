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
	
	// private variables
	
	/**
	 * Constructor
	 */
	public function __construct($db_info) {
		$this->db = pg_connect("host={$db_info['server']} port=5432 dbname={$db_info['db']} user={$db_info['user']} password={$db_info['pwd']}");
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
