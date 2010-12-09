<?php $core = array('users', 'roles', 'pathmap'); ?>
<h1>Manage Modules</h1>
<table border="0" style="width: 900px; margin-left: auto; margin-right: auto;" id="manage-modules">
	<tbody>
<?php foreach ($modules as $module): ?>
		<tr>
			<td style="width: 690px; padding: 5px;"><p><strong><?php echo $module->module; ?></strong><?php if (in_array($module->module, $core)) { echo " (core module - cannot be disabled)"; } ?></p></td>
			<td style="width: 190px; padding: 5px;"><button<?php if (in_array($module->module, $core)) { echo " disabled=\"disabled\" title=\"This is a core module. It cannot be disabled.\""; } ?>><?php echo ($module->enabled == 1) ? 'disable' : 'enable'; ?></button></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>