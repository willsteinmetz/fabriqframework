<?php
/**
 * @file Database interface file - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
interface Database {
	/**
	 * Executes a given query
	 * @param string $sql
	 */
	public function query($sql);
	
	/**
	 * Prepares and executes a query for create, update, and
	 * delete operations
	 * @param string $sql
	 * @param array $inputs
	 * @return boolean success
	 */
	public function prepare_cud($sql, $inputs);
	
	/**
	 * Executes a prepared select sql query
	 * @param string $sql
	 * @param array $fields
	 * @param array $inputs
	 * @param array $attributes
	 * @return array/boolean
	 */
	public function prepare_select($sql, $fields, $inputs = array(), $attributes = NULL);
	
	/**
	 * Escapes string for database call
	 * @param string $str
	 */
	public function escape_string($str);
	
	/**
	 * Closes the database connection
	 */
	public function close();
	
	/**
	 * Returns the database error string
	 * @return string/boolean
	 */
	public function error();
}