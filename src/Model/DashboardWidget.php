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
use Gm\Helper\Json;
use Gm\Db\ActiveRecord;

/**
 * Активная запись виджета панели (дашборд).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class DashboardWidget extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public array $excludeSetters = ['options' => true];

    /**
     * @inheritdoc
     */
    public array $excludeGetters = ['options' => true];

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
        return '{{panel_dashboard_widgets}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'id'       => 'id', // идентификатор
            'widgetId' => 'widget_id', // идентификатор установленного виджета
            'panelId'  => 'panel_id', // идентификатор панели
            'column'   => 'column', // порядковый номер в столбце
            'index'    => 'index', // порядковый номер в строке
            'options'  => 'options' // настройки вдижета
        ];
    }

    /**
     * Возвращает все панели(ь) виджетов со столбцами и параметрами виджетов добаленными 
     * в них.
     * 
     * @param null|int $panelId Идентификатор панели виджетов. Если значение `null`, 
     *     все панели (по умолчанию `null`).
     * @param null|int $optionsToArray Преобразовывать параметры виджетов в массив в 
     *     виде пар "ключ - значение" перед добавлением в столбец.
     * 
     * @return array
     */
    public function getByPanel(int $panelId = null, bool $optionsToArray = true): array
    {
        /** @var \Gm\Db\Sql\Select $select */
        $select = $this->select(
            [
                'id'       => 'id',
                'widgetId' => 'widget_id',
                'panelId'  => 'panel_id',
                'column'   => 'column',
                'index'    => 'index',
                'options'  => 'options'
            ]
        );
        $select
            ->order('panel_id', 'ASC')
            ->order('column', 'ASC')
            ->order('index', 'ASC');
        if ($panelId) {
            $select->where(['panel_id' => $panelId]);
        }

        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $this
            ->getDb()
                ->createCommand($select)
                    ->query();
        $panels = [];
        while ($widget = $command->fetch()) {
            $panelId = $widget['panelId'];
            // если панель еще не определена
            if (!isset($panels[$panelId])) {
                $panels[$panelId] = [];
            }

            $columnIndex = $widget['column'] - 1;
            // если панель еще не определена
            if (!isset($panels[$panelId][$columnIndex])) {
                $panels[$panelId][$columnIndex] = [];
            }

            if ($optionsToArray) {
                if (empty($widget['options']))
                    $widget['options'] = [];
                else {
                    $options = Json::decode($widget['options']);
                    if (Json::error()) {
                        // TODO: в отладку ошибку
                        $options = [];
                    }
                    $widget['options'] = $options;
                }
            }
            $panels[$panelId][$columnIndex][] = $widget;
        }
        return $panels;
    }

    /**
     * Устанавливает параметры виджетов.
     * 
     * @param null|string|array $options Параметры виджетов.
     * 
     * @return void
     */
    public function setOptions($options)
    {
        if ($options) {
            if (is_array($options))
                $this->options = Json::encode($options);
            else
                $this->options = $options;
        } else
            $this->options = null;
    }

    /**
     * Возвращает параметры виджетов.
     * 
     * @param bool $toArray Если значение `true`, возвратит массив параметров, иначи, 
     *     строка параметров.
     * 
     * @return null|string|array
     */
    public function getOptions(bool $toArray)
    {
        if ($toArray) {
            if ($this->options) {
                if (is_string($this->options)) {
                    return Json::decode($this->options);
                }
            }
            return (array) $this->options;
        }
        return $this->options;
    }

    /**
     * Удаляет все записи.
     * 
     * @throws \Gm\Db\Adapter\Driver\Exception\CommandException Невозможно выполнить инструкцию SQL.
     * 
     * @return void
     */
    public function deleteAll()
    {
        $this->getDb()
            ->createCommand()
                ->truncateTable($this->tableName())
                ->execute();
    }
}
