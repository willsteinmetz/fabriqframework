<h1>Module Updates</h1>
<?php
if (Messaging::has_messages()) {
	Messaging::display_messages();
}
?>
<p>Installed modules:</p>
<ul>
<?php for ($i = 0; $i < count($installed); $i++): ?>
	<li><?php echo $installed[$i]['module']; ?> - version <?php echo $installed[$i]['versionInstalled']; ?></li>
<?php endfor; ?>
</ul>
<p>Available updates:</p>
<ul>
<?php
foreach ($available as $module => $updates):
	if (count($updates) > 0):
?>
	<li><?php echo $module; ?>
		<ul>
		<?php foreach ($updates as $update): ?>
			<li><?php echo $update['version']; ?></li>
		<?php endforeach; ?>
		</ul>
	</li>
	<?php else: ?>
	<li><?php echo $module; ?> - No updates available</li>
<?php
	endif;
endforeach;
?>
</ul>
<form method="post" action="<?php PathMap::build_path('fabriqinstall', 'update', 3); ?>">
	<p style="text-align: right;">
		<input type="submit" name="submit" value="Install module updates and continue &raquo;" />
	</p>
</form>
