<h1>Menus</h1>
<p><a href="<?php echo PathMap::build_path('sitemenus', 'create'); ?>">Create new menu</a></p>
<?php if ($menus->count() > 0): ?>
<table border="0" id="sitemenus-index" cellspacing="0">
	<thead>
		<tr>
			<th>Menu</th>
			<th>Description</th>
			<th>Edit</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
	<?php for ($i = 0; $i < $menus->count(); $i++): ?>
		<tr<?php if (($i % 2) == 1) { echo ' class="stripe"'; } ?>>
			<td>
				<a href="<?php echo PathMap::build_path('sitemenus', 'items', 'index', $menus[$i]->id); ?>"><?php echo $menus[$i]->menuName; ?></a>
			</td>
			<td><?php echo $menus[$i]->description; ?></td>
			<td style="text-align: center;"><button type="button" onclick="window.location = '<?php echo PathMap::build_path('sitemenus', 'update', $menus[$i]->id); ?>';">Edit</button></td>
			<td style="text-align: center;"><button type="button" onclick="window.location = '<?php echo PathMap::build_path('sitemenus', 'destroy', $menus[$i]->id); ?>';">Delete</button></td>
		</tr>
	<?php endfor; ?>
	</tbody>
</table>
<?php else: ?>
<p><strong>No menus have been created yet.</strong></p>
<?php endif; ?>
