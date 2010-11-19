<?php

class Modules extends Model {
	function __construct($id = NULL) {
		parent::__construct(array('module', 'enabled'), 'fabmods_modules');
		if ($id != NULL) {
			$this->find($id);
		}
	}
}
	