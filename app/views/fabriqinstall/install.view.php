<?php
switch (PathMap::arg(2)) {
	case 2:
		require_once('app/views/fabriqinstall/install_step2.view.php');
	break;
	case 3:
		require_once('app/views/fabriqinstall/install_step3.view.php');
	break;
	case 4:
		require_once('app/views/fabriqinstall/install_step4.view.php');
	break;
	case 5:
		require_once('app/views/fabriqinstall/install_step5.view.php');
	break;
	case 1: default:
		require_once('app/views/fabriqinstall/install_step1.view.php');
	break;
}
?>
