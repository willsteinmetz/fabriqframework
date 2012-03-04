<?php $found = FabriqModules::get_var('sitemenus', 'found'); ?>
<h1><?php echo Fabriq::title(); ?></h1>
<?php if ($found): ?>
<p>
	<a href="<?php echo PathMap::build_path('sitemenus', 'index'); ?>">Return to menu list</a> |
	<a href="<?php echo PathMap::build_path('sitemenus', 'items', 'create', $menu->id); ?>">Add menu item</a>
</p>
<p><?php echo $menu->description; ?></p>
<?php if (count($menu->items) > 0): ?>
<ul>
	<?php
		for ($i = 0; $i < count($menu->items); $i++) {
			echo $menu->items[$i]->getItemHtml(true);
		}
	?>
</ul>
<?php else: ?>
<p>This menu currently has no items. Use the <strong>Add menu item</strong> link above to add items to the menu.</p>
<?php
	endif;
else:
?>
<p>The menu could not be found.</p>
<p><a href="<?php echo PathMap::map_path('sitemenus', 'index'); ?>">Return to menu list</a></p>
<?php endif; ?>
