<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}

if (!$submitted || ($submitted && Messaging::has_messages())): ?>
<h1>Delete menu?</h1>
<form method="post" action="<?php echo PathMap::build_path('sitemenus', 'destroy', $menu->id); ?>">
	<p>Are you sure you want to delete the menu "<?php echo $menu->menuName; ?>"?</p>
	<p>
		<input type="submit" name="submit" value="Submit" />
		<button type="button" onclick="window.location = '<?php echo PathMap::build_path('sitemenus'); ?>';">Cancel</button>
	</p>
</form>
<?php else: ?>
<h1>Menu deleted</h1>
<?php Messaging::display_messages('successes'); ?>
<p><a href="<?php echo PathMap::build_path('sitemenus'); ?>">Return to menu list</a></p>
<?php endif; ?>
