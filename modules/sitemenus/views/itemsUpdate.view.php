<?php if (!$found): ?>
<h1>Menu item not found</h1>
<p><a href="<?php echo PathMap::build_path('sitemenus', 'index'); ?>">Return to menu list</a></p>
<?php else: ?>
<h1>Update menu item</h1>
<?php
if (Messaging::has_messages()) {
	Messaging::display_messages();
}

if (!$submitted || ($submitted && Messaging::has_messages())):
?>
<p><a href="<?php echo PathMap::build_path('sitemenus', 'items', 'index', $menu->id); ?>">Return to menu items list</a></p>
<form method="post" action="<?php echo PathMap::build_path('sitemenus', 'items', 'update', $menu->id, $menuItem->id); ?>">
	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_itemName">
			<strong>Item name:</strong>
		</label>
		<input type="text" name="<?php echo $moduleName; ?>_itemName" size="35" maxlength="100" value="<?php echo $menuItem->itemName; ?>" />
	</div>
	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_path">
			<strong>Path:</strong>
		</label>
		<input type="text" name="<?php echo $moduleName; ?>_path" size="35" maxlength="100" value="<?php echo $menuItem->path; ?>" />
		<p style="font-size: 9pt; color: #666;">For paths local to this web site/application, the system can automatically map them. Start the path with a single / and do <strong>NOT</strong> include the application path (The application path is the part of the URL between your domain name and the start of requests if your web site/application is not at the root of the URL. ex: if the site/application was located at http://example.com/apppath, a path for http://example.com/apppath/blog/view/1 would be /blog/view/1).</p>
	</div>

	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_weight">
			<strong>Weight:</strong>
		</label>
		<select name="<?php echo $moduleName; ?>_weight">
		<?php
		for ($i = -20; $i <= 20; $i++) {
			echo "\t\t\t<option value=\"{$i}\"";
			if ($i == $menuItem->weight) {
				echo ' selected="selected"';
			}
			echo ">{$i}</option>\n";
		}
		?>
		</select>
		<p style="font-size: 9pt; color: #666;">The lower the weight, the higher an item is in the menu.</p>
	</div>
	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_parentItem">
			<strong>Parent item:</strong>
		</label>
		<select name="<?php echo $moduleName; ?>_parentItem">
			<option value="">Top level</option>
			<?php
				for ($i = 0; $i < count($menu->items); $i++) {
					echo $menu->items[$i]->getItemSelectOption(0, $menuItem->parentItem, $menuItem->id);
				}
			?>
		</select>
	</div>
	<div style="padding: 2px;">
		<label for="<?php echo $moduleName; ?>_newWindow">
			<strong>Open in new window:</strong>
		</label>
		<input type="checkbox" value="1" name="<?php echo $moduleName; ?>_newWindow"<?php if ($menuItem->newWindow) { echo ' checked="checked"'; } ?> />
	</div>
	<div style="padding: 2px;">
		<input type="submit" name="submitted" value="Update item" />
	</div>
</form>
<?php else: ?>
<p>Menu item "<?php echo $menuItem->itemName; ?>" has been updated.</p>
<p><a href="<?php echo PathMap::build_path('sitemenus', 'items', 'index', $menu->id); ?>">Return to menu items list</a></p>
<?php
	endif;
endif;
?>
