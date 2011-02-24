<h1>Framework updates</h1>
<?php if (count($toInstall) > 0): ?>
<p>Available framework updates:</p>
<ul>
	<?php foreach($toInstall as $update): ?>
	<li><strong><?php $update['version']; ?>:</strong> <?php $update['description']; ?></li>
	<?php endforeach; ?>
</ul>
<form method="post" action="<?php echo PathMap::build_path('fabriqinstall', 'update', 2); ?>">=
	<p style="text-align: right;">
		<input type="submit" name="submit" value="Install updates and continue &raquo;" />
	</p>
</form>
<?php else: ?>
<p>There are no framework updates available. Continue on to check for module updates.</p>
<p style="text-align: right;">
	<input type="button" value="Next step &raquo;" onclick="window.location = '<?php echo PathMap::build_path('fabriqinstall', 'update', 3); ?>';" />
</p>	
<?php endif; ?>
