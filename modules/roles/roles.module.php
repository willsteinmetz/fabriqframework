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
		$modulePerms = new ModulePerms_mm();
		$modulePerms->getAll();
		$permissions = array();
		foreach ($perms as $perm) {
			$permissions[$perm->id] = array();
			foreach($roles as $role) {
				if (isset($modulePerms->perms[$perm->id][$role->id])) {
					$permissions[$perm->id][$role->id] = 1;
				} else {
					$permissions[$perm->id][$role->id] = 0;
				}
			}
		}
		
		if (isset($_POST['submit'])) {
			foreach ($perms as $perm) {
				foreach($roles as $role) {
					if (isset($_POST['permission'][$perm->id][$role->id])) {
						$permissions[$perm->id][$role->id] = 1;
						// add to database if it's not already set
						if (!isset($modulePerms->perms[$perm->id][$role->id])) {
							$p = new ModulePerms_mm();
							$p->permission = $perm->id;
							$p->role = $role->id;
							$p->id = $p->create();
							$modulePerms->perms[$perm->id][$role->id] = $modulePerms->count();
							$modulePerms->add($p);
						}
					} else {
						$permissions[$perm->id][$role->id] = 0;
						// remove from database if it is already set
						if (isset($modulePerms->perms[$perm->id][$role->id])) {
							$p = new ModulePerms_mm($modulePerms[$modulePerms->perms[$perm->id][$role->id]]->id);
							$p->destroy();
							$modulePerms->remove($modulePerms->perms[$perm->id][$role->id]);
							$modulePerms->reindex();
						}
					}
				}
			}
			Messaging::message('Permissions have been updated.', 'success');
		}
		
		FabriqModules::set_var($this->name, 'perms', $perms);
		FabriqModules::set_var($this->name, 'modules', $modules);
		FabriqModules::set_var($this->name, 'roles', $roles);
		FabriqModules::set_var($this->name, 'permissions', $permissions);
	}
}	
