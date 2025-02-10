/*!
 * Класс панели виджетов.
 * Модуль "Информационная панель".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.dashboard.Panel', {
    extend: 'Gm.view.portal.Panel',
    alias: 'widget.dashboard-panel',
    cls:  'g-portal g-panel_background',
    listeners: {
        beforeclose: 'onClosePanel'
    },

    /**
     * @cfg {Numeric} rowId
     * Идентификатор панели в базе данных.
     */
    rowId: 0
});