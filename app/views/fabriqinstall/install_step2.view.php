<h1>Site configuration</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
?>
<form method="post" action="<?php PathMap::build_path('fabriqinstall', 'install', 2); ?>">
	<label for="pagetitle">Page title <span class="required-field">*</span>: </label><input type="text" id="page-title" name="pagetitle" size="50" tabindex="1"<?php if ($submitted) { echo ' value="' . $_POST['pagetitle'] . '"'; } ?> /><br />
	<label for="titleposition">Page title position <span class="required-field">*</span>: </label><select id="title-position" name="titleposition" tabindex="2">
		<option value="left"<?php if (($submitted) && ($_POST['titleposition'] == 'left')) { echo ' selected="selected"'; } ?>>Left</option>
		<option value="right"<?php if (($submitted) && ($_POST['titleposition'] == 'right')) { echo ' selected="selected"'; } ?>>Right</option>
	</select><br />
	<label for="titleseparator">Page title separator <span class="required-field">*</span>: </label><input type="text" id="title-separator" name="titleseparator" size="5" maxlength="5" value="<?php echo ($submitted) ? $_POST['titleseparator'] : '|'; ?>" tabindex="3" /><br />
	<label for="usecleanurls">Use clean URLs <span class="required-field">*</span>: </label><select id="use-clean-urls" name="usecleanurls" tabindex="4">
		<option value="TRUE"<?php if (($submitted) && ($_POST['usecleanurls'] == 'TRUE')) { echo ' selected="selected"'; } ?>>Yes</option>
		<option value="FALSE"<?php if (($submitted) && ($_POST['usecleanurls'] == 'FALSE')) { echo ' selected="selected"'; } ?>>No</option>
	</select><br />
	<div class="form-item-description"><strong>It is recommended to use clean URLs.</strong> If you are not sure whether your server has clean URLs enabled, check with the system administrator. While <strong>not recommended</strong>, it is possible to use Fabriq without clean URLs.</div>
	<label for="defaultcontroller">Default controller <span class="required-field">*</span>: </label><input type="text" id="default-controller" name="defaultcontroller" size="50" tabindex="5" value="<?php echo ($submitted) ? $_POST['defaultcontroller'] : 'homepage'; ?>" /><br />
	<div class="form-item-description">The default controller can only contain alpha-numeric and underscore characters and <strong>must begin with a letter</strong>. The default and <strong>recommended</strong> controller is <strong>homepage</strong>.</div>
	<label for="defaultaction">Default action <span class="required-field">*</span>: </label><input type="text" id="default-action" name="defaultaction" size="50" tabindex="6" value="<?php echo ($submitted) ? $_POST['defaultaction'] : 'index'; ?>" /><br />
	<div class="form-item-description">The default action of your default controller can only contain alpha-numeric and underscore characters and <strong>must begin with a letter</strong>. The default and <strong>recommended</strong> action is <strong>index</strong>.</div>
	<label for="apppath">Application path <span class="required-field">*</span>: </label><input type="text" id="apppath" name="apppath" value="<?php echo ($submitted) ? $_POST['apppath'] : '/'; ?>" tabindex="7" /><br />
	<div class="form-item-description">The application path is the root to your application on your server. If your application is stored in the default root directory, leave the application path set to /. Otherwise, set the path to the location that your application is stored on the server. <strong>Be sure that your application path begins and ends with a /</strong>.</div>
	
	<p style="text-align: right;">
		<input type="submit" value="Next step &raquo;" name="submit" />
	</p>
</form>
