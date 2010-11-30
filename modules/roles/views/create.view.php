<h1>Create a role</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}

if (!$submitted || ($submitted && Messaging::has_messages())):
?>
<form method="post" action="<?php echo PathMap::build_path('fabriqadmin', 'roles', 'create'); ?>">
<div style="padding: 2px;">
	<label for="role">
		Role: <input type="text" id="role" name="role" size="50" maxlength="100"<?php if ($submitted) { echo " value=\"{$role->role}\""; } ?> />
	</label>
</div>
<div style="padding: 2px;">
	<input type="submit" name="submit" id="submit" value="Add role" />
</div>
</form>
<?php else: ?>
<p>New role "<?php echo $role->role; ?>" has been created.</p>
<p><a href="<?php echo PathMap::build_path('fabriqadmin', 'roles', 'manage'); ?>">&laquo; Return to roles list</p>
<?php endif;?>