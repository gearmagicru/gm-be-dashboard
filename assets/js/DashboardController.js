/*!
 * Контроллер представления панелей виджетов.
 * Модуль "Информационная панель".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.dashboard.DashboardController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.gm-be-dashboard',

    /**
     * Обновляет панель виджетов.
     * @param {object} config Параметры виджета.
     */
    updatePanel: function (config = []) {
        let panel = this.getActivePanel(),
            columns = config.items;

        for (i = 0; i < columns.length; i++) {
            panel.items.getAt(i).columnWidth = columns[i].columnWidth;
        }
        panel.setTitle(config.title);
        panel.updateLayout();
    },

    /**
     * Обновляет содержимое виджета.
     * @param {String|Gm.be.dashboard.Panel} widgetId Идентфикатор DOM виджета или сам виджет.
     */
    refreshWidgetContent: function (widgetId) {
        let widget;
        if (Ext.isString(widgetId))
            widget = Ext.getCmp(widgetId);
        else
            widget = widgetId;

        // т.к. при remoteStore сам виджет выполнит запрос
        if (widget.contentType === 'remoteStore')
            widget.updateContent('');
        else
            this.contentWidgetRequest(widget);
    },

    /**
     * Добавляет панель виджетов.
     * @param {Object} config Параметры виджета.
     */
    addPanel: function (config = []) {
        let panels = this.getPanels(),
            panel = panels.add(config);
        panels.setActiveTab(panel);
    },

    /**
     * Добавляет виджет на панель.
     * @param {String} panelId Идентификатор DOM панели.
     * @param {String} widgetId Идентификатор DOM виджета. Если значение null, будет сгенерирован.
     * @param {Number} columnIndex Номер столбца панели.
     * @param {Boolean} customize Настроить виджет после добавления.
     * @param {Object} config Параметры виджета.
     */
    addWidget: function (panelId, widgetId, columnIndex, customize, config) {
        let panel = Ext.getCmp(panelId);

        if (panel) {
            let column = panel.items.getAt(columnIndex);
            if (column) {
                if (widgetId) {
                    config.id = widgetId;
                }
                column.add(config);
                if (customize) {
                    Gm.getApp().widget.load('@backend/dashboard/options/view/' + config.rowId);
                }
            }
        }
    },

    /**
     * Удаляет активную панель виджетов.
     * @param {String} panelId Идентификатор DOM панели.
     */
    removePanel: function (panelId = null) {
        let panel;
        if (panelId === null)
            panel = this.getActivePanel();
        else
            panel = Ext.getCmp(panelId);
        this.getPanels().remove(panel);
    },

    /**
     * Возвращает вкладки панелей виджетов.
     * @return {Ext.tab.Panel}
     */
    getPanels: function () {
        return Ext.getCmp('gm-dashboard-tabs');
    },

    /**
     * Возвращает активную панель виджетов.
     * @return {Gm.be.dashboard.Panel}
     */
    getActivePanel: function () {
        return Ext.getCmp('gm-dashboard-tabs').activeTab;
    },

    /**
     * После отображения виджета.
     * @param {Gm.view.portal.Panel} Виджет.
     * @param {object} eOpts
     */
    onWidgetRender: function (widget, eOpts) {
        if (widget.autoload) {
            this.refreshWidgetContent(widget);
        }
    },

    /**
     * Закрытие панели виджетов.
     * Панель закроется если будет успешен запрос.
     * @param {Gm.be.dashboard.Panel} panel Панель виджетов.
     * @param {object} eOpts
     */
    onClosePanel: function (panel, eOpts) {
        this.removePanelRequest(this.getPanels(), panel);
        return false;
    },

    /**
     * Клик на пункте "Добавить рабочий стол" контекстного меню кнопки.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemPanelAdd: function (item, e) {
        Gm.getApp().widget.load('@backend/dashboard/panel/view');
    },

    /**
     * Клик на пункте "Настроить рабочий стол" контекстного меню кнопки.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemPanelEdit: function (item, e) {
        let tabs = this.getPanels(),
            panel = this.getActivePanel();

        if (tabs.items.length > 0)
            Gm.getApp().widget.load('@backend/dashboard/panel/view/' + panel.rowId);
        else
            Ext.Msg.warning(item.parentMenu.msgNoPanel);
    },

    /**
     * Клик на пункте "Права доступа рабочему столу" контекстного меню кнопки.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemPanelRoles: function (item, e) {
        let tabs = this.getPanels(),
            panel = this.getActivePanel();

        if (tabs.items.length > 0) {
            Gm.getApp().widget.load('@backend/dashboard/roles/view/' + panel.rowId);
        } else
            Ext.Msg.warning(item.parentMenu.msgNoPanel);
    },

    /**
     * Клик на пункте "Сохранить рабочий стол" контекстного меню кнопки.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemPanelSave: function (item, e) {
        let panels = this.getPanels(),
            panel = this.getActivePanel();

        if (panels.items.length > 0) {
            this.savePanelRequest(panel);
        } else
            Ext.Msg.warning(item.parentMenu.msgNoPanel);
    },

    /**
     * Клик на пункте "Удалить рабочий стол" контекстного меню кнопки.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemPanelDelete: function (item, e) {
        let panels = this.getPanels();

        if (panels.items.length > 0)
            this.removePanelRequest(panels, this.getActivePanel());
        else
            Ext.Msg.warning(item.parentMenu.msgNoPanel);
    },

    /**
     * Клик на пункте "Удалить все рабочии столы" контекстного меню кнопки.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemPanelDeletes: function (item, e) {
        let panels = this.getPanels();

        if (panels.items.length > 0) {
            Ext.Msg.confirm(
                Ext.Txt.confirmation,
                item.msgConfirm,
                function (btn, text) {
                    if (btn == 'yes') this.removePanelsRequest(panels);
                },
                this
            );
        } else
            Ext.Msg.warning(item.msgNoPanels);
    },

    /**
     * Клик на кнопке "закрыть" инструмента заголовка виджета.
     * Закрытие виджета.
     * @param {Ext.panel.Tool} tool Кнопка инструмента заголовка.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     * @param {Ext.Component} owner Виджет.
     * @param {Object} eOpts
     */
    onToolWidgetClose: function (tool, e, owner, eOpts) {
        this.removeWidgetRequest(owner.ownerCt);
    },

    /**
     * Клик на кнопке "информация" инструмента заголовка виджета.
     * Информация о виджете через запрос.
     * @param {Ext.panel.Tool} tool Кнопка инструмента заголовка.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     * @param {Ext.Component} owner Виджет.
     * @param {Object} eOpts
     */
    onToolWidgetInfo: function (tool, e, owner, eOpts) {
        Gm.getApp().widget.load('@backend/marketplace/wmanager/winfo?id=' + eOpts.handlerArgs.id);
    },

    /**
     * Клик на кнопке "настройка" инструмента заголовка виджета.
     * Настройка виджета через запрос.
     * @param {Ext.panel.Tool} tool Кнопка инструмента заголовка.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     * @param {Ext.Component} header Заголовок виджета.
     * @param {Object} eOpts
     */
     onToolWidgetSettings: function (tool, e, header, eOpts) {
        Gm.getApp().widget.load('@backend/dashboard/options/view/' + header.ownerCt.rowId);
    },

    /**
     * Клик на кнопке "обновить" инструмента заголовка виджета.
     * Обновляет виджет через запрос.
     * @param {Ext.panel.Tool} tool Кнопка инструмента заголовка.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     * @param {Ext.Component} header Заголовок виджета.
     * @param {Object} eOpts
     */
     onToolWidgetRefresh: function (tool, e, header, eOpts) {
        this.refreshWidgetContent(header.ownerCt);
    },

    /**
     * Вызывает форму добваления виджета в выбранный столбец.
     * @param {Ext.menu.Item} item Пункт контекстного меню.
     * @param {Ext.event.Event} e Событие вызываемое кликом.
     */
    onItemWidgetAdd: function (item, e) {
        let panels = this.getPanels(),
            panel = this.getActivePanel();

        if (panels.items.length > 0)
            Gm.getApp().widget.load(
                '@backend/dashboard/widget/view',
                { panelId: panel.rowId, widgetId: item.handlerArgs.rowId }
            );
        else
            Ext.Msg.warning(item.parentMenu.msgNoPanel);
    },

    /**
     * Запрос обновления содержимого виджета.
     * @param {Gm.view.portal.Panel} widget Виджет.
     */
     contentWidgetRequest: function (widget) {
        widget.mask();
        Ext.Ajax.request({
            url: Gm.url.build('@backend/dashboard/widget/content/' + widget.rowId),
            method: 'post',
            /**
             * Успешное выполнение запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            success: function (response, opts) {
                widget.unmask();
                var response = Gm.response.normalize(response);
                if (!response.success) 
                    Ext.Msg.exception(response, false, true);
                else {
                    widget.updateContent(response.data);
                }
            },
            /**
             * Ошибка запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            failure: function (response, opts) {
                widget.unmask();
                Ext.Msg.exception(response, true);
            }
        });
    },

    /**
     * Запрос удаления виджета.
     * @param {Gm.view.portal.Panel} widget Виджет.
     */
    removeWidgetRequest: function (widget) {
        widget.mask();
        Ext.Ajax.request({
            url: Gm.url.build('@backend/dashboard/widget/delete/' + widget.rowId),
            method: 'post',
            /**
             * Успешное выполнение запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            success: function (response, opts) {
                widget.unmask();
                var response = Gm.response.normalize(response);
                if (!response.success) 
                    Ext.Msg.exception(response, false, true);
                else
                    widget.close();
            },
            /**
             * Ошибка запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            failure: function (response, opts) {
                widget.unmask();
                Ext.Msg.exception(response, true);
            }
        });
    },

    /**
     * Запрос удаления панели виджетов.
     * @param {Ext.tab.Panel} panels Вкладка (дашборд) панелей виджетов.
     * @param {Gm.be.dashboard.Panel} panel Панель виджетов.
     */
    removePanelRequest: function (panels, panel) {
        panels.mask();
        Ext.Ajax.request({
            url: Gm.url.build('@backend/dashboard/panel/delete/' + panel.rowId),
            method: 'post',
            /**
             * Успешное выполнение запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            success: function (response, opts) {
                panels.unmask();
                var response = Gm.response.normalize(response);
                if (!response.success) 
                    Ext.Msg.exception(response, false, true);
            },
            /**
             * Ошибка запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            failure: function (response, opts) {
                panels.unmask();
                Ext.Msg.exception(response, true);
            }
        });
    },

    /**
     * Запрос удаления всех панелей виджетов.
     * @param {Ext.tab.Panel} panels Вкладка (дашборд) панелей виджетов.
     */
     removePanelsRequest: function (panels) {
        panels.mask();
        Ext.Ajax.request({
            url: Gm.url.build('@backend/dashboard/panels/clear'),
            method: 'post',
            /**
             * Успешное выполнение запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            success: function (response, opts) {
                panels.unmask();
                var response = Gm.response.normalize(response);
                if (!response.success) 
                    Ext.Msg.exception(response, false, true);
                else {
                    panels.items.each(function (item, index, len) {
                        item.clearListeners(); // чтобы не было AJAX-запросов
                        item.close();
                    });
                }
            },
            /**
             * Ошибка запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            failure: function (response, opts) {
                panels.unmask();
                Ext.Msg.exception(response, true);
            }
        });
    },

    /**
     * Запрос сохранения панели виджетов.
     * @param {Gm.be.dashboard.Panel} panel Панель виджетов.
     */
    savePanelRequest: function (panel) {
        let widgets = [];
        panel.items.each(function (column, columnIndex) {
            column.items.each(function (widget, widgetIndex) {
                if (Ext.isDefined(widget.rowId)) {
                    widgets.push({
                        rowId: widget.rowId,
                        index: widgetIndex + 1,
                        column: columnIndex + 1
                    });
                }
            });
        });

        panel.mask();
        Ext.Ajax.request({
            url: Gm.url.build('@backend/dashboard/panel/fix/' + panel.rowId),
            method: 'post',
            params: {widgets: Ext.encode(widgets)},
            /**
             * Успешное выполнение запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            success: function (response, opts) {
                panel.unmask();
                var response = Gm.response.normalize(response);
                if (!response.success) 
                    Ext.Msg.exception(response, false, true);
            },
            /**
             * Ошибка запроса.
             * @param {XMLHttpRequest} response Ответ.
             * @param {Object} opts Параметр запроса вызова.
             */
            failure: function (response, opts) {
                panel.unmask();
                Ext.Msg.exception(response, true);
            }
        });
    }
});