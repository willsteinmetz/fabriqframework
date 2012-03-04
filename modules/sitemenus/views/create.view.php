<h1>Create New Menu</h1>
<?php
if (Messaging::has_messages()) {
	Messaging::display_messages();
}

if (!$submitted || ($submitted && Messaging::has_messages())):
?>
<p><a href="<?php echo PathMap::build_path('sitemenus'); ?>">Return to menus list</a></p>
<form method="post" action="<?php echo PathMap::build_path('sitemenus', 'create'); ?>">
	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_name">
			Menu name: <input type="text" name="<?php echo $moduleName; ?>_menuName" size="50" maxlength="50" <?php if ($submitted) { echo "value=\"{$menu->menuName}\""; } ?> />
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_description">
			Description:<br />
			<textarea name="<?php echo $moduleName; ?>_description" rows="5" cols="60"><?php if ($submitted) { echo $menu->description; } ?></textarea>
		</label>
	</div>
	<div style="padding: 2px;">
		<input type="submit" name="submit" value="submit" />
	</div>
</form>
<?php 
else:
	Messaging::display_messages('successes');
?>
<p><a href="<?php echo PathMap::build_path('sitemenus'); ?>">Return to menus list</a></p>
<?php endif; ?>
