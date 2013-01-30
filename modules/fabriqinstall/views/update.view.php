<?php
switch (PathMap::arg(2)) {
	case 2:
		require_once('app/views/fabriqinstall/update_step2.view.php');
	break;
	case 3:
		require_once('app/views/fabriqinstall/update_step3.view.php');
	break;
	case 4:
		require_once('app/views/fabriqinstall/update_step4.view.php');
	break;
	case 1: default:
		require_once('app/views/fabriqinstall/update_step1.view.php');
	break;
}
?>
