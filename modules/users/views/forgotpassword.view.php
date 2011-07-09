<h1>Reset password</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
if (!$submitted || ($submitted && Messaging::has_messages())):
?>
<p>Enter your display name or e-mail address in the field below and click submit.</p>
<form method="post" action="<?php echo PathMap::build_path('users', 'forgotPassword'); ?>">
	<p><label for="user">User:</label> <input type="text" size="50" maxlength="100" id="user" name="user" /></p>
	<p><input type="submit" value="Submit" name="submit" /></p>
</form>
<?php else: ?>
<p>A new password has been generated and sent the e-mail address associated with your account. You will be asked to change your password the next time you log in.</p>
<p><?php Fabriq::link_to('Return to log in page', 'users', 'login'); ?></p>
<?php endif; ?>
