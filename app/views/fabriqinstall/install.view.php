<?php
switch (PathMap::arg(2)) {
	case 2:
		require_once('app/views/fabriqinstall/install_step2.view.php');
	break;
	case 1: default:
		require_once('app/views/fabriqinstall/install_step1.view.php');
	break;
}
?>
