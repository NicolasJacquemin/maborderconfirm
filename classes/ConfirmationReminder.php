<?php
/*
 * 2019-2020 Nicolas Jacquemin
 */

class ConfirmationReminder {

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
        'c7' => $c7, 
        'c15' => $c15,
        'c30' => $c30,
        'total' => $total,
    );
  }

  // 🥄
  public static function SendReminders() {
    $data = self::GetElligibleToReminderData();
    $now = date_create('now');

    foreach ($data as $d) {
      $shippingDate = date_create($d['date_add']);
      $interval = date_diff($now, $shippingDate);

      if ($interval->days > 30 && !(bool)$d['days_30']) {
        self::SendReminderMail($d['email'], $d['reference'], $d['id_order']);
        self::UpdateReminderHistory($d['id_order'], 1, 1, 1);
      } else if ($interval->days > 15 && !(bool)$d['days_15']) {
        self::SendReminderMail($d['email'], $d['reference'], $d['id_order']);
        self::UpdateReminderHistory($d['id_order'], 1, 1);
      } else if ($interval->days > 7 && !(bool)$d['days_7']) {
        self::SendReminderMail($d['email'], $d['reference'], $d['id_order']);
        self::UpdateReminderHistory($d['id_order'], 1);
      }
    }
  }

  // 🥄
  protected static function GetReminderStatus() {
    $sql = 'SELECT o.id_order, oc.days_7, oc.days_15, days_30, h.date_add
      FROM `' . _DB_PREFIX_ . 'orders` o
      LEFT JOIN `' . _DB_PREFIX_ . 'maborderconfirm` oc ON o.id_order = oc.id_order
      LEFT JOIN `' . _DB_PREFIX_ . 'order_history` h ON h.id_order = o.id_order
      WHERE o.current_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED') . ' 
      AND h.id_order_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED');
    
    $result = Db::getInstance()->executeS($sql);
    
    return $result;
  }
  
  protected static function GetElligibleToReminderData() {
    $sql = 'SELECT c.email, o.id_order, o.reference, h.date_add, oc.days_7, oc.days_15, oc.days_30 
      FROM `' . _DB_PREFIX_ . 'customer` c, `' . _DB_PREFIX_ . 'order_history` h, `' . _DB_PREFIX_ . 'orders` o
      LEFT JOIN `' . _DB_PREFIX_ . 'maborderconfirm` oc ON oc.id_order = o.id_order
      WHERE o.current_state = ' . Configuration::get('MAB_ORDER_CONFIRM_SHIPPED') . '
      AND c.id_customer = o.id_customer
      AND h.id_order = o.id_order 
      AND h.id_order_state = o.current_state;';

    $result = Db::getInstance()->executeS($sql);
    
    foreach ($result as $k => $r) {
      // don't resend if all reminders were sent
      if ((int)$r['days_7'] === 1 &&(int)$r['days_15'] === 1 &&(int)$r['days_30'] === 1) {
        unset($result[$k]);
      }
    }
    
    return $result;
  }
  
  protected static function SendReminderMail($email, $reference, $orderId) {
    $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
    $id_lang = $lang->id;
    $template = 'reminder';
    $subject = 'Confirmez votre commande';  // TODO translate
    $template_vars = array('reference' => $reference, 'orderId' => $orderId);
    $to = $email;
    $to_name = null; //-- Nice to have
    $from = null;
    $from_name = null;
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
  
  protected static function UpdateReminderHistory($orderId, $d7, $d15 = 0, $d30 = 0) {
    $sql = "REPLACE INTO mab_maborderconfirm (id_order, days_7, days_15, days_30)
            VALUES ($orderId, $d7, $d15, $d30);";

    return Db::getInstance()->executeS($sql);
  }
}
