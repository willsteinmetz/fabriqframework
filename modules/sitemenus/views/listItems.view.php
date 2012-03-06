<?php if ($listMenu->menuName != ''): ?>
<nav class="sitemenus-menu" id="sitemenu-<?php echo str_replace(' ', '_', strtolower($listMenu->menuName)); ?>">
	<ul>
	<?php
		for ($i = 0; $i < count($listMenu->items); $i++) {
			echo $listMenu->items[$i]->getItemHtml();
		}
	?>
	</ul>
	<?php if ($clear): ?>
	<div class="clearbox"></div>
	<?php endif; ?>
</nav>
<?php endif; ?>