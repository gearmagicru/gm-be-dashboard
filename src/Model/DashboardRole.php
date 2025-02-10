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
 * Активная запись роли пользователя для панели виджетов (дашборд).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class DashboardRole extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'roleId';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{panel_dashboard_roles}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'panelId' => 'panel_id',
            'roleId'  => 'role_id'
        ];
    }
    /**
     * Возвращает запись по указанному идентификатору, роль пользователя панели виджетов.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param int|string $panelId Идентификатор панели виджетов.
     * @param int $roleId Идентификатор роли пользователя.
     * 
     * @return null|ActiveRecord Активная запись при успешном запросе, иначе `null`.
     */
    public function get($panelId, $roleId)
    {
        return $this->selectOne([
            'panel_id' => $panelId,
            'role_id'  => $roleId
        ]);
    }

    /**
     * Удаляет записи по указанному идентификатору роли пользователя.
     * 
     * @param int $roleId Идентификатор роли пользователя.
     * 
     * @return bool|int Возвращает значение `false`, если ошибка выполнения запроса. 
     *     Иначе, количество удалённых записей.
     */
    public function deleteByRole(int $roleId)
    {
        return $this->deleteRecord(['role_id' => $roleId]);
    }

    /**
     * Удаляет записи по указанному идентификатору панели виджетов.
     * 
     * @param int $panelId Идентификатор панели виджетов.
     * 
     * @return bool|int Возвращает значение `false`, если ошибка выполнения запроса. 
     *     Иначе, количество удалённых записей.
     */
    public function deleteByPanel(int $panelId)
    {
        return $this->deleteRecord(['panel_id' => $panelId]);
    }

    public function addDefaults(int $panelId)
    {
        $rolesId = Gm::userIdentity()->getRoles()->ids(false);
        if ($rolesId) {
            foreach ($rolesId as $roleId) {
                $this->insertRecord([
                    'panel_id' => $panelId,
                    'role_id'  => $roleId
                ]);
            }
        }
    }

    /**
     * Возвращает идентификаторы панелей, доступные по указанным ролям пользователя.
     * 
     * @param null|array $roleId Роль или роли пользователя. Если значение `null`, 
     *     то буду указаны роли текущего пользователя (по умолчанию `null`).
     * 
     * @return array
     */
    public function getPanelsByRole($roleId = null): array
    {
        /** @var \Gm\Db\Sql\Select $select */
        $select = $this->select(['panel_id']);

        if ($roleId) {
            $select->where(['role_id' => $roleId]);
        } else {
            /** @var array $userRoles Роли текущего пользователя */
            $userRoles = Gm::userIdentity()->getRoles()->ids();
            if ($userRoles) {
                $select->where(['role_id' => $userRoles]);
            }
        }
        $select->group('panel_id');

        return $this->getDb()
            ->createCommand($select)
                ->queryColumn();
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
