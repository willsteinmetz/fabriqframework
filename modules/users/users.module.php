<?php

class users_module extends FabriqModule {
	function __construct() {
		parent::__construct();
		require_once('modules/users/models/Users.model.php');
	}
	
	public function index() {
		if (FabriqModules::module('roles')->requiresPermission('administer users', $this->name)) {
			$page = (PathMap::arg(2)) ? PathMap::arg(2) : 1;
			$users = FabriqModules::new_model('users', 'Users');
			$users->getList($page);
			
			for ($i = 0; $i < $users->count(); $i++) {
				$users[$i]->encpwd = NULL;
			}
			
			Fabriq::title('Manage users');
			Fabriq::fabriq_ui_on();
			FabriqModules::add_js('users', 'jquery.validate.min');
			FabriqLibs::js_lib('handlebars', 'handlebars');
			FabriqModules::add_js('users', 'users-index');
			FabriqModules::add_css('users', 'users-admin');
			FabriqModules::set_var('users', 'users', $users);
		}
	}
	
	public function login() {
		if ($this->isLoggedIn()) {
			global $_FAPP;
			header('Location:' . PathMap::build_path($_FAPP['cdefault'], $_FAPP['adefault']));
			exit();
		}
		
		Fabriq::title('Log in');
		
		$configs = new ModuleConfigs();
		$configs->getForModule('users');
		
		FabriqModules::add_css('users', 'users');
		if (isset($_POST['submit'])) {
			if (trim($_POST['user']) != '') {
				$user = FabriqModules::new_model('users', 'Users');
				$user->getByDisplayEmail($_POST['user']);
				if ($user->count() == 1) {
					if ($user->banned == 0) {
						if (crypt($_POST['pwd'], $user->id) == $user->encpwd) {
							$_SESSION['FABMOD_USERS_displayname'] = $user->display;
							$_SESSION['FABMOD_USERS_email'] = $user->email;
							$_SESSION['FABMOD_USERS_userid'] = $user->id;
							if ($user->forcepwdreset == 1) {
								$_SESSION['FABMOD_USERS_forcepwdreset'] = 1;
							}
							$roles = FabriqModules::new_model('users', 'UserRoles');
							$roles->getRoles($user->id);
							$r = array();
							foreach ($roles as $role) {
								$r[] = $role->role;
								$r[] = $role->roleName;
							}
							$authenticated = FabriqModules::new_model('roles', 'Roles');
							$authenticated->getRole('authenticated');
							$r[] = $authenticated->id;
							$r[] = $authenticated->role;
							$_SESSION['FABMOD_USERS_roles'] = serialize($r);
							if (isset($_POST['return_to'])) {
								$path = explode('/', $_POST['return_to']);
								if ($user->forcepwdreset == 1) {
									array_unshift($path, 'users', 'changePassword');
								}
								header('Location:' . call_user_func_array('PathMap::build_path', $path));
								exit();
							} else {
								$path = array('users', 'myAccount');
								if ($user->forcepwdreset == 1) {
									array_unshift($path, 'users', 'changePassword');
								}
								header('Location:' . call_user_func_array('PathMap::build_path', $path));
								exit();
							}
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
			FabriqModules::set_var('users', 'submitted', true);
		}
		FabriqModules::set_var('users', 'anyoneCanRegister', $configs[$configs->configs['anyoneCanRegister']]->val);
	}
	
	public function logout() {
		unset($_SESSION['FABMOD_USERS_displayname']);
		unset($_SESSION['FABMOD_USERS_email']);
		unset($_SESSION['FABMOD_USERS_userid']);
		unset($_SESSION['FABMOD_USERS_roles']);
		unset($_SESSION['FABMOD_USERS_forcepwdreset']);
		header("Location: " . PathMap::build_path('users', 'login'));
		exit();
	}
	
	public function create() {
		Fabriq::render('none');
		
		if (FabriqModules::module('roles')->requiresPermission('administer users', $this->name)) {
			$emailPattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
			$displayPattern = '/([A-z0-9]){6,24}/';
			$user = FabriqModules::new_model('users', 'Users');
			$user->display = $_POST['display'];
			$user->email = $_POST['email'];
			$user->encpwd = $_POST['pwd'];
			$errors = array();
			$u = null;
			
			if (!preg_match($displayPattern, $user->display)) {
				$errors[] = "Display name is invalid";
			}
			if (!preg_match($emailPattern, $user->email)) {
				$errors[] = "e-mail address is invalid";
			}
			if ((strlen($user->encpwd) < 8) || ($user->encpwd == $user->display) || ($user->encpwd == $user->email)) {
				$errors[] = "Password is invalid";
			}
			
			if (count($errors) == 0) {
				$user->status = 1;
				$user->banned = 0;
				$user->forcepwdreset = 1;
				$user->id = $user->create();
				$user->encpwd = crypt($user->encpwd, $user->id);
				$user->update();
				
				// get add user roles
				$r = FabriqModules::new_model('roles', 'Roles');
				$r->getAll();
				for ($i = 0; $i < $r->count(); $i++) {
					if ($_POST['role' . $r[$i]->id] == 1) {
						$userRole = FabriqModules::new_model('users', 'UserRoles');
						$userRole->user = $user->id;
						$userRole->role = $r[$i]->id;
						$userRole->create();
					}
				}
				
				$u = new stdClass();
				$u->display = $user->display;
				$u->email = $user->email;
				$u->id = $user->id;
				
				if ($_POST['emailUser']) {
					global $_FAPP;
					$url = $_FAPP['url'] . PathMap::build_path('users', 'login');
					$message = <<<EMAIL
Hello {$user->display},

An account has been created for you on the {$_FAPP['title']} website.

Your account details are below:

Display name: {$user->display}
Temporary password: {$_POST['pwd']}

You will be prompted to change your password the next time you log in. You can log in by navigating to {$url} in your browser.

Thanks,
The {$_FAPP['title']} team


NOTE: Do not reply to this message. It was automatically generated.
EMAIL;
					mail(
						$user->email,
						"An account has been created for you at {$_FAPP['title']}",
						$message,
						'From: noreply@' . str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $_FAPP['url'])))
					);
				}
				$msg = "User added";
				$success = true;
			} else {
				$msg = "User could not be added";
				$success = false;
			}
			$notLoggedIn = false;
		} else {
			$success = false;
			$msg = 'User not logged in';
			$errors = null;
			$u = null;
			$notLoggedIn = true;
		}
		
		header('Content-type:application/json');
		echo json_encode(array('success' => $success, 'msg' => $msg, 'errors' => $errors, 'user' => $u, 'notLoggedIn' => $notLoggedIn));
	}
	
	public function update() {
		Fabriq::render('none');
		
		if (FabriqModules::module('roles')->requiresPermission('administer users', $this->name)) {
			$user = FabriqModules::new_model('users', 'Users');
			$user->find(PathMap::arg(2));
			$u = null;
			$errors = array();
			$roles = null;
			if ($user->display != '') {
				$ur = FabriqModules::new_model('users', 'UserRoles');
				$ur->getRoles($user->id);
				$uroles = array();
				for ($i = 0; $i < $ur->count(); $i++) {
					$uroles[] = $ur[$i]->role;
				}
				$r = FabriqModules::new_model('roles', 'Roles');
				$r->getAll();
				$roles = array();
				for ($i = 0; $i < $r->count(); $i++) {
					if (($r[$i]->role != 'unauthenticated') && ($r[$i]->role != 'authenticated')) {
						$roles[] = $r[$i];
					}
				}
				if (isset($_POST['submit'])) {
					$user->display = $_POST['display'];
					$user->email = $_POST['email'];
					$emailPattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
					$displayPattern = '/([A-z0-9]){6,24}/';
					
					if (!preg_match($emailPattern, $_POST['email'])) {
						$errors[] = "email";
					}
					if (!preg_match($displayPattern, $_POST['display'])) {
						$errors[] = "display";
					}
					
					if (count($errors) == 0) {
						// update roles
						$toAdd = array();
						$toRemove = array();
						for ($i = 0; $i < count($roles); $i++) {
							if ($_POST['role' . $roles[$i]->id] == 1) {
								if (!array_key_exists($roles[$i]->id, $ur->roles)) {
									$toAdd[] = $roles[$i]->id;
								}
							} else {
								if (array_key_exists($roles[$i]->id, $ur->roles)) {
									$toRemove[] = $roles[$i]->id;
								}
							}
						}
						
						// add new role assignments
						for ($i = 0; $i < count($toAdd); $i++) {
							$ur = FabriqModules::new_model('users', 'UserRoles');
							$ur->user = $user->id;
							$ur->role = $toAdd[$i];
							$ur->create();
						}

						// remove unneeded role assignments
						for ($i = 0; $i < count($toRemove); $i++) {
							$ur = FabriqModules::new_model('users', 'UserRoles');
							$ur->getRole($user->id, $toRemove[$i]);
							$ur->destroy();
						}

						// refresh user roles
						$uroles = FabriqModules::new_model('users', 'UserRoles');
						$uroles->getRoles($user->id);
						
						$user->update();
						$msg = "User updated";
						$success = true;
					} else {
						$msg = "User could not be updated because of errors";
						$success = false;
					}
					
					$u = new stdClass();
					$u->display = $_POST['display'];
					$u->email = $_POST['email'];
				} else {
					$msg = "User found";
					$u = new stdClass();
					$u->display = $user->display;
					$u->email = $user->email;
					$success = true;
				}
				$u->id = $user->id;
				$u->roles = $uroles;
			} else {
				$success = false;
				$msg = "User could not be found";
			}
			$notLoggedIn = false;
		} else {
			$success = false;
			$msg = 'User not logged in';
			$errors = null;
			$u = null;
			$notLoggedIn = true;
		}

		header('Content-type:application/json');
		echo json_encode(array(
			'success' => $success,
			'msg' => $msg,
			'user' => $u,
			'errors' => $error,
			'notLoggedIn' => $notLoggedIn,
			'roles' => $roles
		));
	}
	
	public function enable() {
		Fabriq::render('none');
		
		if (FabriqModules::module('roles')->requiresPermission('administer users', $this->name)) {
			if ($_POST['user'] != $_SESSION['FABMOD_USERS_userid']) {
				$user = FabriqModules::new_model('users', 'Users');
				$user->find($_POST['user']);
				if ($user->display != '') {
					$user->enable();
					if ($user->banned == 0) {
						$success = true;
					} else {
						$success = false;
					}
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
			$notLoggedIn = false;
		} else {
			$success = false;
			$notLoggedIn = true;
		}
		
		header('Content-type:application/json');
		echo json_encode(array('success' => $success, 'notLoggedIn' => $notLoggedIn));
	}
	
	public function ban() {
		Fabriq::render('none');
		
		if (FabriqModules::module('roles')->requiresPermission('administer users', $this->name)) {
			if ($_POST['user'] != $_SESSION['FABMOD_USERS_userid']) {
				$user = FabriqModules::new_model('users', 'Users');
				$user->find($_POST['user']);
				if ($user->display != '') {
					$user->ban();
					if ($user->banned == 1) {
						$success = true;
					} else {
						$success = false;
					}
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}
			$notLoggedIn = false;
		} else {
			$success = false;
			$notLoggedIn = true;
		}
		
		header('Content-type:application/json');
		echo json_encode(array('success' => $success, 'notLoggedIn' => $notLoggedIn));
	}
	
	public function forgotpassword() {
		if ($this->isLoggedIn()) {
			global $_FAPP;
			header('Location:' . PathMap::build_path($_FAPP['cdefault'], $_FAPP['adefault']));
			exit();
		}
		
		Fabriq::title('Reset my password');
		
		if (isset($_POST['submit'])) {
			if (trim($_POST['user']) != '') {
				$user = FabriqModules::new_model('users', 'Users');
				$user->getByDisplayEmail($_POST['user']);
				if ($user->count() == 1) {
					if ($user->banned == 0) {
						global $_FAPP;
						$str = '';
						for ($i = 0; $i < 8; $i++) {
							$str .= chr(rand(97, 122));
						}
						$user->encpwd = crypt($str, $user->id);
						$user->forcepwdreset = 1;
						$user->update();
						$url = $_FAPP['url'] . PathMap::build_path('users', 'login');
						$message = <<<EMAIL
Hello {$user->display},

Your password for {$_FAPP['title']} has been reset.

Display name: {$user->display}
Temporary password: {$str}

You will be prompted to change your password the next time you log in. You can log in by navigating to {$url} in your browser.

Thanks,
The {$_FAPP['title']} team


NOTE: Do not reply to this message. It was automatically generated.
EMAIL;
						mail(
							$user->email,
							"Your password for {$_FAPP['title']}",
							$message,
							'From: noreply@' . str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $_FAPP['url'])))
						);
						FabriqModules::set_var('users', 'submitted', true);
					} else {
						Messaging::message('User has been banned');
					}
				} else {
					Messaging::message('Display name/e-mail address could not be found');
				}
			} else {
				Messaging::message('You must provide a display name or e-mail address');
			}
			FabriqModules::set_var('users', 'submitted', true);
		}
	}
	
	public function register() {
		if ($this->isLoggedIn()) {
			header("Location: " . PathMap::build_path('users', 'myAccount'));
			exit();
		}
		
		$configs = new ModuleConfigs();
		$configs->getForModule('users');
		
		if ($configs[$configs->configs['anyoneCanRegister']]->val == 0) {
			FabriqModules::module('roles')->noPermission();
			FabriqModules::render('roles', 'noPermission');
			FabriqModules::has_permission(false);
		} else {
			Fabriq::title('Register');
			if (isset($_POST['submit'])) {
				$emailPattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
				$displayPattern = '/([A-z0-9]){6,24}/';
				$user = FabriqModules::new_model('users', 'Users');
				$user->display = $_POST['display'];
				$user->email = $_POST['email'];
				$user->encpwd = $_POST['pwd'];
				
				if (!preg_match($displayPattern, $user->display)) {
					Messaging::message("Display name is invalid");
				}
				if (!preg_match($emailPattern, $user->email)) {
					Messaging::message("e-mail address is invalid");
				}
				if ((strlen($user->encpwd) < 8) || ($user->encpwd == $user->display) || ($user->encpwd == $user->email)) {
					Messaging::message("Password is invalid");
				}
				
				if (!Messaging::has_messages()) {
					$user->status = 1;
					$user->banned = 0;
					$user->forcepwdreset = 0;
					$user->id = $user->create();
					$user->encpwd = crypt($user->encpwd, $user->id);
					$user->update();
					
					global $_FAPP;
					$url = $_FAPP['url'] . PathMap::build_path('users', 'login');
					$message = <<<EMAIL
Hello {$user->display},

Your account has been created on the {$_FAPP['title']} website.

You can log in by navigating to {$url} in your browser.

Thanks,
The {$_FAPP['title']} team


NOTE: Do not reply to this message. It was automatically generated.
EMAIL;
					mail(
						$user->email,
						"Your account at {$_FAPP['title']}",
						$message,
						'From: noreply@' . str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $_FAPP['url'])))
					);
				}
				
				FabriqModules::set_var('users', 'submitted', true);
			} else {
				FabriqModules::add_js('users', 'jquery.validate.min');
				FabriqModules::add_js('users', 'users-register');
				FabriqModules::add_css('users', 'users-admin');
			}
		}
	}
	
	public function changePassword() {
		if (!$this->isLoggedIn()) {
			header("Location: " . PathMap::build_path('users', 'login'));
			exit();
		}
		
		Fabriq::title('Change password');
		
		$user = FabriqModules::new_model('users', 'Users');
		$user->find($_SESSION['FABMOD_USERS_userid']);
		
		if ($user->forcepwdreset == 1) {
			Messaging::message('You must change your password before you can continue', 'warning');
		}
		
		if (isset($_POST['submit'])) {
			if ($user->encpwd != crypt($_POST['currpwd'], $user->id)) {
				Messaging::message('Current password is incorrect');
			}
			if ((strlen($_POST['newpwd']) < 8) || ($_POST['currpwd'] == $user->display) || ($_POST['currpwd'] == $user->email)) {
				Messaging::message('New password is invalid');
			}
			if ($_POST['newpwd'] != $_POST['comfnewpwd']) {
				Messaging::message('New password and confirmation do not match');
			}

			if (!Messaging::has_messages()) {
				$user->encpwd = crypt($_POST['newpwd'], $user->id);
				$user->forcepwdreset = 0;
				$user->update();
				$_SESSION['FABMOD_USERS_forcepwdreset'] = null;
				unset($_SESSION['FABMOD_USERS_forcepwdreset']);
				if (isset($_POST['return_to'])) {
					header('Location:' . call_user_func_array('PathMap::build_path', explode('/', $_POST['return_to'])));
					exit();
				} else {
					Messaging::message('Password has been changed', 'success');
				}
			}
			
			FabriqModules::set_var('users', 'submitted', true);
		}
	}
	
	public function myAccount() {
		if (!$this->isLoggedIn()) {
			header('Location: ' . PathMap::build_path('users', 'login'));
			exit();
		}
		if (isset($_POST['isAjax'])) {
			Fabriq::render('view');
		} else {
			Fabriq::title('My account');
		}
	}
	
	public function updateAccount() {
		if (!$this->isLoggedIn()) {
			header('Location: ' . PathMap::build_path('users', 'login'));
			exit();
		}
		Fabriq::title('Update account');
		
		$user = FabriqModules::new_model('users', 'Users');
		$user->find($_SESSION['FABMOD_USERS_userid']);
		
		if (isset($_POST['submit'])) {
			$emailPattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
			$displayPattern = '/([A-z0-9]){6,24}/';
			
			if (!preg_match($displayPattern, $_POST['display'])) {
				Messaging::message("Display name is invalid");
			}
			if (!preg_match($emailPattern, $_POST['email'])) {
				Messaging::message("e-mail address is invalid");
			}
			
			if (!Messaging::has_messages()) {
				$user->display = $_POST['display'];
				$user->email = $_POST['email'];
				$user->update();
				
				$_SESSION['FABMOD_USERS_displayname'] = $user->display;
				$_SESSION['FABMOD_USERS_email'] = $user->email;
				
				Messaging::message('Account has bee updated', 'success');
			}
			
			FabriqModules::set_var('users', 'submitted', true);
		}
		
		FabriqModules::set_var('users', 'user', $user);
		FabriqModules::add_js('users', 'jquery.validate.min');
		FabriqModules::add_js('users', 'users-updateAccount');
		FabriqModules::add_css('users', 'users-admin');
	}
	
	public function isLoggedIn() {
		if (isset($_SESSION['FABMOD_USERS_displayname']) && ($_SESSION['FABMOD_USERS_displayname'] != '')) {
			return TRUE;
		}
		return FALSE;
	}
	
	public function checkDisplay() {
		Fabriq::render('none');
		
		header('Content-type:application/json');
		if (isset($_POST['user'])) {
			echo json_encode(array('exists' => users_Users::displayExists($_POST['display'], $_POST['user'])));
		} else {
			echo json_encode(array('exists' => users_Users::displayExists($_POST['display'])));
		}
	}
	
	public function checkEmail() {
		Fabriq::render('none');
		
		header('Content-type:application/json');
		if (isset($_POST['user'])) {
			echo json_encode(array('exists' => users_Users::emailExists($_POST['email'], $_POST['user'])));
		} else {
			echo json_encode(array('exists' => users_Users::emailExists($_POST['email'])));
		}
	}

	public function getRoles() {
		Fabriq::render('none');
		
		header('Content-type:application/json');
		if (FabriqModules::module('roles')->requiresPermission('administer users', $this->name)) {
			$r = FabriqModules::new_model('roles', 'Roles');
			$r->getAll();
			$roles = array();
			for ($i = 0; $i < $r->count(); $i++) {
				if (($r[$i]->role != 'unauthenticated') && ($r[$i]->role != 'authenticated')) {
					$roles[] = $r[$i];
				}
			}
			echo json_encode(array('roles' => $roles));
		} else {
			echo json_encode(array('notLoggedIn' => true));
		}
	}
}
	