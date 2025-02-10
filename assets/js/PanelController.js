/*!
 * Контроллер представления формы (добавление / настройка) панели виджетов.
 * Модуль "Информационная панель".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.dashboard.PanelController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-be-dashboard-panel',

    /**
     * Устанавливает поля, которые могут менять ширину столбцов.
     * @param {Count} count Количество столбцов.
     */
    setColumnsCount: function (count) {
        let items = Ext.getCmp(this.view.id + '__fsColumns').items;
        for (let i = 0; i < 4; i++) {
            items.getAt(i).setDisabled(i > count - 1);
        }
    },

    /**
     * Срабатывает, когда выбрано количество столбцов.
     * @param {Ext.form.field.ComboBox} me
     * @param {Ext.data.Model|Ext.data.Model[]} record
     * @param {Object} eOpts
     */
    selectCount: function (me, record, eOpts) {
        let items = Ext.getCmp(this.view.id + '__fsColumns').items,
            count = me.getValue();
        for (let i = 0; i < 4; i++) {
            items.getAt(i).setDisabled(i > count - 1);
        }
    }
});