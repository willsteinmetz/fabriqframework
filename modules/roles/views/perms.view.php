<h1>Module permissions</h1>
<?php
if (Messaging::has_messages('successes')) {
	Messaging::display_messages('successes');
}
?>
<form method="post" action="<?php PathMap::build_path('fabriqadmin', 'roles', 'persm'); ?>">
<?php foreach ($modules as $module): ?>
<h3><?php echo $module->module; ?></h3>
	<?php if (isset($perms->modules[$module->id])): ?>
<table>
	<thead>
		<tr>
			<th style="width: 300px;">&nbsp;</th>
			<?php foreach($roles as $role): ?>
			<th style="width: 85px; text-align: center; font-size: 10pt;<?php if ($role->enabled == 0) { echo ' color: #999;'; } ?>"><?php echo $role->role; ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($perms->modules[$module->id] as $perm): ?>
		<tr>
			<td style="width: 300px;"><?php echo $perms[$perm]->permission; ?></td>
			<?php foreach($roles as $role): ?>
			<td style="width: 85px; text-align: center;"><input type="checkbox" name="permission[<?php echo $perms[$perm]->id; ?>][<?php echo $role->id; ?>]"<?php if ($permissions[$perms[$perm]->id][$role->id]) { echo ' checked="checked"'; } ?> title="<?php echo $perms[$perm]->permission . ' - ' . $role->role; ?>" value="1"<?php if ($role->enabled == 0) { echo ' disabled="disabled"'; } ?> /></td>
			<?php endforeach; ?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
	<?php else: ?>
	<p>There are no permissions to set for this module.</p>
	<?php endif; ?>
<?php endforeach; ?>
<p><input type="submit" name="submit" id="submit" value="Save changes" /></p>
</form>
