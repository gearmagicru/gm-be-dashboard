<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'gm.be.dashboard',
    'name'        => 'Dashboard',
    'description' => 'Interactive information panel with data visualization',
    'namespace'   => 'Gm\Backend\Dashboard',
    'path'        => '/gm/gm.be.dashboard',
    'route'       => 'dashboard',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'      => 'gm.be.dashboard',
                'route'       => 'dashboard',
                'prefix'      => BACKEND,
                'constraints' => ['id'],
                'defaults'    => [
                    'controller' => 'panels',
                    'action'     => 'view'
                ]
            ]
        ]
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['any', 'read', 'widgetInfo', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM MS'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
    ]
];
