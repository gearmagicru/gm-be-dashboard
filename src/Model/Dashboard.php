<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Dashboard\Model;

use Gm;
use Gm\Db\ActiveRecord;

/**
 * Модель данных интерактивной панели (дашборд).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class Dashboard extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{panel_dashboard}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'id'            => 'id', // идентификатор
            'index'         => 'index', // порядковый номер
            'name'          => 'name', // название
            'count'         => 'columns_count', // 
            'columnsWidths' => 'columns_widths', // 
            'enabled'       => 'enabled', // доступность
        ];
    }

    /**
     * Возвращает записи панелей виджетов.
     * 
     * @param bool $accessible Если значение `true`, возвращает только доступные текущей 
     *     роли пользователя панели виджетов (по умолчанию `true`).
     * 
     * @return array
     */
    public function getPanels(bool $accessible = true): array
    {
        /** @var \Gm\Db\Sql\Select $select */
        $select = $this->select($this->maskedAttributes());
        $select->order('index', 'ASC');

        if ($accessible) {
            /** @var DashboardRole $rolesAR */
            $rolesAR = new DashboardRole();
            /** @var array $panelsId Доступные панели для текущего пользователя */
            $panelsId = $rolesAR->getPanelsByRole();

            if ($panelsId)
                // только доступные пользователю панели 
                $select->where(['id' => $panelsId]);
            else
                return [];
        }

        /** @var array $dashboards */
        $dashboards = $this->getDb()
            ->createCommand($select)
                ->queryAll('id');

        /** @var array $widgets Все виджеты панелей */
        $widgets = $this->getWidgets();

        foreach ($dashboards as $id => &$dashboard) {
            if (isset($widgets[$id])) {
                $dashboard['widgets'] = $widgets[$id];
            }
        }
        return $dashboards;
    }

    /**
     * Возвращает записи виджетов панелей.
     * 
     * @see Widget::getByPanel()
     * 
     * @param int|null $panelId Идентификатор панели виджетов. Если значение `null`, 
     *     возвращает все виджеты панелей (по умолчанию `null`).
     * @param bool $optionsToArray
     * 
     * @return array
     */
    public function getWidgets(int $panelId = null, bool $optionsToArray = true): array
    {
        return (new DashboardWidget())->getByPanel($panelId, $optionsToArray);
    }

    /**
     * Удаляет все панели виджетов.
     * 
     * @throws \Gm\Db\Adapter\Driver\Exception\CommandException Невозможно выполнить инструкцию SQL.
     */
    public function deleteAll()
    {
        $this->getDb()
            ->createCommand()
                ->truncateTable($this->tableName())
                ->execute();

        /** @var DashboardWidget $widgetsAR Виджеты панелей */
        $widgetsAR = new DashboardWidget();
        $widgetsAR->deleteAll();

        /** @var DashboardRole $rolesAR Роли пользователей панелей виджетов */
        $rolesAR = new DashboardRole();
        $rolesAR->deleteAll();
    }
}
