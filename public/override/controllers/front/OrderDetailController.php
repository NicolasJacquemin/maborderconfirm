<?php
/*
 * 2019 - 2020 Nicolas Jacquemin
 */

class OrderDetailController extends OrderDetailControllerCore {

  public function postProcess() {
    parent::postProcess();

    if (Tools::isSubmit('markAsReceived')) {
      $this->markAsReceived();
    }
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

        $new_history->changeIdOrderState($idStatusReceived, $order); // 5: delivered
        $new_history->addWithemail(true);
      }

      $this->context->smarty->assign('order', $order);
    } else {
      $this->_errors[] = Tools::displayError('Error: Invalid order number');
    }
  }
}
