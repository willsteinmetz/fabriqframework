<h1>Change password</h1>
<?php
if (Messaging::has_messages('warnings')) {
	Messaging::display_messages('warnings');
}
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
if ($submitted && Messaging::has_messages('successes')) {
	Messaging::display_messages('successes');
}

$path = str_replace('users/changePassword/', '', $_GET['q']);
if ($_GET['q'] != 'users/changePassword'): ?>
<form method="post" action="<?php echo call_user_func_array('PathMap::build_path', array_merge(array('users', 'changePassword'), explode('/', $path))); ?>">
	<input type="hidden" id="return_to" name="return_to" value="<?php echo $path; ?>" />
<?php else: ?>
<form method="post" action="<?php echo PathMap::build_path('users', 'changePassword'); ?>">
<?php endif; ?>
	<div style="padding: 2px;">
		<label for="currpwd">
			Current password: <input type="password" name="currpwd" size="50" maxlength="100" id="currpwd" />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="newpwd">
			New password: <input type="password" name="newpwd" size="50" maxlength="100" id="newpwd" /><br />
			<span style="font-size: 8pt;">Passwords must be at least 8 characters long and must be different from your username and e-mail address.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="comfnewpwd">
			Comfirm new password: <input type="password" name="comfnewpwd" size="50" maxlength="100" id="comfnewpwd" />
		</label>
	</div>
	<div style="padding: 2px;">
		<input type="submit" name="submit" value="Change password" />
	</div>
</form>
<p>
	<?php Fabriq::link_to('Update account', 'users', 'updateAccount'); ?> | 
	<?php Fabriq::link_to('My account', 'users', 'myAccount'); ?>
</p>
