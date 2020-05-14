<?php
/**
 * 2020 Nicolas Jacquemin
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = array(
    'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'maborderconfirm` ( 
      `id_order` INT NOT NULL ,
      `days_7` BOOLEAN NOT NULL ,
      `days_15` BOOLEAN NOT NULL ,
      `days_30` BOOLEAN NOT NULL ,
      PRIMARY KEY (`id_order`))
      ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;',
);

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
