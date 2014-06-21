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
		<img src="data:image/png;base64,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" title="Fabriq Framework" id="fabriq-icon" alt="F" style="margin: 5px; float: left;" />
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