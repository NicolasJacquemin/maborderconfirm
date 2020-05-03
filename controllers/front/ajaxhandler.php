<?php
/**
 * 2020 Nicolas Jacquemin
 */
class MabOrderConfirmAjaxHandlerModuleFrontController extends ModuleFrontController {
  public $errors = array();
  public $messages = array();
  
  public function init() {
    parent::init();
    
    if (Tools::isSubmit('markAsReceived')) {
      $this->markAsReceived();
    } else {
      $this->errors[] = 'Wrong parameters .' .Tools::getValue('id_order');
    }
    
    if (count($this->errors) > 0) {
      echo Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors));
    } else if (count($this->messages) > 0) {
      echo Tools::jsonEncode(array('hasError' => false, 'messages' => $this->messages));
    } else {
      echo Tools::jsonEncode(array('hasError' => true, 'errors' => ['no error is an error']));
    }

    exit;
  }

  public function markAsReceived() {
    $orderId = (int) (Tools::getValue('id_order'));
    $order = new Order($orderId);
    
    if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
      
      
      
      $idStatusShipped = (int) Configuration::get('MAB_ORDER_CONFIRM_SHIPPED');
      $idStatusReceived = (int) Configuration::get('MAB_ORDER_CONFIRM_RECEIVED');

      if ($order->getCurrentState() == $idStatusShipped) { // if the order is shipped
        $new_history = new OrderHistory();
        $new_history->id_order = (int) $order->id;

//        $new_history->changeIdOrderState($idStatusReceived, $order); // 5: delivered
//        $new_history->changeIdOrderState(4, $order); // 5: delivered
//        $new_history->addWithemail(true);
        //-- TODO make email optional
        
        if ((bool) Configuration::get('MAB_ORDER_CONFIRM_SEND_EMAIL')) {
          $this->sendMail($order, $this->context->customer);
        }
      }

//      $this->context->smarty->assign('order', $order);
      $this->messages[] = 'Order updated: ' . $orderId;
    } else {
      $this->errors[] = Tools::displayError('Error: Invalid order number');
    }
  }
  
  protected function getRecipients() {
    $emails = array();
    $result = Db::getInstance()->executeS('
      SELECT e.email
      FROM `' . _DB_PREFIX_ . 'employee` e,  `' . _DB_PREFIX_ . 'employee_shop` s
      WHERE e.id_employee = s.id_employee 
      AND s.id_shop = ' . (int) $this->context->shop->id
    );
    
    foreach ($result as $r) {
      $emails[] = $r['email'];
    }

    return $emails;
  }
  
  protected function getSenderAddress() {
    //-- $reply_to can be different from sender and also customisable
    return configuration::get('MAB_ORDER_CONFIRM_SENDER_EMAIL');
  }

  protected function getSenderName() {
    $name = configuration::get('MAB_ORDER_CONFIRM_SENDER_NAME');
    
    return (!empty($name)) ? $name : null;
  }

  public function sendMail($order, $customer) {
    $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
    $id_lang = $lang->id;
    $template = 'delivered';
    $subject = 'A customer has received their order';  // TODO customise admin language
    $template_vars = $this->getMailParams($order, $customer);
    $to = $this->getRecipients();
    $to_name = null; //-- Nice to have
    $from = $this->getSenderAddress();
    $from_name = $this->getSenderName();
    $file_attachment = null;
    $mode_smtp = null;
    $template_path = _PS_MODULE_DIR_ . '/maborderconfirm/mails/';
    $die = true;
    $id_shop = null; //-- uses current shop
    $bcc = null; //-- Nice to have customisable
    $reply_to = null; //-- Nice to have customisable
    
    Mail::Send( $id_lang, $template, $subject, $template_vars, $to,
                $to_name, $from, $from_name, $file_attachment, $mode_smtp,
                $template_path, $die, $id_shop, $bcc, $reply_to
            );
  }
  
  protected function getMailParams($order, $customer){
    return array(
        '{id_order}' => $order->id,
        '{firstname}' => $customer->firstname,
        '{lastname}' => $customer->lastname
    );
  }
}
