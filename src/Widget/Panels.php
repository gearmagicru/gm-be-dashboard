<?php
/**
 * Этот файл является частью пакета GM Panel.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Dashboard\Widget;

use Gm;
use Gm\Stdlib\Collection;
use Gm\Panel\Widget\Widget;
use Gm\Panel\Http\Response\JsongMetadata;

/**
 * Панели виджетов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Widget
 * @since 1.0
 */
class Panels extends Widget
{
    /**
     * {@inheritdoc}
     */
    public Collection|array $params = [
        /**
         * @var string Уникальный идентификатор виджета для всего приложения.
         */
        'id' => 'tabs',
        /**
         * @var string Короткое название класса виджета.
         */
        'xtype' => 'tabpanel',
        /**
         * @var array|string Вид макета панели.
         */
        'layout' => 'fit',
        /**
         * @var string Класс CSS, который будет добавлен к виджету.
         */
        'cls' => 'gm-dashboard-tabs',
        /**
         * @var array Массив виджетов панели вкладки.
         */
        'items' => [],
        /**
         * @var string Контроллер панели.
         */
        'controller' => 'gm-be-dashboard',
        /**
         * @var string Положение вкладок.
         */
        'tabPosition' => 'top'
    ];

    /**
     * Параметры кнопки "Добавить виджет".
     * 
     * @link https://docs.sencha.com/extjs/5.1.3/api/Ext.button.Button.html
     * 
     * @var array
     */
    public array $buttonWidgets = [];

    /**
     * Параметры кнопки "Настройки".
     * 
     * @link https://docs.sencha.com/extjs/5.1.3/api/Ext.button.Button.html
     * 
     * @var array
     */
    public array $buttonSettings = [];

    /**
     * Возможность перетягивания виджета на панели.
     * 
     * @see Panels::addPanel()
     * 
     * @var bool
     */
    public bool $draggableWidgets = true;

    /**
     * Возможность закрыть (удалить) виджет.
     * 
     * @see Panels::addPanel()
     * 
     * @var bool
     */
    public bool $closableWidgets = true;

    /**
     * Возможность настроить виджет (если виджет имеет настройки).
     * 
     * @see Panels::addPanel()
     * 
     * @var bool
     */
    public bool $customizeWidgets = true;

    /**
     * Возможность просмотреть информацию о виджете.
     * 
     * @see Panels::addPanel()
     * 
     * @var bool
     */
    public bool $infoWidgets = true;

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        $this->buttonWidgets = [
            'xtype'   => 'button',
            'cls'     => 'gm-dashboard__tab-button',
            'iconCls' => 'gm-dashboard__icon-widgets',
            'tooltip' => '#Add widget',
            'margin'  => '0 1px 0 1px',
            'menu'    => [
                'msgNoPanel' => '#No active panel',
                'items' => []
            ]
        ];
        $this->buttonSettings = [
            'xtype'   => 'button',
            'cls'     => 'gm-dashboard__tab-button',
            'iconCls' => 'gm-dashboard__icon-settings',
            'margin'  => '0 1px 0 1px',
            'tooltip' => '#Widget panel settings',
            'menu'    => [
                'msgNoPanel' => '#No active panel',
                'msgConfirm' => '#Are you sure you want to delete the panel?',
                'items' => [
                    [
                        'text'    => '#Add panel',
                        'handler' => 'onItemPanelAdd'
                    ],
                    [
                        'text'    => '#Customize panel',
                        'handler' => 'onItemPanelEdit'
                    ],
                    '-',
                    [
                        'text'    => '#Panel permissions',
                        'handler' => 'onItemPanelRoles'
                    ],
                    [
                        'text'    => '#Save panel',
                        'handler' => 'onItemPanelSave'
                    ],
                    '-',
                    [
                        'text'    => '#Delete panel',
                        'handler' => 'onItemPanelDelete'
                    ],
                    [
                        'text'        => '#Delete all panels',
                        'handler'     => 'onItemPanelDeletes',
                        'msgConfirm'  => '#Are you sure you want to delete the panels?',
                        'msgNoPanels' => '#No to delete widget panels',
                    ]
                ]
            ]
        ];

