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
use Gm\Db\Sql\Select;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных панели виджетов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class PanelForm extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_dashboard}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'], // идентификатор
                ['index', 'label' => 'Index',], // порядковый номер
                ['name', 'label' => 'Name'], // название
                ['columns_count', 'alias' => 'count', 'label' => 'Count'], // количест столбцов
                ['columns_widths', 'alias' => 'columnsWidths'], // ширина столбцов
                ['enabled', 'label' => 'Enabled'], // доступность
            ],
            // зависимые записи
            'dependencies' => [
                'deleteAll' => ['{{panel_dashboard_widgets}}', '{{panel_dashboard_roles}}'],
                'delete'    => [
                    '{{panel_dashboard_widgets}}' => ['panel_id' => 'id'],
                    '{{panel_dashboard_roles}}'   => ['panel_id' => 'id']
                ]
            ],
            // правила форматирования полей
            'formatterRules' => [
                ['name', 'safe'],
                ['enabled', 'logic']
            ],
            // правила валидации полей
            'validationRules' => [
                [['name'], 'notEmpty'],
                // порядковый номер
                [
                    'index', 
                    'between',
                    'min' => 1, 'max' => PHP_INT_MAX
                ],
                // количест столбцов
                [
                    'count', 
                    'between',
                    'min' => 1, 'max' => 4
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                // всплывающие сообщение
                $meta = $this->response()->meta;
                $meta->cmdPopupMsg($message['message'], $message['title'], $message['type']);

                if ($message['success']) {
                    if ($isInsert) {
                        /** 
                         * @see Panel::getPanelOptions() 
                         * @var int $id Уникальный идентификатор панели виджетов
                         **/
                        $this->id = (int) $result;

                        /** @var DashboardRole */
                        $roleAR = new DashboardRole();
                        // добавляет роли текущего пользователя для доблавленной панели виджетов
                        $roleAR->addDefaults($this->id);
                    }

                    // добавить вкладку панели виджетов на стороне клиента
                    $meta->command(
                        'callControllerMethod',
                        $this->module->viewId('tabs'), // g-dashboard-tabs
                        $isInsert ? 'addPanel' : 'updatePanel',
                        [$this->getPanelOptions()]
                    );
                }
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                // всплывающие сообщение
                $meta = $this->response()->meta;
                $meta->cmdPopupMsg($message['message'], $message['title'], $message['type']);

                if ($message['success']) {
                    // удлаить вкладку панели виджетов на стороне клиента
                    $meta->command(
                        'callControllerMethod',
                        $this->module->viewId('tabs'), // g-dashboard-tabs
                        'removePanel',
                        [
                            $this->module->viewId('panel-' . $this->getIdentifier()) // g-dashboard-panel-id
                        ]
                    );
                }
            });
    }

    /**
     * Сохраняет (фиксирует) состояния панели виджетов.
     *
     * @param array $widgets Виджеты, положение которых необходимо сохранить.
     *     Например `['rowId' => 1, 'index' => 1, 'column' => 1]`, где:
     *     - 'rowId', уникальный идентификатор виджета на панели виджетов;
     *     - 'index', порядковый номер в столбце (всегда больше 0);
     *     - 'column', порядковый номер столбца (всегда больше 0).
     * @param int|null $panelId Идентификатор панели виджетов (по умолчанию `null`).
     *     Если значение `null`, идентификатор будет взят из запроса {@see Panel::getIdentifier()}.
     * 
     * @return bool
     */
    public function fix(array $widgets, int $panelId = null): bool
    {
        if (empty($widgets)) return true;

        if ($panelId === null) {
            $panelId = $this->getIdentifier();
        }

        /** @var Select $select */
        $select = new Select('{{panel_dashboard_widgets}}');
        $select
            ->columns(['id', 'column', 'index'])
            ->where(['panel_id' => $panelId]);
        /** @var array $oldWidgets Все добавленные ранее виджеты указанной панели */
        $oldWidgets = $this->getDb()
            ->createCommand($select)
                ->queryAll('id');
        
        /** @var \Gm\Backend\Dashboard\Model\DashboardWidget|null $dashboardWidgetAR Модель данных виджета */
        $dashboardWidgetAR = $this->module->getModel('DashboardWidget');
        foreach ($widgets as $widget) {
            $rowId = $widget['rowId'];
            if (isset($oldWidgets[$rowId])) {
                $oldWidget = $oldWidgets[$rowId];
                // если виджет изменил положение
                if ($oldWidget['column'] != $widget['column'] || $oldWidget['index'] != $widget['index']) {
                    $dashboardWidgetAR->updateRecord(
                        ['column' => $widget['column'], 'index' => $widget['index']],
                        ['id' => $rowId]
                    );
                }
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoad(array &$data): void
    {
        if (isset($data['columns'])) {
            $columns = [];
            for ($i = 0; $i < 4; $i++) {
                if (isset($data['columns'][$i]))
                    $columns[] = (int) $data['columns'][$i];
                else
                    $columns[] = 0;
            }
            $data['columnsWidths'] = implode('|', $columns);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionMessages(): array
    {
        return [
            'titleAdd'           => $this->t('Panel'),
            'titleUpdate'        => $this->t('Panel'),
            'titleDelete'        => $this->t('Panel'),
            'msgSuccessAdd'      => $this->t('Panel added successfully'),
            'msgUnsuccessAdd'    => $this->t('Error adding panel'),
            'msgSuccessUpdate'   => $this->t('Panel updated successfully'),
            'msgUnsuccessUpdate' => $this->t('Panel update error'),
            'msgSuccessDelete'   => $this->t('Panel deleted successfully'),
            'msgUnsuccessDelete' => $this->t('Deleting panel error')
        ];
    }

    /**
     * Возвращает параметры конфигурации, используемые для добавления и изменения 
     * панели виджетов на стороне клиента.
     *
     * @return array
     */
    protected function getPanelOptions(): array
    {
        $items  = [];

        $columns = explode('|', $this->columnsWidths);
        foreach ($columns as $width) {
            $width = (int) $width;
            if ($width === 0) continue;

            $items[] = ['columnWidth' => $width > 0 ? round($width / 100, 2) : 0];
        }
        return [
            'id'    => $this->module->viewId('panel-' . $this->id),
            'xtype' => 'dashboard-panel',
            'rowId' => $this->id,
            'title' => $this->name,
            'items' => $items
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        /** @var \Gm\Panel\Http\Response $response */
        $response = $this->response();
        /** @var \Gm\Panel\Http\Response\JsongMetadata $meta */
        $meta = $response->meta;

        if ($this->columnsWidths) {
            $columns = explode('|', $this->columnsWidths);
            foreach ($columns as $index => $width) {
                $this->attributes['columns[' . $index . ']'] = $width;
            }
        }

        // устанавливаем количество полей, которые могут изм-ь ширину столбцов
        $meta->command(
            'callControllerMethod',
            $this->module->viewId('form'), // g-dashboard-tabs
            'setColumnsCount',
            [$this->count]
        );
    }
}
