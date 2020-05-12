<?php

class ConfirmationReminder {

  // unused
  public static function CountShippedOrders() {
    $result = Db::getInstance()->executeS('
      SELECT COUNT(o.id_order) as total
      FROM `' . _DB_PREFIX_ . 'orders` o
      WHERE o.current_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED')
    );

    return (count($result) > 0) ? $result[0]['total'] : -1;
  }

  // 🥄
  protected static function GetReminderStatus() {
    $sql = '
      SELECT o.id_order, oc.days_7, oc.days_15, days_30, h.date_add
      FROM `' . _DB_PREFIX_ . 'orders` o
      LEFT JOIN `' . _DB_PREFIX_ . 'maborderconfirm` oc ON o.id_order = oc.id_order
      LEFT JOIN `' . _DB_PREFIX_ . 'order_history` h ON h.id_order = o.id_order
      WHERE o.current_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED') . ' 
      AND h.id_order_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED');
    //echo $sql;exit;
    $result = Db::getInstance()->executeS($sql);
    
    return $result;
  }
  
  
  public static function GetReminderStats() {
    $data = self::GetReminderStatus();
    $now = date_create('now');
    $c7 = $c15 = $c30 = 0;
    $total = count($data);
    foreach ($data as $d) {
      $shippingDate = date_create($d['date_add']);
      $interval = date_diff($now, $shippingDate);
      if ($interval->days > 30 && !(bool) $d['days_30']) {
        $c30++;
      } else if ($interval->days > 15 && !(bool) $d['days_15']) {
        $c15++;
      } else if ($interval->days > 7 && !(bool) $d['days_7']) {
        $c7++;
      } else if ($interval->days < 7) {
        $total--;
      }
    }

    return array(
        'reminder1' => $c7, 
        'reminder2' => $c15,
        'reminder3' => $c30,
        'total' => $total,
    );
  }

  // WIP - site en construction.gif #1997
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
