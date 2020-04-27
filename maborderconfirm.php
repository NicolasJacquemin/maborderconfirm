<?php
/*
 * 2019-2020 Nicolas Jacquemin
 */

if (!defined('_PS_VERSION_'))
  exit;

class MabOrderConfirm extends Module {

  public function __construct() {
    $this->name = 'maborderconfirm';
    $this->tab = 'front_office_features';
    $this->version = '0.0.1';
    $this->author = 'NicolasJacquemin';
    $this->need_instance = 0;

    $this->bootstrap = true;
    parent::__construct();

    $this->displayName = $this->l('Confirm you received your order');
    $this->description = $this->l('Invites users to confirm they have received their order.');
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
  }

  public function install() {
    Configuration::updateValue('MAB_ORDER_CONFIRM_SHIPPED', 4);
    Configuration::updateValue('MAB_ORDER_CONFIRM_RECEIVED', 5);

    if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('hookOrderHistory') || $this->installOrderOverrides()) {
      return false;
    }

    return true;
  }

  public function uninstall() {
    return parent::uninstall();
  }
  
  //-- TODO admin form to customise status ID (order is shipped, order is received)
  public function getContent() {
    $output = '';
    $errors = array();
    
    return $output . $this->renderForm();
  }
  
  //-- TODO admin form
  public function renderForm() {
    return '';
  }

  public function hookDisplayHeader($params) {
    if (floatval(_PS_VERSION_) >= 1.6 && floatval(_PS_VERSION_) < 1.7) {
      $this->context->controller->addJS('modules/' . $this->name . '/views/js/maborderconfirm.js');
      $this->context->controller->addCSS('modules/' . $this->name . '/views/css/maborderconfirm.css');
    }
    $this->context->controller->addJqueryPlugin('bxslider');
  }

  public function hookOrderHistory($params) {
    //-- TODO assign customised status ID
    $this->smarty->assign(array(
        
    ));

    return $this->display(__FILE__, 'root-category.tpl');
  }
  
  /***********************************************************************/
  /****************************** Overrides ******************************/
  /***********************************************************************/
  public function installOrderOverrides() {
    $overrides = array(
        'override/controllers/front/OrderDetailController.php'
    );

    $text_override_must_copy = $this->l('You must copy the file');
    $text_override_at_root = $this->l('at the root of your store');
    $text_override_create_folders = $this->l('Create folders if necessary.');

    foreach ($overrides as $override) {
      if (!$this->existOverride($override)) {
        if (!$this->copyOverride($override)) {
          $text_override = $text_override_must_copy . ' "/modules/' . $this->name . '/public/' . $override . '" '
                  . $text_override_at_root . ' "/' . $override . '". ' . $text_override_create_folders;
          $this->warnings[] = $text_override;
        }
      } else {
        if (!$this->existOverride($override, '/KEY_' . $this->prefix_module . '_' . $this->version . '/')) {
          rename(_PS_ROOT_DIR_ . '/' . $override, _PS_ROOT_DIR_ . '/' . $override . '_BK-' . $this->prefix_module . '-PTS_' . date('Y-m-d'));
          if (!$this->copyOverride($override)) {
            $text_override = $text_override_must_copy . ' "/modules/' . $this->name . '/public/' . $override . '" '
                    . $text_override_at_root . ' "/' . $override . '". ' . $text_override_create_folders;
            $this->warnings[] = $text_override;
          }
        }
      }
    }
  }
  
  protected function copyOverride($file) {
    $source = _PS_MODULE_DIR_ . $this->name . '/public/' . $file;
    $dest = _PS_ROOT_DIR_ . '/' . $file;

    $path_dest = dirname($dest);

    if (!is_dir($path_dest)) {
      if (!mkdir($path_dest, 0777, true)) {
        return false;
      }
    }

    if (@copy($source, $dest)) {
      $path_cache_file = _PS_ROOT_DIR_ . '/cache/class_index.php';
      if (file_exists($path_cache_file)) {
        unlink($path_cache_file);
      }

      return true;
    }

    return false;
  }

  protected function existOverride($filename, $key = false) {
    $file = _PS_ROOT_DIR_ . '/' . $filename;

    if (file_exists($file)) {
      if ($key) {
        $file_content = Tools::file_get_contents($file);
        if (preg_match($key, $file_content) > 0) {
          return true;
        }

        return false;
      }

      return true;
    }

    return false;
  }
}
