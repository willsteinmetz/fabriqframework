<h1>Fabriq Updates</h1>
<p>Version installed: <?php echo $installedVersion; ?></p>
<?php if ($connected): ?>
	<?php if ($upToDate): ?>
<p><strong>Your install of Fabriq is up to date.</strong></p>
<?php
	else:
		foreach ($available as $version => $details):
?>
<p><strong>Version <?php echo $version; ?></strong>, released <?php echo date('F j, Y', strtotime($details['released'])); ?></p>
<ul>
	<li><?php echo str_replace('<br />', '</li><li>', str_replace('<br>', '</li><li>', nl2br($details['changes']))); ?></li>
</ul>
		<?php endforeach; ?>
<p><a href="https://github.com/willsteinmetz/fabriqframework/tags" target="_blank">Download the latest version</a></p>
	<?php endif; ?>
<?php else: ?>
<p>A problem occurred while checking for updates. Please try again later.</p>
<?php endif; ?>