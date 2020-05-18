<?php
/*
 * 2019-2020 Nicolas Jacquemin
 */

class OrderConfirmForm {
  static $NAME = 'maborderconfirm';
  static $CLASSNAME = 'maborderconfirm';
  
  protected static function GetStatusList() {
    $lang = Context::getContext()->language->id;
    
    return OrderState::getOrderStates($lang);
  }

  public static function GetStatusForm() {
    $options = array(
        'query' => self::GetStatusList(),
        'id' => 'id_order_state',
        'name' => 'name'
    );

    return array(
        'form' => array(
            'legend' => array(
                'title' => TranslateCore::getModuleTranslation(self::$NAME, 'Status settings', self::$CLASSNAME),
                'icon' => 'icon-cogs'
            ),
            'description' => TranslateCore::getModuleTranslation(self::$NAME, 'Check status IDs in Order > Status.', self::$CLASSNAME),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Shipped status', self::$CLASSNAME),
                    'name' => 'MAB_ORDER_CONFIRM_SHIPPED',
                    'class' => 'fixed-width-xs',
                    'desc' => TranslateCore::getModuleTranslation(self::$NAME, 'Set the ID of the shipped status (default: 4).', self::$CLASSNAME),
                    'options' => $options
                ),
                array(
                    'type' => 'select',
                    'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Received status', self::$CLASSNAME),
                    'name' => 'MAB_ORDER_CONFIRM_RECEIVED',
                    'class' => 'fixed-width-xs',
                    'desc' => TranslateCore::getModuleTranslation(self::$NAME, 'Set the ID of the received status (default: 5).', self::$CLASSNAME),
                    'options' => $options
                ),
            ),
            'submit' => array('title' => TranslateCore::getModuleTranslation(self::$NAME, 'Save', self::$CLASSNAME))
        ),
    );
  }
  
  public static function GetEmailForm() {
    return array(
        'form' => array(
            'legend' => array(
                'title' => TranslateCore::getModuleTranslation(self::$NAME, 'email settings', self::$CLASSNAME),
                'icon' => 'icon-cogs'
            ),
            'description' => TranslateCore::getModuleTranslation(self::$NAME, 'Email sent to admin when a user confirms reception of their order.', self::$CLASSNAME),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Send confirmation email', self::$CLASSNAME),
                    'name' => 'MAB_ORDER_CONFIRM_SEND_EMAIL',
                    'is_bool' => true,
                    'values' => array(// $values contains the data itself.
                        array(
                            'id' => 'send_mail_on',
                            'value' => 1,
                            'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Enabled', self::$CLASSNAME)
                        ),
                        array(
                            'id' => 'send_mail_off',
                            'value' => 0,
                            'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Disabled', self::$CLASSNAME)
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Sender email', self::$CLASSNAME),
                    'name' => 'MAB_ORDER_CONFIRM_SENDER_EMAIL',
                    'class' => 'fixed-width-xxl',
                    'desc' => TranslateCore::getModuleTranslation(self::$NAME, 'The email address for the email sent to admins. Leave blank for $1.', self::$CLASSNAME), //---------- TODO string replace
                ),
                array(
                    'type' => 'text',
                    'label' => TranslateCore::getModuleTranslation(self::$NAME, 'Sender name', self::$CLASSNAME),
                    'name' => 'MAB_ORDER_CONFIRM_SENDER_NAME',
                    'class' => 'fixed-width-xxl',
                    'desc' => TranslateCore::getModuleTranslation(self::$NAME, 'The name for the email sent to admins. Leave blank for no name.', self::$CLASSNAME),
                ),
            ),
            'submit' => array('title' => TranslateCore::getModuleTranslation(self::$NAME, 'Save'), self::$CLASSNAME)
        ),
    );
  }
}

