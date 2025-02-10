<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации Карты SQL-запросов.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'drop'   => ['{{panel_dashboard}}', '{{panel_dashboard_roles}}', '{{panel_dashboard_widgets}}'],
    'create' => [
        '{{panel_dashboard}}' => function () {
            return "CREATE TABLE `{{panel_dashboard}}` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `index` int(11) unsigned DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `widgets` text,
                `columns_count` int(11) unsigned DEFAULT NULL,
                `columns_widths` varchar(255) DEFAULT NULL,
                `enabled` tinyint(1) unsigned DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{panel_dashboard_roles}}' => function () {
            return "CREATE TABLE `{{panel_dashboard_roles}}` (
                `panel_id` int(11) unsigned NOT NULL,
                `role_id` int(11) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`panel_id`,`role_id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{panel_dashboard_widgets}}' => function () {
            return "CREATE TABLE `{{panel_dashboard_widgets}}` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `widget_id` int(11) unsigned DEFAULT NULL,
                `panel_id` int(11) unsigned DEFAULT NULL,
                `column` int(11) unsigned DEFAULT '1',
                `index` int(11) unsigned DEFAULT '1',
                `options` text,
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        }
    ],

    'run' => [
        'install'   => ['drop', 'create'],
        'uninstall' => ['drop']
    ]
];