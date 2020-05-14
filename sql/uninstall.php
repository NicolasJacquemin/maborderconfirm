<?php
/**
 * 2020 Nicolas Jacquemin
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = array();
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'maborderconfirm`;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
