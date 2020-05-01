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
    $this->version = '0.0.2';
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

    if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('orderHistory')) {
      return false;
    }

    return true;
  }

  public function uninstall() {
    return parent::uninstall();
  }
  
  //-- TODO admin form to customise status ID (order is shipped, order is received)
  //-- TODO infobulle to add the hook in the view | {hook h='orderHistory'}
  public function getContent() {
    $output = '';
    $errors = array();
    
    if (Tools::isSubmit('maborderconfirm')) {
      $rec = Tools::getValue('MAB_ORDER_CONFIRM_RECEIVED');
      if (!Validate::isInt($rec) || $rec <= 0) {
        $errors[] = $this->l('The received status ID is invalid. Please choose an existing ID.');
      }

      $shp = Tools::getValue('MAB_ORDER_CONFIRM_SHIPPED');
      if (!Validate::isInt($shp) || $shp <= 0) {
        $errors[] = $this->l('The shipped status ID is invalid. Please choose an existing ID.');
      }

      if (isset($errors) && count($errors) > 0) {
        $output = $this->displayError(implode('<br />', $errors));
      } else {
        Configuration::updateValue('MAB_ORDER_CONFIRM_SHIPPED', (int) $shp);
        Configuration::updateValue('MAB_ORDER_CONFIRM_RECEIVED', (int) $rec);

        $output = $this->displayConfirmation($this->l('Your settings have been updated.'));
      }
    }
    
    return $output . $this->renderForm();
  }
  
  //-- TODO admin form
  //-- TODO load the list of statuses
  public function renderForm() {
    $fields_form = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'description' => $this->l('Check status IDs in Order > Status.'),
//            'description' => $this->l('Don\'t forget to add {hook h=\'orderHistory\'} in your template.'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Shipped status ID'),
                    'name' => 'MAB_ORDER_CONFIRM_SHIPPED',
                    'class' => 'fixed-width-xs',
                    'desc' => $this->l('Set the ID of the shipped status (default: 4).'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Received status ID'),
                    'name' => 'MAB_ORDER_CONFIRM_RECEIVED',
                    'class' => 'fixed-width-xs',
                    'desc' => $this->l('Set the ID of the received status (default: 5).'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        ),
    );

    $helper = new HelperForm();
    $helper->show_toolbar = false;
    $helper->table = $this->table;
    $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
    $helper->default_form_language = $lang->id;
    $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    $this->fields_form = array();
    $helper->id = (int) Tools::getValue('id_carrier');
    $helper->identifier = $this->identifier;
    $helper->submit_action = 'maborderconfirm';
    $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->tpl_vars = array(
        'fields_value' => $this->getConfigFieldsValues(),
        'languages' => $this->context->controller->getLanguages(),
        'id_language' => $this->context->language->id
    );
    
    $echo = $this->announcement();
    $echo .= $helper->generateForm(array($fields_form));

    return $echo;
  }
  
  protected function announcement() {
    $output = '<div class="bootstrap"><div class="module_warning alert">';
    $output .= '<div style="background-color:#abdcb3;color:#353;padding:16px;">WIP - Coming soon, the list of statuses.</div>';
    $output .= '</div></div>';

    return $output;
  }

  public function getConfigFieldsValues() {
    return array(
        'MAB_ORDER_CONFIRM_SHIPPED' => Tools::getValue('MAB_ORDER_CONFIRM_SHIPPED', (int) Configuration::get('MAB_ORDER_CONFIRM_SHIPPED')),
        'MAB_ORDER_CONFIRM_RECEIVED' => Tools::getValue('MAB_ORDER_CONFIRM_RECEIVED', (int) Configuration::get('MAB_ORDER_CONFIRM_RECEIVED')),
    );
  }

  public function hookDisplayHeader($params) {
    if (floatval(_PS_VERSION_) >= 1.6 && floatval(_PS_VERSION_) < 1.7) {
      $this->context->controller->addJS(($this->_path) . '/views/js/maborderconfirm.js');
      $this->context->controller->addCSS(($this->_path) . 'views/css/maborderconfirm.css');
    }
  }

  public function hookOrderHistory($params) {
    $values = $this->getConfigFieldsValues();

    $this->smarty->assign(array(
        'action_url' => $this->context->link->getModuleLink('maborderconfirm', 'ajaxhandler', array(), (bool) Configuration::get('PS_SSL_ENABLED')),
        'id_status_shipped' => $values['MAB_ORDER_CONFIRM_SHIPPED'],
        'id_status_received' => $values['MAB_ORDER_CONFIRM_RECEIVED']
    ));
    
    return $this->display(__FILE__, 'views/templates/maborderconfirm.tpl');
  }
}
