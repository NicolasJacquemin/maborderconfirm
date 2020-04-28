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
      $idOrder = (int) (Tools::getValue('orderId'));
      $order = new Order($idOrder);

      if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
        //-- TODO make this id customisable
        if ($order->getCurrentState() == 4) { // if the order is shipped
          $new_history = new OrderHistory();
          $new_history->id_order = (int) $order->id;
          //-- TODO make this id customisable
          $new_history->changeIdOrderState(5, $order); // 5: delivered
          $new_history->addWithemail(true);
        }

        $this->context->smarty->assign('order', $order);
      } else {
        $this->_errors[] = Tools::displayError('Error: Invalid order number');
      }
  }
}
