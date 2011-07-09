<form>
	<div style="padding: 2px;">
		<label for="anyoneCanRegister">
			<strong>How are new users created?</strong><br />
			<input type="radio" value="1" name="anyoneCanRegister" style="margin-left: 15px;"<?php if ($module_configs[$module_configs->configs['anyoneCanRegister']]->val == 1) { echo ' checked="checked"'; } ?> />Any one can register<br />
			<input type="radio" value="0" name="anyoneCanRegister" style="margin-left: 15px;"<?php if ($module_configs[$module_configs->configs['anyoneCanRegister']]->val == 0) { echo ' checked="checked"'; } ?> />Only administrators can add new users
		</label>
	</div>
	<div style="padding: 2px;">
		<button type="button" name="submit">Save configuration</button> 
		<button type="button" name="cancel">Cancel</button>
	</div>
</form>
<script language="JavaScript" type="text/javascript" id="ajax-callback">
$(function() {
	$('button[name="cancel"]').click(function(event) {
		Fabriq.UI.Overlay.close();
	});
	$('button[name="submit"]').click(function(event) {
		$.ajax({
			type: 'POST',
			url: Fabriq.build_path('fabriqmodules', 'configure', '<?php echo PathMap::arg(2); ?>'),
			data: {
				anyoneCanRegister: ($('input[name="anyoneCanRegister"]:eq(0)').is(':checked')) ? 1 : 0,
				submit: true
			},
			dataType: 'json',
			success: function(data, status) {
				if (data.success) {
					Fabriq.UI.Overlay.close();
					$('#message-box')
						.addClass('successes')
						.html('Users module configuration has been saved')
						.fadeIn();
				}
			}
		});
	});
});
</script>