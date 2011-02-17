<h1>Database configuration</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
?>
<form method="post" action="<?php PathMap::build_path('fabriqinstall', 'install', 3); ?>">
	<label for="db">Database name <span class="required-field">*</span>: </label><input type="text" id="db" name="db" size="50" tabindex="9"<?php if ($submitted) { echo ' value="' . $_POST['db'] . '"'; } ?> /><br />
	<div class="form-item-description">This database must already exist</div>
	<label for="user">Database user <span class="required-field">*</span>: </label><input type="text" id="user" name="user" size="50" tabindex="10"<?php if ($submitted) { echo ' value="' . $_POST['user'] . '"'; } ?> /><br />
	<div class="form-item-description">This user must have privileges to use the selected database</div>
	<label for="pwd">Password <span class="required-field">*</span>: </label><input type="password" id="pwd" name="pwd" size="50" tabindex="11" /><br />
	<label for="server">Database server <span class="required-field">*</span>: </label><input type="text" id="server" name="server" size="50" tabindex="12"<?php if ($submitted) { echo ' value="' . $_POST['server'] . '"'; } ?> /><br />
	<p style="text-align: right;">
		<input type="submit" value="Next step &raquo;" name="submit" />
	</p>
</form>
