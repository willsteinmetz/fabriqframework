<?php
/**
 * @file sitemenus.module.php
 * @author Will Steinmetz
 * Menu module designed for organizing and displaying menus
 */

class sitemenus_module extends FabriqModule {
  function __construct() {
    parent::__construct();
  }

  public function index() {
    if (FabriqModules::module('roles')->requiresPermission('administer menus', 'sitemenus')) {
      $menus = FabriqModules::new_model('sitemenus', 'Menus');
      $menus->getAll();

      FabriqModules::set_var('sitemenus', 'menus', $menus);
      Fabriq::title('Manage menus');
      Fabriq::fabriq_ui_on();
      FabriqModules::add_js('users', 'jquery.validate.min');
      FabriqLibs::js_lib('jquery.tmpl.min', 'jquery/plugins');
      FabriqModules::add_js('sitemenus', 'sitemenus.index');
      FabriqModules::add_css('sitemenus', 'sitemenus');
    }
  }

  public function create() {
    if (FabriqModules::module('roles')->requiresPermission('create menus', 'sitemenus')) {
      Fabriq::title('Create new menu');
      FabriqModules::set_var('sitemenus', 'moduleName', $this->name);

      if (isset($_POST['submit'])) {
        $menu = FabriqModules::new_model('sitemenus', 'Menus');
        $menu->menuName = trim($_POST[$this->name . '_menuName']);
        $menu->description = trim($_POST[$this->name . '_description']);

        if (strlen($menu->menuName) == 0) {
          Messaging::message('You must provide a menu name');
        }
        if (strlen($menu->description) == 0) {
          $menu->description = NULL;
        }

        if (!Messaging::has_messages()) {
          $menu->id = $menu->create();

          Messaging::message("Menu \"{$menu->menuName}\" has been created", 'success');
          FabriqModules::trigger_event('sitemenus', 'create', 'menu created');
        }

        FabriqModules::set_var('sitemenus', 'menu', $menu);
        FabriqModules::set_var('sitemenus', 'submitted', true);
      }
    }
  }

  public function update() {
    if (FabriqModules::module('roles')->requiresPermission('update menus', 'sitemenus')) {
      $menu = FabriqModules::new_model('sitemenus', 'Menus');
      $menu->find(Fabriq\Core\Routing::arg(2));

      if (($menu->count() > 0) && ($menu->menuName != '')) {
        Fabriq::title('Update menu');
        FabriqModules::set_var('sitemenus', 'moduleName', $this->name);

        if (isset($_POST['submit'])) {
          $menu->menuName = trim($_POST[$this->name . '_menuName']);
          $menu->description = trim($_POST[$this->name . '_description']);

          if (strlen($menu->menuName) == 0) {
            Messaging::message('You must provide a menu name');
          }
          if (strlen($menu->description) == 0) {
            $menu->description = NULL;
          }

          if (!Messaging::has_messages()) {
            $menu->update();

            Messaging::message("Menu \"{$menu->menuName}\" has been updated", 'success');
            FabriqModules::trigger_event('sitemenus', 'create', 'menu created');
          }

          FabriqModules::set_var('sitemenus', 'submitted', true);
        }
      } else {
        Fabriq::title('Menu not found');
      }
      FabriqModules::set_var('sitemenus', 'menu', $menu);
    }
  }

  public function destroy() {
    if (FabriqModules::module('roles')->requiresPermission('delete menus', 'sitemenus')) {
      $menu = FabriqModules::new_model('sitemenus', 'Menus');
      $menu->find(Fabriq\Core\Routing::arg(2));

      if (($menu->count() > 0) && ($menu->menuName != '')) {
        Fabriq::title('Delete menu?');
        FabriqModules::set_var('sitemenus', 'moduleName', $this->name);

        if (isset($_POST['submit'])) {
          $menu->destroy();

          Messaging::message('Site menu has been deleted', 'success');

          FabriqModules::set_var('sitemenus', 'submitted', true);
        }
      } else {
        Fabriq::title('Menu not found');
      }
      FabriqModules::set_var('sitemenus', 'menu', $menu);
    }
  }

  public function itemsIndex() {
    if (FabriqModules::module('roles')->requiresPermission('update menus', 'sitemenus')) {
      $menu = FabriqModules::new_model('sitemenus', 'Menus');
      $menu->find(Fabriq\Core\Routing::arg(3));

      if ($menu->menuName != '') {
        Fabriq::title("Menu \"{$menu->menuName}\"");

        $menu->buildMenu();

        FabriqModules::set_var('sitemenus', 'menu', $menu);
        FabriqModules::set_var('sitemenus', 'found', true);
      } else {
        Fabriq::title("Menu not found");
        FabriqModules::set_var('sitemenus', 'found', false);
      }
    }
  }

