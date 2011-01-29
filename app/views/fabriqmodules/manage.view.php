<?php $core = array('users', 'roles', 'pathmap'); ?>
<h1>Manage Modules</h1>
<div class="message" style="display: none;" id="message-box"></div>
<table border="0" cellspacing="0" cellpadding="0" style="width: 900px; margin-left: auto; margin-right: auto;" id="manage-modules">
	<thead>
		<tr>
			<th>Module</th>
			<th>Version</th>
			<th>Enabled</th>
			<th>Configuration</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($modules as $module): ?>
		<tr id="module-<?php echo $module->id; ?>">
			<td style="width: 570px; padding: 5px;">
				<strong><?php echo $module->module; ?></strong><?php if (in_array($module->module, $core)) { echo " (core module - cannot be disabled)"; } ?>
				<div style="padding: 5px 5px 5px 25px; font-size: 10pt;"><?php echo $module->description; ?></div>
			</td>
			<td style="width: 100px; padding: 5px; text-align: center;">
				<?php echo $module->versioninstalled; ?>
			</td>
			<td style="width: 100px; padding: 5px; text-align: center;">
				<button type="button" id="enable-button-<?php echo $module->id; ?>"<?php if (in_array($module->module, $core)) { echo " disabled=\"disabled\" title=\"This is a core module. It cannot be disabled.\""; } ?> onclick="FabriqModules.<?php echo ($module->enabled == 1) ? 'disable' : 'enable'; ?>(<?php echo $module->id; ?>);"><?php echo ($module->enabled == 1) ? 'disable' : 'enable'; ?></button>
			</td>
			<td style="width: 100px; padding 5px; text-align: center;">
			<?php if ($module->hasconfigs == 1): ?>
				<button type="button" id="config-button-<?php echo $module->id; ?>" onclick="FabriqModules.configurationForm(<?php echo $module->id; ?>);">configure</button>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>