<?php
/**
 * @file Fabriq install template
 * @author Will Steinmetz
 * 
 * Copyright (c)2013, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Fabriq Framework | <?php echo Fabriq::title(); ?></title>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>
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
		<div id="site-name">Fabriq - <?php if (PathMap::action() == 'install') { echo 'Install'; } else { echo 'Update'; } ?></div>
	</section>
</header>
<?php if (PathMap::action() == 'install'): ?>
<nav id="default-nav">

	<section class="nav">
		<ul>
			<li<?php if (PathMap::arg(2) == 1) { echo ' class="current"'; } ?>>Start</li>
			<li<?php if (PathMap::arg(2) == 2) { echo ' class="current"'; } ?>>Site configuration</li>
			<li<?php if (PathMap::arg(2) == 3) { echo ' class="current"'; } ?>>Database configuration</li>
			<li<?php if (PathMap::arg(2) == 4) { echo ' class="current"'; } ?>>Module installation</li>
			<li<?php if (PathMap::arg(2) == 5) { echo ' class="current"'; } ?>>Finish</li>
		</ul>
		<div class="clearbox">&nbsp;</div>
	</section>
</nav>
<?php else: ?>
<nav id="default-nav">
	<section class="nav">
		<ul>
			<li<?php if (PathMap::arg(2) == 1) { echo ' class="current"'; } ?>>Start</li>
			<li<?php if (PathMap::arg(2) == 2) { echo ' class="current"'; } ?>>Framework updates</li>
			<li<?php if (PathMap::arg(2) == 3) { echo ' class="current"'; } ?>>Module updates</li>
			<li<?php if (PathMap::arg(2) == 4) { echo ' class="current"'; } ?>>Finish</li>
		</ul>
		<div class="clearbox">&nbsp;</div>
	</section>
</nav>
<?php endif; ?>

<section id="body">
	<section id="content">

<?php echo FabriqTemplates::body(); ?>

	</section>
</section>

</body>
</html>