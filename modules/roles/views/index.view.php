<h1>Manage Roles</h1>
<?php
if ($submitted && Messaging::has_messages('successes')) {
	Messaging::display_messages('successes');
}
?>
<p>
	<a href="<?php echo PathMap::build_path('fabriqadmin', 'roles', 'create'); ?>">Add role</a> | 
	<a href="<?php echo PathMap::build_path('fabriqadmin', 'roles', 'perms'); ?>">Manage permissions</a>
</p>
<form method="post" action="<?php PathMap::build_path('fabriqadmin', 'roles', 'manage'); ?>">
<table border="0" style="border: solid 1px #999;" cellspacing="0">
	<thead>
		<tr>
			<th style="width: 300px;">Role</th>
			<th>Enabled</th>
		</tr>
	</thead>
	<tbody>
<?php for ($i = 0; $i < $roles->count(); $i++): ?>
		<tr<?php if (($i %2) == 0) { echo ' class="even"'; } ?>>
			<td style="padding: 2px;"><?php echo $roles[$i]->role; ?></td>
			<td style="text-align: center; padding: 2px;"><input type="checkbox" id="role<?php echo $roles[$i]->id; ?>" name="role<?php echo $roles[$i]->id; ?>"<?php if ($roles[$i]->enabled == 1) { echo ' checked="checked"'; } ?> value="1" /></td>
		</tr>
<?php endfor; ?>
	</tbody>
</table>
<p><input type="submit" name="submit" id="submit" value="Update" /></p>
</form>