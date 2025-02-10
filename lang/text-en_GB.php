<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Dashboard',
    '{description}' => 'Interactive information panel with data visualization',
    '{permissions}' => [
        'any'        => ['Full access', 'View and make changes to widgets and desktops'],
        'read'       => ['Read', 'Only view widgets and desktops'],
        'widgetInfo' => ['Widget information', 'Viewing information about a widget'],
        'info'       => ['Information', 'Module information']
    ],

    // Dashboard: панели виджетов
    'Add widget' => 'Add widget',
    'No active panel' => 'No active panel.',
    'Settings' => 'Settings',
    'Widget panel settings' => 'Widget panel settings',
    'Are you sure you want to delete the panel?' => 'Are you sure you want to delete the panel?',
    'Add panel' => 'Add panel',
    'Customize panel' => 'Customize panel',
    'Panel permissions' => 'Panel permissions',
    'Save panel' => 'Save panel',
    'Delete panel' => 'Delete panel',
    'Delete all panels' => 'Delete all panels',
    // Dashboard: заголовки
    'Desktop' => 'Desktop',
    // Dashboard: сообщения (ошибки)
    'Error deleting panels' => 'Error deleting panels.',
    'No to delete widget panels' => 'No to delete widget panels.',
    // Dashboard: сообщения
    'Are you sure you want to delete the panels?' => 'Are you sure you want to delete the panels?',
    'Panels and their widgets have been successfully removed' => 'Panels and their widgets have been successfully removed.',

    // PanelForm: рабочий стол
    '{panel.title}' => 'Add a desktop',
    '{panel.titleTpl}' => 'Desktop settings "{name}"',
    // PanelForm: поля
    'Panel' => 'Panel',
    'Index' => 'Index',
    'Index number' => 'Index number',
    'Name' => 'Name',
    'Enabled' => 'Enabled',
    'Panel column width' => 'Panel column width',
    'Count' => 'Count',
    'Columns count' => 'Columns count',
    'Column 1' => 'Column 1, %',
    'Column 2' => 'Column 2, %',
    'Column 3' => 'Column 3, %',
    'Column 4' => 'Column 4, %',
    // PanelForm: сообщения / заголовки
    'Panel' => 'Panel',
    'Adding' => 'Adding',
    'Update' => 'Update',
    'Deletion' => 'Deletion',
    // PanelForm: сообщения
    'Panel added successfully' => 'Panel added successfully.',
    'Error adding panel' => 'Error adding panel.',
    'Panel updated successfully' => 'Panel updated successfully.',
    'Panel update error' => 'Panel update error.',
    'Panel deleted successfully' => 'Panel deleted successfully.',
    'Deleting panel error' => 'Deleting panel error.',
    'Panel saved' => 'Panel saved.',
    'Saving panel error' => 'Saving panel error.',

    // WidgetForm: виджет
    '{widget.title}' => 'Add widgetr"{0}"',
    '{widget.subtitle}' => 'on the panel "{0}"',
    '{widget.titleTpl}' => 'Widget settings "{name}"',
    // WidgetForm: поля
    'Widget panel column' => 'Widget panel column',
    'Customize after adding' => 'Customize after adding',
    'Customize after adding (if widget have params)' => 'Customize after adding (if widget have params)',
    'Widget ID' => 'Widget ID',
    'Widget panel ID' => 'Widget panel ID',
    // WidgetForm: сообщения / заголовки
    'Widget' => 'Widget',
    // WidgetForm: сообщения (ошибки)
    'Widget not found' => 'Widget not found.',
    'Panel not found' => 'Panel not found.',
    // WidgetForm: сообщения
    'Widget added successfully' => 'Widget added successfully.',
    'Error adding widget' => 'Error adding widget.',
    'Widget updated successfully' => 'Widget updated successfully.',
    'Widget update error' => 'Widget update error.',
    'Widget deleted successfully' => 'Widget deleted successfully.',
    'Deleting widget error' => 'Deleting widget error.',

    // OptionsForm: настройка виджета
    '{options.title}' => 'Widget settings "{0}"',
    // OptionsForm: сообщения / заголовки
    'Widget options' => 'Widget options',
    // OptionsForm: сообщения (ошибки)
    'Widget options not found' => 'Widget options not found.',
    // OptionsForm: сообщения
    'Widget options changed successfully' => 'Widget options changed successfully.',

    // RolesGrid: роли пользователей
    '{roles.title}' => 'Access to panel "{0}" for user roles',
    // RolesGrid: поля
    'User role availability' => 'User role availability',
    // RolesGrid: сообщения / заголовки
    'Access' => 'Access',
    // RolesGrid: сообщения (ошибки)
    'It is not possible to remove access for the user role you have selected because you have this role'
        => 'It is not possible to remove access for the user role you have selected because you have this role',
    // RolesGrid: сообщения
    'Widget panel for user role {0} - enabled' => 'Widget panel for user role "<b>{0}</b>" - <b>enabled</b>.',
    'Widget panel for user role {0} - disabled' => 'Widget panel for user role "<b>{0}</b>" - <b>disabled</b>.'
];