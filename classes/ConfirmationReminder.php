<?php

class ConfirmationReminder {

  public static function CountShippedOrders() {
    $result = Db::getInstance()->executeS('
      SELECT COUNT(o.id_order) as total
      FROM `' . _DB_PREFIX_ . 'orders` o
      WHERE o.current_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED')
    );

    return (count($result) > 0) ? $result[0]['total'] : -1;
  }

  public static function TotalRecall() {
    // 🥄
    $result = Db::getInstance()->executeS('
      SELECT o.id_order, oc.days_7, oc.days_15, days_30
      FROM `' . _DB_PREFIX_ . 'orders` o
      LEFT JOIN `' . _DB_PREFIX_ . 'maborderconfirm` oc ON o.id_order = oc.id_order
      WHERE o.current_state = 4'
    );
    // TODO get date of history state = 4
    // var_dump($result);exit;

    $days7 = 0;
    $days15 = 0;
    $days30 = 0;

    foreach ($result as $r) {
      if (boolval($r['days_7'])) {
        $days7++;
      }
      if (boolval($r['days_15'])) {
        $days15++;
      }
      if (boolval($r['days_30'])) {
        $days30++;
      }
    }

    return array(
        'days7' => $days7,
        'days15' => $days15,
        'days30' => $days30,
    );
  }

  protected function totalRecallGetEmail() {
    // 🥄

    $emails = array();
    // SELECT DISTINCT c.email
    $result = Db::getInstance()->executeS('
      SELECT c.email, o.id_order
      FROM `' . _DB_PREFIX_ . 'orders` o, `' . _DB_PREFIX_ . 'customer` c
      WHERE o.current_state = 4
      AND c.id_customer = o.id_customer'
    );

    // var_dump($result);exit;

    foreach ($result as $r) {
      $emails[] = $r['email'];
    }

    return $emails;
  }

}
