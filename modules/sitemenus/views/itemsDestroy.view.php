<h1>Delete Menu item</h1>
<?php if ($submitted): ?>
<p>Menu item "<?php echo $menuItem->itemName; ?>" and its children have been deleted.</p>
<p><a href="<?php echo PathMap::build_path('sitemenus', 'items', 'index', $menu->id); ?>">Return to menu list</a></p>
<?php else: ?>
<form method="POST" action="<?php echo PathMap::build_path('sitemenus', 'items', 'destroy', $menu->id, $menuItem->id); ?>">
<p>Are you sure you want to delete the menu item "<?php echo $menuItem->itemName; ?>" and all of its child items?</p>
<p>
	<input type="submit" name="submitted" value="Delete menu item"/ >
	<a href="<?php echo PathMap::build_path('sitemenus', 'items', 'index', $menu->id); ?>">Cancel</a>
</p>
</form>
<?php endif; ?>