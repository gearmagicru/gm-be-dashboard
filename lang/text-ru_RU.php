<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Информационная панель',
    '{description}' => 'Интерактивная информационная панель (дашборд) с визуализацией данных',
    '{permissions}' => [
        'any'        => ['Полный доступ', 'Просмотр и внесение изменений в виджеты и рабочии столы'],
        'read'       => ['Чтение', 'Только просмотр виджетов и рабочих столов'],
        'widgetInfo' => ['Информация о виджете', 'Просмотр информации о виджете'],
        'info'       => ['Информация', 'Информация о модуле']
    ],

    // Dashboard: панели виджетов
    'Add widget' => 'Добавить виджет',
    'No active panel' => 'Нет активного рабочего стола.',
    'Settings' => 'Настройки',
    'Widget panel settings' => 'Настройки рабочего стола',
    'Are you sure you want to delete the panel?' => 'Вы действительно хотите удалить рабочий стол?',
    'Add panel' => 'Добавить рабочий стол',
    'Customize panel' => 'Настроить рабочий стол',
    'Panel permissions' => 'Права доступа к рабочему столу',
    'Save panel' => 'Сохранить рабочий стол',
    'Delete panel' => 'Удалить рабочий стол',
    'Delete all panels' => 'Удалить все рабочии столы',
    // Dashboard: заголовки
    'Desktop' => 'Рабочий стол',
    // Dashboard: сообщения (ошибки)
    'Error deleting panels' => 'Ошибка удаления рабочих столов.',
    'No to delete widget panels' => 'Нет для удаления рабочих столов.',
    // Dashboard: сообщения
    'Are you sure you want to delete the panels?' => 'Вы действительно хотите удалить все рабочии столы?',
    'Panels and their widgets have been successfully removed' => 'Рабочии столы и их виджеты успешно удалены.',

    // PanelForm: рабочий стол
    '{panel.title}' => 'Добавление рабочего стола',
    '{panel.titleTpl}' => 'Настройка рабочего стола "{name}"',
    // PanelForm: поля
    'Panel' => 'Рабочий стол',
    'Index' => 'Порядок',
    'Index number' => 'Порядковый номер',
    'Name' => 'Название',
    'Enabled' => 'Доступен',
    'Panel column width' => 'Ширина столбцов рабочего стола',
    'Count' => 'Количество',
    'Columns count' => 'Количество столбцов',
    'Column 1' => 'Столбец 1, %',
    'Column 2' => 'Столбец 2, %',
    'Column 3' => 'Столбец 3, %',
    'Column 4' => 'Столбец 4, %',
    // PanelForm: сообщения / заголовки
    'Panel' => 'Рабочий стол',
    'Adding' => 'Добавление',
    'Update' => 'Изменение',
    'Deletion' => 'Удаление',
    // PanelForm: сообщения
    'Panel added successfully' => 'Рабочий стол успешно добавлен.',
    'Error adding panel' => 'Ошибка добавления рабочего стола.',
    'Panel updated successfully' => 'Рабочий стол успешно обнавлён.',
    'Panel update error' => 'Ошибка обновления рабочего стола.',
    'Panel deleted successfully' => 'Рабочий стол успешно удалён.',
    'Deleting panel error' => 'Ошибка удаления рабочего стола.',
    'Panel saved' => 'Рабочий стол сохранён.',
    'Saving panel error' => 'Ошибка сохранения рабочего стола.',

    // WidgetForm: виджет
    '{widget.title}' => 'Добавление виджета "{0}"',
    '{widget.subtitle}' => 'на рабочий стол "{0}"',
    '{widget.titleTpl}' => 'Настройка виджета "{name}"',
    // WidgetForm: поля
    'Widget panel column' => 'Столбец рабочего стола',
    'Customize after adding' => 'Настроить после добавления',
    'Customize after adding (if widget have params)' => 'Настроить после добавления (если виджет имеет параметры)',
    'Widget ID' => 'Идентификатор виджета',
    'Widget panel ID' => 'Идентификатор панели виджетов',
    // WidgetForm: сообщения / заголовки
    'Widget' => 'Виджет',
    // WidgetForm: сообщения (ошибки)
    'Widget not found' => 'Виджет не найден.',
    'Panel not found' => 'Рабочий стол не найден.',
    // WidgetForm: сообщения
    'Widget added successfully' => 'Виджет успешно добавлен.',
    'Error adding widget' => 'Ошибка добавления виджета.',
    'Widget updated successfully' => 'Виджет успешно обнавлён.',
    'Widget update error' => 'Ошибка обновления виджета.',
    'Widget deleted successfully' => 'Виджет успешно удалён.',
    'Deleting widget error' => 'Ошибка удаления виджета.',

    // OptionsForm: настройка виджета
    '{options.title}' => 'Настройка виджета "{0}"',
    // OptionsForm: сообщения / заголовки
    'Widget options' => 'Параметры виджета',
    // OptionsForm: сообщения (ошибки)
    'Widget options not found' => 'Параметры виджета не найдены.',
    // OptionsForm: сообщения
    'Widget options changed successfully' => 'Параметры виджета успешно изменены.',

    // RolesGrid: роли пользователей
    '{roles.title}' => 'Доступ к рабочему столу "{0}" для ролей пользователя',
    // RolesGrid: поля
    'User role availability' => 'Доступность роли пользователя',
    // RolesGrid: сообщения / заголовки
    'Access' => 'Доступ',
    // RolesGrid: сообщения (ошибки)
    'It is not possible to remove access for the user role you have selected because you have this role'
        => 'Невозможно убрать доступ для выбранной вами роли пользователя, т.к. вы имеете эту роль',
    // RolesGrid: сообщения
    'Widget panel for user role {0} - enabled' => 'Для роли пользователя "<b>{0}</b>" рабочий стол - <b>доступен</b>.',
    'Widget panel for user role {0} - disabled' => 'Для роли пользователя "<b>{0}</b>" рабочий стол - <b>не доступен</b>.'
];
