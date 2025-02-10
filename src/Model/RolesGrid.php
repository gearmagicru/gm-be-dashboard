<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Dashboard\Model;

use Gm\Panel\Data\Model\GridModel;

/**
 * Модель данных списка доступности панелей виджетов ролям пользователей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class RolesGrid extends GridModel
{
    /**
     * Панель виджетов.
     *
     * @var array
     */
    protected $panel = [];

    /**
     * {@inheritdoc}
     * 
     * не задействован, т.к. вся реализация в {@see ItemsGrid::fetchRows()}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_dashboard_roles}}',
            'primaryKey' => 'id',
            'order'      => ['name' => 'asc'],
            'useAudit'   => false,
            'fields'     => [
                ['name'],
                [
                    'panel_id',
                    'alias' => 'panelId'
                ],
                [
                    'role_id',
                    'alias' => 'roleId'
                ],
                ['available']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->panel = $this->module->getStorage()->panel;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSelect(mixed $command = null): void
    {
        $command->bindValues([
            ':panelId' => $this->panel['id'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS `role`.*,`roles`.`panel_id` '
             . 'FROM `{{role}}` `role` '
             . 'LEFT JOIN `{{panel_dashboard_roles}}` `roles` ON `roles`.`role_id`=`role`.`id` AND `roles`.`panel_id`=:panelId';
        return $this->selectBySql($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow(array $row): array
    {
        // доступность роли
        $row['available'] = empty($row['panel_id']) ? 0 : 1;;
        return $row;
    }
}
