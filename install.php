<?php
/**
 * @file Installer file
 * @author Will Steinmetz
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
$errors = array();
$messages = array();
$submitted = FALSE;
$step = (isset($_GET['s']) && is_numeric($_GET['s'])) ? $_GET['s'] : 1;

/**
 * Determines if directories are writeable
 * @return boolean
 */
function dirs_writeable() {
	global $errors;
	
	try {
		$confFile = 'config/config.inc.php';
		$fh = fopen($confFile, 'w');
		fclose($fh);
		unlink($confFile);
	} catch (Exception $e) {
		$errors[] = 'The <code>config</code> directory must be writeable';
	}
	try {
		$confFile = 'app/controllers/test.controller.php';
		$fh = fopen($confFile, 'w');
		fclose($fh);
		unlink($confFile);
	} catch (Exception $e) {
		$errors[] = 'The <code>app/controllers</code> directory must be writeable';
	}
	try {
		mkdir('app/views/test');
		rmdir('app/views/test');
	} catch (Exception $e) {
		$errors[] = 'The <code>app/views</code> directory must be writeable';
	}
	
	if (count($errors) == 0) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

switch ($step) {
	case 4:
		if (!file_exists('config/config.inc.php')) {
			header("Location: install.php?s=1");
			exit();
		}
	break;
	case 3:
		if (!file_exists('config/config.inc.php')) {
			header("Location: install.php?s=1");
			exit();
		}
	break;
	case 2:
		if (file_exists('config/config.inc.php')) {
			header("Location: install.php?s=4");
			exit();
		}
		if (dirs_writeable()) {
			if (isset($_POST['submit'])) {
				if (strlen(trim($_POST['pagetitle'])) == 0) {
					array_push($errors, 'You must enter a page title');
				}
				if (strlen(trim($_POST['titleseparator'])) == 0) {
					array_push($errors, 'You must enter a page title separator');
				}
				if (!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9]+)$/', $_POST['defaultcontroller'])) {
					array_push($errors, 'You must enter a default controller. The default controller can only contain alpha-numeric and underscore characters and must begin with a letter.');
				}
				if (!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9]+)$/', $_POST['defaultaction'])) {
					array_push($errors, 'You must enter a default action. The default action can only contain alpha-numeric and underscore characters and must begin with a letter.');
				}
				if (strlen(trim($_POST['dbname'])) == 0) {
					array_push($errors, 'You must enter a database name');
				}
				if (strlen(trim($_POST['dbuser'])) == 0) {
					array_push($errors, 'You must enter a database user');
				}
				if (strlen(trim($_POST['dbpwd'])) == 0) {
					array_push($errors, 'You must enter a database user password');
				}
				if (strlen(trim($_POST['dbserver'])) == 0) {
					array_push($errors, 'You must enter a database server');
				}
				if (trim($_POST['dbtype']) == '') {
					array_push($errors, 'You must select a database type');
				}
				
				$submitted = TRUE;
				if (count($errors) == 0) {
					// write config file
					$confFile = 'config/config.inc.php';
					$fh = fopen($confFile, 'w');
					fwrite($fh, "<?php\n");
					fwrite($fh, "/**\n");
					fwrite($fh, " * @file\n");
					fwrite($fh, " * Base config file for a Fabriq app.\n");
					fwrite($fh, " */\n\n");
					fwrite($fh, "\$_FAPP = array(\n");
					fwrite($fh, "	'title' => \"{$_POST['pagetitle']}\",\n");
					fwrite($fh, "	'title_pos' => '{$_POST['titleposition']}',\n");
					fwrite($fh, "	'title_sep' => \"{$_POST['titleseparator']}\",\n");
					fwrite($fh, "	'cleanurls' => {$_POST['usecleanurls']},\n");
					fwrite($fh, "	'cdefault' => '{$_POST['defaultcontroller']}',\n");
					fwrite($fh, "	'adefault' => '{$_POST['defaultaction']}',\n");
					fwrite($fh, "	'apppath' => '{$_POST['apppath']}'\n");
					fwrite($fh, ");\n\n");
					fwrite($fh, "\$_FDB['default'] = array(\n");
					fwrite($fh, "	'user' => '{$_POST['dbuser']}',\n");
					fwrite($fh, "	'pwd' => '{$_POST['dbpwd']}',\n");
					fwrite($fh, "	'db' => '{$_POST['dbname']}',\n");
					fwrite($fh, "	'server' => '{$_POST['dbserver']}',\n");
					fwrite($fh, "	'type' => '{$_POST['dbtype']}'\n");
					fwrite($fh, ");\n");
					fclose($fh);
					
					// write default controller
					$contFile = "app/controllers/{$_POST['defaultcontroller']}.controller.php";
					$fh = fopen($contFile, 'w');
					fwrite($fh, "<?php\n");
					fwrite($fh, "class {$_POST['defaultcontroller']}_controller extends Controller {\n");
					fwrite($fh, "\tfunction {$_POST['defaultaction']}() {\n");
					fwrite($fh, "\t\tFabriq::title('Welcome to {$_POST['pagetitle']}');\n");
					fwrite($fh, "\t}\n");
					fwrite($fh, "}\n");
					fclose($fh);
					
					// write default action
					mkdir("app/views/{$_POST['defaultcontroller']}");
					$actionFile = "app/views/{$_POST['defaultcontroller']}/{$_POST['defaultaction']}.view.php";
					$fh = fopen($actionFile, 'w');
					fwrite($fh, "<h1>{$_POST['defaultcontroller']}#{$_POST['defaultaction']}</h1>\n");
					fclose($fh);
					
					header("Location: install.php?s=3");
					exit();
				}
			}
		}
	break;
	case 1: default:
		if (file_exists('config/config.inc.php')) {
			header("Location: install.php?s=4");
			exit();
		}
	break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=encoding" />
