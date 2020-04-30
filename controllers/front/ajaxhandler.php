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
//var_dump($order->getCurrentState());exit;

//        $new_history->changeIdOrderState($idStatusReceived, $order); // 5: delivered
//        $new_history->changeIdOrderState(4, $order); // 5: delivered
//        $new_history->addWithemail(true);
      }

//      $this->context->smarty->assign('order', $order);
      $this->messages[] = 'Order updated: ' . $orderId;
    } else {
      $this->errors[] = Tools::displayError('Error: Invalid order number');
    }
  }
}
