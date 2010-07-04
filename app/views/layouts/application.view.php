<?php
/**
 * @file Application layout. Modify this file for the layout of your website
 * or application. Keep an copy of this file available for websites or
 * applications using multiple layouts.
 * --
 * Copyright (c)2010, Ralivue.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Ralivue.com nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Ralivue.com BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * --
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
	echo "<title>" . Fabriq::title() . " {$_FAPP['title_sep']} {$_FAPP['title']}</title>\n";
} else {
	echo "<title>{$_FAPP['title']} {$_FAPP['title_sep']} " . Fabriq::title() . "</title>\n";
}

// process css queue
foreach (Fabriq::cssqueue() as $css) {
	echo "<link href=\"{$css['path']}{$css['css']}.{$css['ext']}\" media=\"{$css['media']}\" rel=\"stylesheet\" type=\"text/css\" />\n";
}

// process javascript queue
foreach (Fabriq::jsqueue() as $js) {
	echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"{$js['path']}{$js['js']}{$js['ext']}\"></script>\n";
}
?>
<script type="text/javascript">
//<![CDATA[
jQuery.extend(Fabriq.settings, {"basePath": "<?php echo Fabriq::base_path(); ?>", "cleanURLs": <?php echo Fabriq::clean_urls_str(); ?>});
//]]>
</script>
</head>
<body>

<?php require_once("app/views/" .Fabriq::render_controller() . "/" . Fabriq::render_action() . ".view.php"); ?>

</body>
</html>