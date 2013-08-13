<h1>Register</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}

if (!$submitted || ($submitted && Messaging::has_messages())):
?>
<p><strong>All fields are required</strong></p>
<form id="user-registration" method="post" action="<?php echo PathMap::build_path('users', 'register'); ?>">
	<div style="padding: 2px;">
		<label for="display">
			Display name: <span id="display-available" class="value-check"></span><br />
			<input type="text" name="display" id="display" size="24" maxlength="24" /><br />
			<span style="font-size: 8pt;">Display names may only contain charcters, number, and the underscore character between 6 and 24 characters long.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="email">
			e-mail address: <span id="email-available" class="value-check"></span><br />
			<input type="text" name="email" id="email" size="50" maxlength="100" /><br />
			<span style="font-size: 8pt;">Must be a valid e-mail address. This e-mail address will be used when necessary to contact the user.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="pwd">
			Password:<br />
			<input type="password" name="pwd" id="pwd" size="24" /><br />
			<span style="font-size: 8pt;">Passwords must be at least 8 characters long. The user will be forced to change their password after their first log in.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="confpwd">
			Confirm password:<br />
			<input type="password" name="confpwd" id="confpwd" size="24" /><br />
		</label>
	</div>
	<div style="padding: 2px;">
		<input type="submit" id="register-user" name="register" value="Register" />
	</div>
</form>
<?php else: ?>
	<p>Your account has been created. You can now <a href="<?php echo PathMap::build_path('users', 'login'); ?>">log in</a>.</p>
<?php endif; ?>