<title>Install Fabriq for your website/web application</title>
<style type="text/css">
body { font-family: Arial, Helvetica, Verdana, sans-serif; margin: 0; padding: 0; background-color: #EEE; }
image { border: none; }
h1 { color: #333; }
code { color: #454545; }
.fabriq-info a:link, .fabriq-info a:active, .fabriq-info a:visited { color: #900; text-decoration: underline; }
.fabriq-info a:hover, .fabriq-info a:focus { color: #600; text-decoration: underline; }
#body { background-color: #FFF; border: solid 1px #d6d6d6; -moz-border-radius: 10px; -webkit-border-radius: 10px; width: 900px; margin: 10px auto; padding: 10px; }
#fabriq-header { margin: 0; padding: 0; color: #900; font-size: 36pt; }
#fabriq-header-text { color: #454545; }
.required-field { font-weight: bold; color: #900; }
fieldset { -moz-border-radius: 5px; -webkit-border-radius: 5px; }
input[type='text'], input[type='password'] { outline: none; border: solid 1px #CCC; padding: 2px; }
input[type='text']:focus, input[type='password']:focus { background-color: #FFF; border: solid 1px #999; }
select { padding: 2px; outline: none; border: solid 1px #CCC; }
select:focus { background-color: #FFF; border: solid 1px #999; }
.form-item-description { font-size: 10pt; padding: 0 10px; color: #666; }
.form-item-description strong { color: #900; }
legend { color: #600; }
.error-box { border: solid 1px #C00; -moz-border-radius: 5px; -webkit-border-radius: 5px; padding: 5px; margin: 5px 0; }
</style>
</head>
<body>


<div id="body">
	<div style="float: left;"><img src="http://fabriqframework.com/public/images/Fabriq.png" /></div>
	<div style="margin-left: 110px;">
		<h1 id="fabriq-header">Fabriq</h1>
		Stop weaving tangled webpages, start with a strong Fabriq.
	</div>
	<div class="clearbox">&nbsp;</div>
<?php
switch($step) {
	case 4:
?>
	<h1>Already installed</h1>
	<p>This Fabriq application has already been installed.</p>
	<p><a href="index.php">Return to the Fabriq app's homepage</a></p>
<?php
		break;
	case 3:
?>
	<h2>Finished!</h2>
	<p>Congrats! You have just set up your Fabriq application!</p>
	<p>Be sure to remove the write permissions for the following directories (<code>chmod 775</code> is sufficient for most systems):</p>
	<ul>
		<li><code>/config</code></li>
		<li><code>/app/controllers</code></li>
		<li><code>/app/views</code></li>
	</ul>
	<p>Now that your Fabriq app is installed, you can delete the install.php script from the directory.</p>
	<p><a href="index.php">Return to the Fabriq app's homepage</a></p>
<?php
		break;
	case 2:
		if (!dirs_writeable()) {
			echo "<div class=\"error-box\">\n";
			echo "<p>Before you can continue, you must fix the following problems:</p>\n";
			echo "<ul>\n";
			foreach($errors as $error) {
				echo "\t<li><em>{$error}</em></li>\n";
			}
			echo "</ul>\n";
			echo "</div>\n";
		}
		else {
?>
	<h2>Step 2: Entering site details</h2>
<?php
if (count($errors) > 0) {
	echo "<div class=\"error-box\">\n";
	echo "<p>Before you can continue, you must fix the following problems:</p>\n";
	echo "<ul>\n";
	foreach($errors as $error) {
		echo "\t<li><em>{$error}</em></li>\n";
	}
	echo "</ul>\n";
	echo "</div>\n";
}
?>
	<form method="post" action="install.php?s=2">
		<fieldset>
			<legend>Website/web application information</legend>
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
		</fieldset>
		<fieldset>
			<legend>Database information</legend>
			<label for="dbtype">Database type <span class="required-field">*</span>: </label><select name="dbtype" id="db-type" tabindex="8">
				<option>-- Select one --</option>
				<option value="MySQL">MySQL</option>
				<option value="pgSQL">PostgreSQL</option>
			</select><br />
			<label for="dbname">Database name <span class="required-field">*</span>: </label><input type="text" id="db-name" name="dbname" size="50" tabindex="9"<?php if ($submitted) { echo ' value="' . $_POST['dbname'] . '"'; } ?> /><br />
			<div class="form-item-description">This database must already exist</div>
			<label for="dbuser">Database user <span class="required-field">*</span>: </label><input type="text" id="db-user" name="dbuser" size="50" tabindex="10"<?php if ($submitted) { echo ' value="' . $_POST['dbuser'] . '"'; } ?> /><br />
			<div class="form-item-description">This user must have privileges to use the selected database</div>
			<label for="dbpwd">Password <span class="required-field">*</span>: </label><input type="password" id="db-pwd" name="dbpwd" size="50" tabindex="11" /><br />
			<label for="dbserver">Database server <span class="required-field">*</span>: </label><input type="text" id="db-server" name="dbserver" size="50" tabindex="12"<?php if ($submitted) { echo ' value="' . $_POST['dbserver'] . '"'; } ?> /><br />
		</fieldset>
		<div style="float: left;">
			<input type="button" value="&laquo; Back" onclick="window.location = 'install.php?s=1';" />
		</div>
		<div style="margin-left: auto; text-align: right">
			<input type="submit" value="Next step &raquo;" name="submit" />
		</div>
		<div class="clearbox">&nbsp;</div>
	</form>
<?php
		}
		break;
	case 1: default:
?>
	<h2>Step 1: Getting stared</h2>
	<p>Before moving on to the next step, make sure of the following:</p>
	<ul>
		<li>
			Be sure that the following directories have write permissions:
			<ul>
				<li><code>/config</code></li>
				<li><code>/app/controllers</code></li>
				<li><code>/app/views</code></li>
			</ul>
		</li>
		<li>
			Be sure to have the following details about the database you are using available:
			<ul>
				<li>Database name</li>
				<li>Database username with privileges to the database</li>
				<li>Database password</li>
				<li>Database server</li>
				<li>Database type (Fabriq currently supports MySQL and PostregSQL)</li>
			</ul>
		</li>
	</ul>
	<p style="text-align: right;">
		<input type="button" value="Next step &raquo;" onclick="window.location = 'install.php?s=2';" />
	</p>
<?php
		break;
}
?>
</div>

</body>
</html>