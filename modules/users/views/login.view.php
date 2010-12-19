<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
?>
<fieldset id="users-login">
	<legend>Log in</legend>
<?php
$path = $_GET['q'];
$path = str_replace('users/login/', '', $path);
if ($path != 'users/login'): ?>
	<form method="post" action="<?php echo call_user_func_array('PathMap::build_path', array_merge(array('users', 'login'), explode('/', $path))); ?>">
		<input type="hidden" id="return_to" name="return_to" value="<?php echo $path; ?>" />
<?php else: ?>
	<form method="post" action="<?php echo PathMap::build_path('users', 'login'); ?>">
<?php endif; ?>
		<div style="padding: 2px;">
			<label for="user">
				User: <input type="text" size="50" maxlength="100" id="user" name="user" />
			</label>
		</div>
		<div style="padding: 2px;">
			<label for "pwd">
				Password: <input type="password" size="50" maxlength="100" id="pwd" name="pwd" />
			</label>
		</div>
		<div style="padding: 2px;">
			<input type="submit" name="submit" id="submit" value="Log in" />
		</div>
		<div style="padding: 2px;">
			<a href="<?php echo PathMap::build_path('users', 'register'); ?>">Sign up</a> |
			<a href="<?php echo PathMap::build_path('users', 'forgotpassword'); ?>">Forgot password</a>
		</div>
	</form>
</fieldset>