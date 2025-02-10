<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'   => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: read)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Dashboard'   => ['view', 'clear'],
                'PanelForm'   => ['data', 'view', 'update', 'add', 'delete', 'fix'],
                'WidgetForm'  => ['view', 'add', 'update', 'delete', 'put', 'content'],
                'OptionsForm' => ['view', 'update'],
                'RolesGrid'   => ['view', 'data', 'update'],
                'Info'        => ['data', 'view']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Чтение" (read)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Dashboard'  => ['view'],
                'PanelForm'  => ['data', 'view'],
                'WidgetForm' => ['view', 'content']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'viewManager' => [
        'id'        => 'gm-dashboard-{name}',
        'useTheme'  => true,
        'viewMap'   => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
        ]
    ]
];
