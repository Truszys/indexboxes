<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'indexboxes` (
    `id_box` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(11) NOT NULL,
    `image` varchar(255) NOT NULL,
    `bo_title` varchar(255) NOT NULL,
    `type` varchar(45) NOT NULL,
    `item_id` int(11) NOT NULL,
    `position` int(11) NOT NULL,
    `active` int(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id_box`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'indexboxes_lang` (
    `id_box` int(11) NOT NULL,
    `id_lang` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    PRIMARY KEY  (`id_box`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
