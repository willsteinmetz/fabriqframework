<?php
/**
 * @file Application layout. Modify this file for the layout of your website
 * or application. Keep a copy of this file available for websites or
 * applications using multiple layouts.
 * 
 * Copyright (c)2010, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?php
// changes should not be made for the title, stylesheet includes,
// and javascript includes
if ($_FAPP['title_pos'] == 'left') {
	echo "<title>" . Fabriq::title() . " " . Fabriq::config('title_sep') . " " . Fabriq::config('title') . "</title>\n";
} else {
	echo "<title>" . Fabriq::config('title') . " " . Fabriq::config('title_sep') . " " . Fabriq::title() . "</title>\n";
}

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
</head>
<body>

<?php require_once("app/views/" . PathMap::render_controller() . "/" . PathMap::render_action() . ".view.php"); ?>

</body>
</html>