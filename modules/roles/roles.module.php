<?php

class roles_module extends FabriqModule {
	public function index() {
		Fabriq::title('Admin | Manage roles');
		
		$roles = new Roles_mm();
		$roles->getRoles();
		FabriqModules::set_var($this->name, 'roles', $roles);
		
		if (isset($_POST['submit'])) {
			for ($i = 0; $i < $roles->count(); $i++) {
				$roles[$i]->enabled = ($_POST['role' . $roles[$i]->id] == 1) ? 1 : 0;
				$roles->update($i);
			}
			Messaging::message('Changes have been saved', 'success');
			FabriqModules::set_var($this->name, 'submitted', true);
		}
	}
	
	public function create() {
		Fabriq::title('Admin | Create role');
		
		if (isset($_POST['submit'])) {
			$role = new Roles_mm();
			$role->role = strtolower(trim($_POST['role']));
			$role->enabled = 1;
			
			if (strlen($role->role) == 0) {
				Messaging::message('You must specify a role');
			}
			$r = new Roles_mm();
			$r->getRole($role->role);
			if ($r->count() > 0) {
				Messaging::message('A role with that name already exists');
			}
			
			if (Messaging::has_messages() == 0) {
				$role->id = $role->create();
			}
			
			FabriqModules::set_var($this->name, 'submitted', true);
			FabriqModules::set_var($this->name, 'role', $role);
		}
	}
	
	public function perms() {
		Fabriq::title('Admin | Manage permissions');
		FabriqModules::add_css('roles', 'roles');
		
		$perms = new Perms();
		$perms->getAll();
		$modules = new Modules();
		$modules->getAll();
		$roles = new Roles_mm();
		$roles->getRoles();
		$permissions = array();
		foreach ($perms as $perm) {
			$permissions[$perm->id] = array();
			foreach($roles as $role) {
				$permissions[$perm->id][$role->id] = 0;
			}
		}
		
		if (isset($_POST['submit'])) {
			foreach ($_POST['permission'] as $permission => $role) {
				foreach ($role as $rkey => $rval) {
					$permissions[$permission][$rkey] = 1;
				}
			}
		}
		
		FabriqModules::set_var($this->name, 'perms', $perms);
		FabriqModules::set_var($this->name, 'modules', $modules);
		FabriqModules::set_var($this->name, 'roles', $roles);
		FabriqModules::set_var($this->name, 'permissions', $permissions);
	}
}	