  public function itemsCreate() {
    if (FabriqModules::module('roles')->requiresPermission('update menus', 'sitemenus')) {
      $menu = FabriqModules::new_model('sitemenus', 'Menus');
      $menu->find(Fabriq\Core\Routing::arg(3));

      if ($menu->menuName != '') {
        Fabriq::title("Add item to menu \"{$menu->menuName}\"");

        $menu->buildMenu();

        FabriqModules::set_var('sitemenus', 'menu', $menu);
        FabriqModules::set_var('sitemenus', 'found', true);
        FabriqModules::set_var('sitemenus', 'moduleName', $this->name);

        if (isset($_POST['submitted'])) {
          $menuItem = FabriqModules::new_model('sitemenus', 'MenuItems');
          $menuItem->itemName = trim($_POST[$this->name . '_itemName']);
          $menuItem->path = trim($_POST[$this->name . '_path']);
          $menuItem->parentItem = $_POST[$this->name . '_parentItem'];
          $menuItem->weight = $_POST[$this->name . '_weight'];
          $menuItem->menu = $menu->id;
          $menuItem->newWindow = (isset($_POST[$this->name . '_newWindow']) && ($_POST[$this->name . '_newWindow'] == 1)) ? 1 : 0;

          if (strlen($menuItem->itemName) == 0) {
            Messaging::message('An item name is required');
          }
          if (strlen($menuItem->path) == 0) {
            $menuItem->path = NULL;
          }
          if ($menuItem->parentItem == '') {
            $menuItem->parentItem = NULL;
          }

          if (!Messaging::has_messages()) {
            $menuItem->id = $menuItem->create();
          }
          FabriqModules::set_var('sitemenus', 'menuItem', $menuItem);
          FabriqModules::set_var('sitemenus', 'submitted', true);
        }
      } else {
        Fabriq::title("Menu not found");
        FabriqModules::set_var('sitemenus', 'found', false);
      }
    }
  }

  public function itemsUpdate() {
    if (FabriqModules::module('roles')->requiresPermission('update menus', 'sitemenus')) {
      $menu = FabriqModules::new_model('sitemenus', 'Menus');
      $menu->find(Fabriq\Core\Routing::arg(3));

      if ($menu->menuName != '') {
        $menuItem = FabriqModules::new_model('sitemenus', 'MenuItems');
        $menuItem->find(Fabriq\Core\Routing::arg(4));

        if ($menuItem->itemName != '') {
          Fabriq::title("Add item to menu \"{$menu->menuName}\"");

          $menu->buildMenu();

          FabriqModules::set_var('sitemenus', 'menu', $menu);
          FabriqModules::set_var('sitemenus', 'found', true);
          FabriqModules::set_var('sitemenus', 'moduleName', $this->name);

          if (isset($_POST['submitted'])) {
            $menuItem->itemName = trim($_POST[$this->name . '_itemName']);
            $menuItem->path = trim($_POST[$this->name . '_path']);
            $menuItem->parentItem = $_POST[$this->name . '_parentItem'];
            $menuItem->weight = $_POST[$this->name . '_weight'];
            $menuItem->menu = $menu->id;
            $menuItem->newWindow = (isset($_POST[$this->name . '_newWindow']) && ($_POST[$this->name . '_newWindow'] == 1)) ? 1 : 0;

            if (strlen($menuItem->itemName) == 0) {
              Messaging::message('An item name is required');
            }
            if (strlen($menuItem->path) == 0) {
              $menuItem->path = NULL;
            }
            if ($menuItem->parentItem == '') {
              $menuItem->parentItem = NULL;
            }

            if (!Messaging::has_messages()) {
              $menuItem->update();
            }
            FabriqModules::set_var('sitemenus', 'submitted', true);
          }
          FabriqModules::set_var('sitemenus', 'menuItem', $menuItem);
        } else {
          Fabriq::title("Menu item not found");
          FabriqModules::set_var('sitemenus', 'found', false);
        }
      } else {
        Fabriq::title("Menu not found");
        FabriqModules::set_var('sitemenus', 'found', false);
      }
    }
  }

  public function itemsDestroy() {
    if (FabriqModules::module('roles')->requiresPermission('update menus', 'sitemenus')) {
      $menu = FabriqModules::new_model('sitemenus', 'Menus');
      $menu->find(Fabriq\Core\Routing::arg(3));

      if ($menu->menuName != '') {
        $menuItem = FabriqModules::new_model('sitemenus', 'MenuItems');
        $menuItem->find(Fabriq\Core\Routing::arg(4));

        if ($menuItem->itemName != '') {
          Fabriq::title("Add item to menu \"{$menu->menuName}\"");

          $menu->buildMenu();

          FabriqModules::set_var('sitemenus', 'menu', $menu);
          FabriqModules::set_var('sitemenus', 'found', true);
          FabriqModules::set_var('sitemenus', 'moduleName', $this->name);

          if (isset($_POST['submitted'])) {
            $menuItem->destroy();
            FabriqModules::set_var('sitemenus', 'submitted', true);
          }
          FabriqModules::set_var('sitemenus', 'menuItem', $menuItem);
        } else {
          Fabriq::title("Menu item not found");
          FabriqModules::set_var('sitemenus', 'found', false);
        }
      } else {
        Fabriq::title("Menu not found");
        FabriqModules::set_var('sitemenus', 'found', false);
      }
    }
  }

  public function listItems($listMenu, $clear = false) {
    $menu = FabriqModules::new_model('sitemenus', 'Menus');
    $menu->getMenuByName($listMenu);
    $menu->buildMenu();
    FabriqModules::set_var('sitemenus', 'listMenu', $menu);
    FabriqModules::set_var('sitemenus', 'clear', $clear);
  }
}
