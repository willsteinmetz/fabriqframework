<?php $core = array(/*'users', */'roles', 'pathmap'); ?>
<h1>Manage Modules</h1>
<table border="0" cellspacing="0" cellpadding="0" style="width: 900px; margin-left: auto; margin-right: auto;" id="manage-modules">
	<thead>
		<tr>
			<th>Module</th>
			<th>Version</th>
			<th>Enabled</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($modules as $module): ?>
		<tr id="module-<?php echo $module->id; ?>">
			<td style="width: 670px; padding: 5px;">
				<strong><?php echo $module->module; ?></strong><?php if (in_array($module->module, $core)) { echo " (core module - cannot be disabled)"; } ?>
				<div style="padding: 5px 5px 5px 25px; font-size: 10pt;"><?php echo $module->description; ?></div>
			</td>
			<td style="width: 100px; padding: 5px; text-align: center;">
				<?php echo $module->versioninstalled; ?>
			</td>
			<td style="width: 100px; padding: 5px; text-align: center;">
				<button id="enable-button-<?php echo $module->id; ?>"<?php if (in_array($module->module, $core)) { echo " disabled=\"disabled\" title=\"This is a core module. It cannot be disabled.\""; } ?> onclick="FabriqModules.hasConfiguration(<?php echo $module->id; ?>);"><?php echo ($module->enabled == 1) ? 'disable' : 'enable'; ?></button>
			</td>
		</tr>
		<tr style="display: none;">
			<td id="module-config-<?php echo $module->id; ?>" class="module-config" colspan="3"></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>