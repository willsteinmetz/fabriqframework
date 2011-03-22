<?php
if (FabriqModules::module('roles')->hasRole('administrator')):
	$core = array('users', 'roles', 'pathmap');
?>
<h1>Manage Modules</h1>
<div class="message" style="display: none;" id="message-box"></div>
<table border="0" cellspacing="0" cellpadding="0" style="width: 900px; margin-left: auto; margin-right: auto;" id="manage-modules">
	<thead>
		<tr>
			<th>Module</th>
			<th>Version</th>
			<th>Install</th>
			<th>Enable</th>
			<th>Configure</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($modules as $module): ?>
		<tr id="module-<?php echo $module->id; ?>">
			<td style="width: 530px; padding: 5px;">
				<strong><?php echo $module->module; ?></strong><?php if (in_array($module->module, $core)) { echo " (core module - cannot be disabled)"; } ?>
				<div style="padding: 5px 5px 5px 25px; font-size: 10pt;"><?php echo $module->description; ?></div>
			</td>
			<td style="width: 85px; padding: 5px; text-align: center;">
				<?php echo $module->versioninstalled; ?>
			</td>
			<td style="width: 85px; padding: 5px; text-align: center;" class="fabriqmodules-install-col">
			<?php if (in_array($module->module, $core)): ?>
				&nbsp;
			<?php else: ?>
				<button type="button" id="install-button-<?php echo $module->id; ?>" onclick="FabriqModules.<?php echo ($module->installed == 1) ? 'uninstall' : 'install'; ?>(<?php echo $module->id; ?>);"<?php if ($module->enabled == 1) { echo ' disabled="disabled" title="Module must be disabled before it can be uninstalled"'; }; ?>><?php echo ($module->installed == 1) ? 'uninstall' : 'install'; ?></button>
			<?php endif; ?>
			</td>
			<td style="width: 85px; padding: 5px; text-align: center;" class="fabriqmodules-enable-col">
			<?php if ($module->installed == 1): ?>
				<button type="button" id="enable-button-<?php echo $module->id; ?>"<?php if (in_array($module->module, $core)) { echo " disabled=\"disabled\" title=\"This is a core module. It cannot be disabled.\""; } ?> onclick="FabriqModules.<?php echo ($module->enabled == 1) ? 'disable' : 'enable'; ?>(<?php echo $module->id; ?>);"><?php echo ($module->enabled == 1) ? 'disable' : 'enable'; ?></button>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
			</td>
			<td style="width: 85px; padding: 5px; text-align: center;" class="fabriqmodules-config-col">
			<?php if (($module->hasconfigs == 1) && ($module->installed == 1)): ?>
				<button type="button" id="config-button-<?php echo $module->id; ?>" onclick="FabriqModules.configurationForm(<?php echo $module->id; ?>);">configure</button>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	FabriqModules::module('roles')->noPermission();
	FabriqModules::render('roles', 'noPermission');
	FabriqModules::has_permission(false);
endif;
?>