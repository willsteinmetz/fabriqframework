<h1>Update account</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
if ($submitted && Messaging::has_messages('successes')) {
	Messaging::display_messages('successes');
}
?>
<p><strong>All fields are required</strong></p>
<form id="user-registration" method="post" action="<?php echo PathMap::build_path('users', 'updateAccount'); ?>">
	<input type="hidden" id="user" name="user" value="<?php echo $user->id; ?>" />
	<div style="padding: 2px;">
		<label for="display">
			Display name: <span id="display-available" class="value-check"></span><br />
			<input type="text" name="display" id="display" size="24" maxlength="24" value="<?php echo $user->display; ?>" /><br />
			<span style="font-size: 8pt;">Display names may only contain charcters, number, and the underscore character between 6 and 24 characters long.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="email">
			e-mail address: <span id="email-available" class="value-check"></span><br />
			<input type="text" name="email" id="email" size="50" maxlength="100" value="<?php echo $user->email; ?>" /><br />
			<span style="font-size: 8pt;">Must be a valid e-mail address. This e-mail address will be used when necessary to contact the user.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<input type="submit" id="update-account" name="submit" value="Update account" />
	</div>
</form>
<p>
	<a href="<?php echo PathMap::build_path('users', 'changePassword'); ?>">Change password</a> | 
	<a href="<?php echo PathMap::build_path('users', 'myAccount'); ?>">My account</a>
</p>
