<?php
/**
 * @file Fabriq install template
 * @author Will Steinmetz
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Fabriq Framework | <?php echo Fabriq::title(); ?></title>
<?php
// process css queue
foreach (Fabriq::cssqueue() as $css) {
	echo "<link href=\"{$css['path']}{$css['css']}{$css['ext']}\" media=\"{$css['media']}\" rel=\"stylesheet\" type=\"text/css\" />\n";
}

// process javascript queue
foreach (Fabriq::jsqueue() as $js) {
	echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"{$js['path']}{$js['js']}{$js['ext']}\"></script>\n";
}
?>
<script type="text/javascript">
//<![CDATA[
jQuery.extend(Fabriq.settings, {"basePath": "<?php echo PathMap::base_path(); ?>", "cleanURLs": <?php echo PathMap::clean_urls_str(); ?>});
//]]>
</script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>

<header>
	<section id="header">
		<img src="http://fabriqframework.com/public/images/Fabriqx60.png" title="Fabriq Framework" id="fabriq-icon" alt="F" style="margin: 5px; float: left;" />
		<div id="site-name">Fabriq Framework</div>
	</section>
</header>
<?php if (PathMap::action() == 'install'): ?>
<nav id="default-nav">
	<section class="nav">
		<ul>
			<li>Install</li>
		</ul>
		<div class="clearbox">&nbsp;</div>
	</section>
</nav>
<nav id="admin-nav">
	<section class="nav">
		<ul>
			<li>Start</li>
			<li>Site configuration</li>
			<li>Database configuration</li>
			<li>Module installation</li>
			<li>Finish</li>
		</ul>
		<div class="clearbox">&nbsp;</div>
	</section>
</nav>
<?php else: ?>
<nav id="default-nav">
	<section class="nav">
		<ul>
			<li>Update</li>
		</ul>
		<div class="clearbox">&nbsp;</div>
	</section>
</nav>
<nav id="admin-nav">
	<section class="nav">
		<ul>
			<li>Start</li>
			<li>Framework updates</li>
			<li>Module updates</li>
			<li>Finish</li>
		</ul>
		<div class="clearbox">&nbsp;</div>
	</section>
</nav>
<?php endif; ?>

<section id="body">
	<section id="content">

<?php require_once("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php"); ?>

	</section>
</section>

</body>
</html>