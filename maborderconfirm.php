<?php
/*
 * 2019-2020 Nicolas Jacquemin
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

require_once(dirname(__FILE__).'/classes/ConfirmationReminder.php');
require_once(dirname(__FILE__).'/classes/OrderConfirmForm.php');

class MabOrderConfirm extends Module {

  public function __construct() {
    $this->name = 'maborderconfirm';
    $this->tab = 'front_office_features';
    $this->version = '0.0.6';
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
    // email configuration
    Configuration::updateValue('MAB_ORDER_CONFIRM_SEND_EMAIL', true);
    Configuration::updateValue('MAB_ORDER_CONFIRM_SENDER_NAME', null);
    Configuration::updateValue('MAB_ORDER_CONFIRM_SENDER_EMAIL', 'no-reply@' . Tools::getHttpHost(false));
    
    if (file_exists($this->local_path.'sql/install.php')) {
        include($this->local_path.'sql/install.php');
    } else {
        return false;
    }

    if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('orderHistory')) {
      return false;
    }

    return true;
  }

  public function uninstall() {
    return parent::uninstall();
  }
  
  public function getContent() {
    $output = '';
    
    if (Tools::isSubmit('maborderconfirm')) {
      $output .= $this->processConfigurationForm();
    } else if (Tools::isSubmit('sendreminder')) {
      $output .= $this->processReminderForm();
    }
    
    $output .= $this->reminderStats();
    $output .= $this->renderForm();
    $output .= $this->showTips();
    
    return $output;
  }
  
  protected function processConfigurationForm() {
    $output = '';
    $errors = array();

    $shp = Tools::getValue('MAB_ORDER_CONFIRM_SHIPPED');
    $rec = Tools::getValue('MAB_ORDER_CONFIRM_RECEIVED');
    $snm = (int)Tools::getValue('MAB_ORDER_CONFIRM_SEND_EMAIL');
    $msn = Tools::getValue('MAB_ORDER_CONFIRM_SENDER_NAME');
    $mse = Tools::getValue('MAB_ORDER_CONFIRM_SENDER_EMAIL');

    if (!Validate::isInt($rec) || $rec <= 0) {
      $errors[] = $this->l('The received status ID is invalid. Please choose an existing ID.');
    }
    
    if (!Validate::isInt($shp) || $shp <= 0) {
      $errors[] = $this->l('The shipped status ID is invalid. Please choose an existing ID.');
    }

    if (empty($msn)) {
      $msn = null;
    }
    
    if (empty($mse)) {
      $mse = 'no-reply@' . Tools::getHttpHost(false);
    } else if (!Validate::isEmail($mse)) {
      $errors[] = $this->l('The sender email format is invalid. Please enter a valid email.');
    }

    if (isset($errors) && count($errors) > 0) {
      $output .= $this->displayError(implode('<br />', $errors));
    } else {
      Configuration::updateValue('MAB_ORDER_CONFIRM_SHIPPED', (int) $shp);
      Configuration::updateValue('MAB_ORDER_CONFIRM_RECEIVED', (int) $rec);
      Configuration::updateValue('MAB_ORDER_CONFIRM_SEND_EMAIL', ($snm === 1));
      Configuration::updateValue('MAB_ORDER_CONFIRM_SENDER_NAME', $msn);
      Configuration::updateValue('MAB_ORDER_CONFIRM_SENDER_EMAIL', $mse);

      $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
    }
    
    return $output;
  }
  
  protected function processReminderForm() {
    ConfirmationReminder::SendReminders();
    return '';
  }

  //-- TODO load the list of statuses
  public function renderForm() {
    $status = OrderConfirmForm::GetStatusForm();
    $email = OrderConfirmForm::GetEmailForm();
    $fields_form = array($status, $email);

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
    
    return $helper->generateForm($fields_form);
  }
  
  protected function showTips() {
    return $this->display(__FILE__, 'views/templates/admin/tips.tpl');
  }
  
  protected function reminderStats() {
    $data = ConfirmationReminder::GetReminderStats();
    $action = Context::getContext()->link->getAdminLink('AdminModules') . '&configure=maborderconfirm&module_name=maborderconfirm&tab_module=front_office_features';
    
    $this->smarty->assign(array(
        'action_url' => $action,
        'data' => $data,
    ));
    
    $output .= $this->display(__FILE__, 'views/templates/admin/send-reminder.tpl');

    return $output;
  }

  public function getConfigFieldsValues() {
    return array(
        'MAB_ORDER_CONFIRM_SHIPPED' => Tools::getValue('MAB_ORDER_CONFIRM_SHIPPED', (int) Configuration::get('MAB_ORDER_CONFIRM_SHIPPED')),
        'MAB_ORDER_CONFIRM_RECEIVED' => Tools::getValue('MAB_ORDER_CONFIRM_RECEIVED', (int) Configuration::get('MAB_ORDER_CONFIRM_RECEIVED')),
        'MAB_ORDER_CONFIRM_SEND_EMAIL' => Tools::getValue('MAB_ORDER_CONFIRM_SEND_EMAIL', (int) Configuration::get('MAB_ORDER_CONFIRM_SEND_EMAIL')),
        'MAB_ORDER_CONFIRM_SENDER_NAME' => Tools::getValue('MAB_ORDER_CONFIRM_SENDER_NAME', Configuration::get('MAB_ORDER_CONFIRM_SENDER_NAME')),
        'MAB_ORDER_CONFIRM_SENDER_EMAIL' => Tools::getValue('MAB_ORDER_CONFIRM_SENDER_EMAIL', Configuration::get('MAB_ORDER_CONFIRM_SENDER_EMAIL')),
    );
  }

  public function getViewParameters() {
    return array(
        'MAB_ORDER_CONFIRM_SHIPPED' => Configuration::get('MAB_ORDER_CONFIRM_SHIPPED'),
        'MAB_ORDER_CONFIRM_RECEIVED' => Configuration::get('MAB_ORDER_CONFIRM_RECEIVED'),
    );
  }

  public function hookDisplayHeader($params) {
    if (floatval(_PS_VERSION_) >= 1.6 && floatval(_PS_VERSION_) < 1.7) {
      $this->context->controller->addJS(($this->_path) . '/views/js/maborderconfirm.js');
      $this->context->controller->addCSS(($this->_path) . 'views/css/maborderconfirm.css');
    }
  }

  public function hookOrderHistory($params) {
    $values = $this->getViewParameters();

    $this->smarty->assign(array(
        'order_id' => is_object($params['order']) ? $params['order']->id : $params['order']['id_order'],
        'order_status' => is_object($params['order']) ? $params['order']->current_state : $params['order']['current_state'],
        'action_url' => $this->context->link->getModuleLink('maborderconfirm', 'ajaxhandler', array(), (bool) Configuration::get('PS_SSL_ENABLED')),
        'id_status_shipped' => $values['MAB_ORDER_CONFIRM_SHIPPED'],
        'id_status_received' => $values['MAB_ORDER_CONFIRM_RECEIVED']
    ));
    
    return $this->display(__FILE__, 'views/templates/maborderconfirm.tpl');
  }
}
