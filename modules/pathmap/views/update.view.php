<?php
/**
 * @file pathmaps module update view - DO NOT EDIT
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

if ($map->controller != ''):
?>
<fieldset>
	<legend>Update path</legend>
	<div style="padding: 2px;">
		<label for="destroy_path">
			<input type="checkbox" id="destroy_path" name="destroy_path" value="1" /> Delete custom path
		</label>
	</div>
	<div id="pathmap-form">
		<input type="hidden" name="update_path" value="1" />
		<input type="hidden" name="pathmap_controller" id="pathmap_controller" value="<?php echo $map->controller; ?>" />
		<input type="hidden" name="pathmap_action" id="pathmap_action" value="<?php echo $map->action; ?>" />
		<input type="hidden" name="pathmap_modpage" id="pathmap_modpage" value="<?php echo $map->modpage; ?>" />
		<label for="path">
			Custom path: <input type="text" name="pathmap_path" id="pathmap_path" size="50" maxlength="100" value="<?php echo $map->path; ?>" />
		</label>
	</div>
</fieldset>
<script language="JavaScript">
$(function() {
	$('input#destroy_path').change(function(event) {
		if ($(this).is(':checked')) {
			$('input#pathmap_path').attr('disabled', 'disabled');
		} else {
			$('input#pathmap_path').attr('disabled', '');
		}
	});
});
</script>
<?php else: ?>
<fieldset>
	<legend>Add path map</legend>
	<div style="padding: 2px;">
		<label for="add_path">
			<input type="checkbox" id="add_path" name="add_path" value="1"<?php if ($submitted && isset($_POST['add_path'])) { echo 'checked ="checked"'; } ?> /> Add custom path
		</label>
	</div>
	<div id="pathmap-form">
		<input type="hidden" name="pathmap_controller" id="pathmap_controller" value="<?php echo $pathmap_controller; ?>" />
		<input type="hidden" name="pathmap_action" id="pathmap_action" value="<?php echo $pathmap_action; ?>" />
		<input type="hidden" name="pathmap_modpage" id="pathmap_modpage" value="<?php echo $pathmap_modpage; ?>" />
		<label for="path">
			Custom path: <input type="text" name="pathmap_path" id="pathmap_path" size="50" maxlength="100"<?php if ($submitted) { echo " value=\"{$_POST['pathmap_path']}\""; } ?> />
		</label>
	</div>
</fieldset>
<script language="JavaScript">
$(function() {
	$('input', '#pathmap-form').attr('disabled', 'disabled');
	$('#add_path').change(function(event) {
		if ($(this).is(':checked')) {
			$('input', '#pathmap-form').attr('disabled', '');
		} else {
			$('input', '#pathmap-form').attr('disabled', 'disabled');
		}
	});
});
</script>
<?php endif; ?>
