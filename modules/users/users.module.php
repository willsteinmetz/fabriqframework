<?php

class users_module extends Modules {
	public function login() {
		Fabriq::title('Log in');
		
		$configs = new ModuleConfigs();
		$configs->getForModule('users');
		
		if ($configs[$configs->configs['useCustom']]->val == 1) {
			$controller = new $configs[$configs->configs['customController']]->val();
			call_user_func(array($controller, $configs[$configs->configs['customLoginAction']]->val));
		} else {
			FabriqModules::add_css('users', 'users');
			if (isset($_POST['submit'])) {
				if (trim($_POST['user']) != '') {
					$user = new Users_mm();
					$user->getByDisplayEmail($_POST['user']);
					if ($user->count() == 1) {
						if ($user->banned == 0) {
							if (crypt($_POST['pwd'], $user->id . substr($user->display, 0, 5)) == $user->encpwd) {
								$_SESSION['FABMOD_USERS_displayname'] = $user->displayname;
								$_SESSION['FABMOD_USERS_email'] = $user->email;
								$_SESSION['FABMOD_USERS_userid'] = $user->id;
								$roles = new UserRoles_mm();
								$roles->getRoles($user->id);
								$r = array();
								foreach ($roles as $role) {
									$r[] = $role->role;
									$r[] = $role->roleName;
								}
								$_SESSION['FABMOD_USERS_roles'] = serialize($r);
							} else {
								Messaging::message('Display name/e-mail address or password incorrect');
							}
						} else {
							Messaging::message('User has been banned');
						}
					} else {
						Messaging::message('Display name/e-mail address could not be found');
					}
				} else {
					Messaging::message('You must provide a display name or e-mail address');
				}
			}
		}
	}
	
	public function logout() {
		$configs = new ModuleConfigs();
		$configs->getForModule('users');
		
		if ($configs[$configs->configs['useCustom']]->val == 1) {
			$controller = new $configs[$configs->configs['customController']]->val();
			call_user_func(array($controller, $configs[$configs->configs['customLogoutAction']]->val));
		} else {
			unset($_SESSION['FABMOD_USERS_displayname']);
			unset($_SESSION['FABMOD_USERS_email']);
			unset($_SESSION['FABMOD_USERS_userid']);
			unset($_SESSION['FABMOD_USERS_roles']);
			header("Location: " . PathMap::build_path('users', 'login'));
		}
	}
	
	public function create() {
		
	}
	
	public function update() {
		
	}
	
	public function enable() {
		
	}
	
	public function ban() {
		
	}
	
	public function forgotpassword() {
		
	}
	
	public function register() {
		
	}
	
	public function isLoggedIn() {
		$configs = new ModuleConfigs();
		$configs->getForModule('users');
		
		if ($configs[$configs->configs['useCustom']]->val == 1) {
			$controller = new $configs[$configs->configs['customController']]->val();
			call_user_func(array($controller, $configs[$configs->configs['customIsLoggedInAction']]->val));
		} else {
			if (isset($_SESSION['FABMOD_USERS_displayname']) && ($_SESSION['FABMOD_USERS_displayname'] != '')) {
				return TRUE;
			}
			return FALSE;
		}
	}
	
	public function hasRole($role) {
		if (!$this->isLoggedIn()) {
			$this->login();
		}
		
		if (isset($_SESSION['FABMOD_USERS_roles'])) {
			$roles = unserialize($_SESSION['FABMOD_USERS_roles']);
			if (count($roles) > 0) {
				if (in_array($role, $roles)) {
					return TRUE;
				}
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
}
	