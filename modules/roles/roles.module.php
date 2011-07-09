<?php

class roles_module extends FabriqModule {
	public function index() {
		if ($this->requiresPermission('manage roles', $this->name)) {
			Fabriq::title('Admin | Manage roles');
			
			$roles = FabriqModules::new_model('roles', 'Roles');
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
	}
	
	public function create() {
		if ($this->requiresPermission('create roles', $this->name)) {
			Fabriq::title('Admin | Create role');
			
			if (isset($_POST['submit'])) {
				$role = FabriqModules::new_model('roles', 'Roles');
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
	}
	
	public function perms() {
		if ($this->requiresPermission('manage roles', $this->name)) {
			Fabriq::title('Admin | Manage permissions');
			FabriqModules::add_css('roles', 'roles');
			
			$perms = new Perms();
			$perms->getAll();
			$modules = new Modules();
			$modules->getEnabled();
			$roles = FabriqModules::new_model('roles', 'Roles');
			$roles->getRoles();
			$modulePerms = FabriqModules::new_model('roles', 'ModulePerms');
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
								$p = FabriqModules::new_model('roles', 'ModulePerms');
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
								$p = FabriqModules::new_model('roles', 'ModulePerms');
								$p->find($modulePerms[$modulePerms->perms[$perm->id][$role->id]]->id);
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

	public function hasRole($role) {
		if (isset($_SESSION['FABMOD_USERS_roles'])) {
			$roles = unserialize($_SESSION['FABMOD_USERS_roles']);
			if (count($roles) > 0) {
				if (in_array($role, $roles)) {
					return TRUE;
				}
				$this->noPermission();
				FabriqModules::render('roles', 'noPermission');
				FabriqModules::has_permission(false);
				return FALSE;
			}
			$this->noPermission();
			FabriqModules::render('roles', 'noPermission');
			FabriqModules::has_permission(false);
			return FALSE;
		}
		// user isn't logged in
		if (Fabriq::render() != 'none') {
			FabriqModules::module('users')->login();
			FabriqModules::render('users', 'login');
			FabriqModules::has_permission(false);
		}
		return FALSE;
	}
	
	public function requiresPermission($permission, $module) {
		if (isset($_SESSION['FABMOD_USERS_roles'])) {
			$roles = unserialize($_SESSION['FABMOD_USERS_roles']);
			if (count($roles) > 0) {
				global $db;
				
				$query = "SELECT COUNT( * ) AS num
FROM fabmod_roles_moduleperms
WHERE permission = (
	SELECT id
	FROM fabmods_perms
	WHERE permission = ?
	AND module = (
		SELECT id
		FROM fabmods_modules
		WHERE module = ?
		LIMIT 1
	)
	LIMIT 1
)
AND role
IN (" . $db->qmarks(count($roles)) . ")";
				$data = $db->prepare_select($query, array('num'), array_merge(array($permission, $module), $roles));
				if ($data[0]['num'] > 0) {
					return TRUE;
				}
				$this->noPermission();
				FabriqModules::render('roles', 'noPermission');
				FabriqModules::has_permission(false);
				return FALSE;
			}
			$this->noPermission();
			FabriqModules::render('roles', 'noPermission');
			FabriqModules::has_permission(false);
			return FALSE;
		}
		// user isn't logged in
		if (Fabriq::render() != 'none') {
			FabriqModules::module('users')->login();
			FabriqModules::render('users', 'login');
			FabriqModules::has_permission(false);
		}
		return FALSE;
	}

	public function userHasPermission($permission, $module) {
		if (isset($_SESSION['FABMOD_USERS_roles'])) {
			$roles = unserialize($_SESSION['FABMOD_USERS_roles']);
			if (count($roles) > 0) {
				global $db;
				
				$query = "SELECT COUNT( * ) AS num
FROM fabmod_roles_moduleperms
WHERE permission = (
	SELECT id
	FROM fabmods_perms
	WHERE permission = ?
	AND module = (
		SELECT id
		FROM fabmods_modules
		WHERE module = ?
		LIMIT 1
	)
	LIMIT 1
)
AND role
IN (" . $db->qmarks(count($roles)) . ")";
				$data = $db->prepare_select($query, array('num'), array_merge(array($permission, $module), $roles));
				if ($data[0]['num'] > 0) {
					return TRUE;
				}
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function noPermission() {
		global $_FAPP;
		Fabriq::title('Access denied');
		
		FabriqModules::set_var('roles', 'controller', $_FAPP['cdefault']);
		FabriqModules::set_var('roles', 'action', $_FAPP['adefault']);
	}
}	
