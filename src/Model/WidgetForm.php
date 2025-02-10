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
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных виджета панели.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class WidgetForm extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_dashboard_widgets}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'], // идентификатор
                ['panel_id', 'alias' => 'panelId', 'label' => 'Widget panel ID'], // порядковый номер
                ['widget_id', 'alias' => 'widgetId', 'label' => 'Widget ID'], // порядковый номер
                ['column', 'label' => 'Column'], // столбец
                ['index', 'label' => 'Index'], // порядковый номер
                ['options'], // 
            ],
            // правила форматирования полей
            'formatterRules' => [
                [['panelId', 'widgetId'], 'type' => ['int']]
            ],
            // правила валидации полей
            'validationRules' => [
                [['panelId', 'widgetId'], 'notEmpty'],
                // количест столбцов
                [
                    'column', 
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
                /** @var \Gm\Panel\Http\Response\JsongMetadata $meta */
                $meta = $this->response()->meta;
                if ($message['success']) {
                    if ($isInsert) {
                        /** @var null|array $params Параметры установленного виджета */
                        $params = Gm::$app->widgets
                            ->getRegistry()
                                ->getAt($this->widgetId);
                        // если виджет не найден
                        if ($params === null) {
                            $meta->error($this->module->t('Widget not found'));
                            return;
                        }

                        /** @var null|\Gm\Panel\Widget\DashboardWidget $widget */
                        $widget = Gm::$app->widgets->get($params['id'], ['rowId' => $result]);

                        // добавить виджет на панель
                        $meta->command(
                            'callControllerMethod',
                            $this->module->viewId('tabs'), // g-dashboard-tabs
                            'addWidget',
                            [
                                $this->module->viewId('panel-' . $this->panelId),
                                $this->module->viewId('widget-' . $result),
                                $this->column - 1, // порядковый номер столбца панели
                                $this->getFormatter() // настроить виджет после добавления
                                    ->doLogic($this->unsafeAttributes['customize'] ?? null, 'customize', true, false),
                                $widget->run()
                            ]
                        );
                    }
                }
                // всплывающие сообщение
                $meta->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                // всплывающие сообщение
                $meta = $this->response()->meta;
                $meta->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionMessages(): array
    {
        return [
            'titleAdd'           => $this->t('Widget'),
            'titleUpdate'        => $this->t('Widget'),
            'titleDelete'        => $this->t('Widget'),
            'msgSuccessAdd'      => $this->t('Widget added successfully'),
            'msgUnsuccessAdd'    => $this->t('Error adding widget'),
            'msgSuccessUpdate'   => $this->t('Widget updated successfully'),
            'msgUnsuccessUpdate' => $this->t('Widget update error'),
            'msgSuccessDelete'   => $this->t('Widget deleted successfully'),
            'msgUnsuccessDelete' => $this->t('Deleting widget error')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
    }
}
