<?php if ($menu->menuName != ''): ?>
<nav class="sitemenus-menu" id="sitemenu-<?php echo str_replace(' ', '_', strtolower($menu->menuName)); ?>">
	<ul>
	<?php
		for ($i = 0; $i < count($menu->items); $i++) {
			echo $menu->items[$i]->getItemHtml();
		}
	?>
	</ul>
	<?php if ($clear): ?>
	<div class="clearbox"></div>
	<?php endif; ?>
</nav>
<?php endif; ?>