        // панель компонентов Маркетплейс (Gm.be.mp.catalog.View GmJS)
        $this->tabBar = [
            'layout' => ['pack' => 'end'],
            'height' => 30,
            'items'  => [
                [
                    'xtype' => 'menuseparator',
                    'cls'   => 'gm-dashboard__tab-separator'
                ],
                &$this->buttonWidgets,
                &$this->buttonSettings
            ]
        ];
    }

    /**
     * Метаданные виджета для HTTP-ответа.
     *
     * @var null|JsongMetadata
     */
    protected ?JsongMetadata $meta = null;

    /**
     * Устанавливает метаданные виджета для HTTP-ответа.
     *
     * @param JsongMetadata $meta
     * 
     * @return void
     */
    public function setMeta(JsongMetadata $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * Возвращает метаданные HTTP-ответа.
     *
     * @return JsongMetadata|null
     */
    public function getMeta(): ?JsongMetadata
    {
        return $this->meta;
    }

    /**
     * Скрывает панель кнопок.
     * 
     * @return void
     */
    public function hideTabBar(): void
    {
        $this->tabBar['items'] = null;
    }

    /**
     * Добавляет пункт меню кнопки "Добавить виджет".
     * 
     * @link https://docs.sencha.com/extjs/5.1.3/api/Ext.menu.Item.html
     * 
     * @param array $configs Параметры пункта меню.
     * 
     * @return $this
     */
    public function addMenuWidgetItem(array $configs): static
    {
        $this->buttonWidgets['menu']['items'][] = $configs;
        return $this;
    }

    /**
     * Добавляет пункт меню кнопки "Настройки".
     * 
     * @link https://docs.sencha.com/extjs/5.1.3/api/Ext.menu.Item.html
     * 
     * @param array $configs Параметры пункта меню.
     * 
     * @return $this
     */
    public function addMenuSettingItem(array $configs): static
    {
        $this->buttonSettings['menu']['items'][] = $configs;
        return $this;
    }

    /**
     * Добавляет панель с виджетами.
     * 
     * @param array $params Параметры добавляемой панели {@see \Gm\Backend\Dashboard\Model\Dashboard::getPanels()}.
     * 
     * @return $this
     */
    public function addPanel(array $params): static
    {
        /** @var \Gm\WidgetManager\WidgetManager $manager Менеджер виджетов */
        $manager = Gm::$app->widgets;
        $panel = [
            'id'       => $this->creator->viewId('panel-') . $params['id'],
            'xtype'    => 'dashboard-panel',
            'title'    => $params['name'],
            'rowId'    => $params['id'],
            'closable' => $params['closable'] ?? false,
            'items'    => []
        ];

        // виджеты панели {@see Dashboard::getWidgets()}
        $rows = $params['widgets'] ?? [];
        // ширина столбцов
        $widths = explode('|', $params['columnsWidths']);
        // количество столбцов
        $count = (int) $params['count'];
        // шаблон идент. DOM виджета в представлении
        $widgetViewId = $this->creator->viewId('widget-');

        // добавляем столбцы с виджетами
        for ($columnIndex = 0; $columnIndex < $count; $columnIndex++) {
            $column = [
                'columnWidth' => $widths[$columnIndex] / 100,
                'items'       => []
            ];

            if (isset($rows[$columnIndex])) {
                foreach ($rows[$columnIndex] as $row) {
                    /** @var array Параметры виджета */
                    $options = $row['options'] ?? [];
                    // идентификатор виджета в базе данных
                    $options['rowId'] = $row['id'];
                    // идентификатор DOM виджета после представления
                    $options['id'] = $widgetViewId . $row['id'];
                    // возможность перетягивать виджет
                    if (!$this->draggableWidgets) {
                        $options['draggable'] = false;
                    }
                    // возможность закрыть виджет
                    if (!$this->closableWidgets) {
                        $options['useToolClose'] = false;
                    }
                    // возможность настроить виджет
                    if (!$this->customizeWidgets) {
                        $options['useToolSettings'] = false;
                    }
                    // возможность просмотреть информацию о виджете
                    if (!$this->infoWidgets) {
                        $options['useToolInfo'] = false;
                    }

                    /** @var \Gm\Panel\Widget\DashboardWidget|null $widget */
                    $widget = $manager->create($row['widgetId'], $options);
                    if ($widget) {
                        $column['items'][] = $widget->run();
                    }
                    if ($this->meta) {
                        $this->meta->addWidget($widget);
                    }
                }
            }
            $panel['items'][] = $column;
        }

        $this->params->items[] = $panel;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRender(): bool
    {
        $this->makeViewID();
        return true;
    }
}
