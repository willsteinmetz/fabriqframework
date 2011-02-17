<h1>Site configuration</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
$appPath = '/';
$aPath = substr($_SERVER['REQUEST_URI'], 1);
$aPath = str_replace('index.php?q=', '', $aPath);
$aPath = explode('/', $aPath);
$i = 0;
while ($aPath[$i] != 'fabriqinstall') {
	$appPath .= $aPath[$i] . '/';
	$i++;
}
if (isset($_SESSION['FAB_INSTALL_site']) && ($_SESSION['FAB_INSTALL_site'] != '')) {
	$siteConfig = unserialize($_SESSION['FAB_INSTALL_site']);
	$submitted = true;
	foreach ($siteConfig as $key => $val) {
		$_POST[$key] = $val;
	}
}
?>
<form method="post" action="<?php PathMap::build_path('fabriqinstall', 'install', 2); ?>">
	<label for="title">Page title <span class="required-field">*</span>: </label><input type="text" id="title" name="title" size="50" tabindex="1"<?php if ($submitted) { echo ' value="' . $_POST['title'] . '"'; } ?> /><br />
	<label for="title_pos">Page title position <span class="required-field">*</span>: </label><select id="title-pos" name="title_pos" tabindex="2">
		<option value="left"<?php if (($submitted) && ($_POST['title_pos'] == 'left')) { echo ' selected="selected"'; } ?>>Left</option>
		<option value="right"<?php if (($submitted) && ($_POST['title_pos'] == 'right')) { echo ' selected="selected"'; } ?>>Right</option>
	</select><br />
	<label for="title_sep">Page title separator <span class="required-field">*</span>: </label><input type="text" id="title-sep" name="title_sep" size="5" maxlength="5" value="<?php echo ($submitted) ? $_POST['title_sep'] : '|'; ?>" tabindex="3" /><br />
	<label for="cleanurls">Use clean URLs <span class="required-field">*</span>: </label><select id="clean-urls" name="cleanurls" tabindex="4">
		<option value="TRUE"<?php if (($submitted) && ($_POST['cleanurls'] == 'TRUE')) { echo ' selected="selected"'; } ?>>Yes</option>
		<option value="FALSE"<?php if (($submitted) && ($_POST['cleanurls'] == 'FALSE')) { echo ' selected="selected"'; } ?>>No</option>
	</select><br />
	<div class="form-item-description"><strong>It is recommended to use clean URLs.</strong> If you are not sure whether your server has clean URLs enabled, check with the system administrator. While <strong>not recommended</strong>, it is possible to use Fabriq without clean URLs.</div>
	<label for="cdefault">Default controller <span class="required-field">*</span>: </label><input type="text" id="cdefault" name="cdefault" size="50" tabindex="5" value="<?php echo ($submitted) ? $_POST['cdefault'] : 'homepage'; ?>" /><br />
	<div class="form-item-description">The default controller can only contain alpha-numeric and underscore characters and <strong>must begin with a letter</strong>. The default and <strong>recommended</strong> controller is <strong>homepage</strong>.</div>
	<label for="adefault">Default action <span class="required-field">*</span>: </label><input type="text" id="adefault" name="adefault" size="50" tabindex="6" value="<?php echo ($submitted) ? $_POST['adefault'] : 'index'; ?>" /><br />
	<div class="form-item-description">The default action of your default controller can only contain alpha-numeric and underscore characters and <strong>must begin with a letter</strong>. The default and <strong>recommended</strong> action is <strong>index</strong>.</div>
	<label for="url">URL <span class="required-field">*</span>: </label><input type="text" id="url" name="url" size="50" tabindex="7" <?php if ($submitted) { echo ' value="' . $_POST['url'] . '"'; } else { echo ' value="http://' . $_SERVER['HTTP_HOST'] . '"'; } ?>><br />
	<label for="apppath">Application path <span class="required-field">*</span>: </label><input type="text" id="apppath" name="apppath" value="<?php echo ($submitted) ? $_POST['apppath'] : $appPath; ?>" tabindex="8" /><br />
	<div class="form-item-description">The application path is the root to your application on your server. If your application is stored in the default root directory, leave the application path set to /. Otherwise, set the path to the location that your application is stored on the server. <strong>Be sure that your application path begins and ends with a /</strong>.</div>
	<label for="templating">Enable templating </label><input type="checkbox" id="templating" name="templating" value="true" tabindex="9"<?php if (($submitted && (($_POST['templating'] == true) || ($_POST['templating'] == 'true'))) || (!$submitted)) { echo ' checked="checked"'; } ?> /><br />
	<div class="form-item-description">Check this box to enable templating by default.</div>
	
	<p style="text-align: right;">
		<input type="submit" value="Next step &raquo;" name="submit" />
	</p>
</form>
