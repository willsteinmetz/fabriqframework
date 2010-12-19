<fieldset id="users-login">
	<legend>Log in</legend>
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
</fieldset>