<h1>Core module configuration</h1>
<?php
if (Messaging::has_messages()) {
	Messaging::display_messages();
} else {
	Messaging::display_messages('successes');
	Messaging::display_messages('warnings');
}
?>
<p><strong>You must create an initial user. This user will be given the administrator role. Users with administrator role can access any areas that are enabled for the role. By default, the user module is configured so that only administrators can add users. This configuration option can be changed later</strong></p>
<form method="post" action="<?php echo PathMap::build_path('fabriqinstall', 'install', 4); ?>">
	<div style="padding: 2px;">
		<label for="display">Display name: </label>
		<input type="text" name="display" id="display" size="24" maxlength="24" /><br />
		<span style="font-size: 8pt;">Display names may only contain charcters, number, and the underscore character between 6 and 24 characters long.</span>
		
	</div>
	<div style="padding: 2px;">
		<label for="email">e-mail address: </label>
		<input type="text" name="email" id="email" size="50" maxlength="100" /><br />
		<span style="font-size: 8pt;">Must be a valid e-mail address. This e-mail address will be used when necessary to contact the user.</span>
		
	</div>
	<div style="padding: 2px;">
		<label for="pwd">Password: </label>
		<input type="password" name="pwd" id="pwd" size="24" /><br />
		<span style="font-size: 8pt;">Passwords must be at least 8 characters long. The user will be forced to change their password after their first log in.</span>
	</div>
	<div style="padding: 2px;">
		<label for="confpwd">Confirm password: </label>
		<input type="password" name="confpwd" id="confpwd" size="24" /><br />
	</div>
	<p style="text-align: right;">
		<input type="submit" value="Finish install" name="submit" />
	</p>
</form>
