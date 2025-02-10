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
 * Модель данных профиля записи выбора роли пользователя.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Model
 * @since 1.0
 */
class RolesGridRow extends FormModel
{
    /**
     * Идентификатор роли пользователя.
     * 
     * @var int
     */
    protected $roleId;

    /**
     * Идентификатор панели виджета.
     * 
     * @var int
     */
    protected $panelId; 

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_dashboard_roles}}',
            'primaryKey' => 'id',
            'useAudit'   => false,
            'fields'     => [
                ['name'],
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

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                // если успешно добавлен доступ
                if ($message['success']) {
                    // если выбранная роль входит в раздел
                    $available = (int) $this->available > 0;
                    $message['message'] = $this->module->t(
                        'Widget panel for user role {0} - ' . ($available > 0 ? 'enabled' : 'disabled'),
                        [$this->name]
                    );
                    $message['title'] = $this->t('Access');
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $identifier = null): ?static
    {
        // т.к. записи формируются при выводе списка, то нет
        // необходимости делать запрос к бд (нет основной таблицы)
        return $this;
    }

    /**
     * Возвращает идентификатор роли пользователя.
     * 
     * @return int
     */
    public function getRoleId()
    {
        if ($this->roleId === null) {
            $this->roleId = $this->getIdentifier();
        }
        return $this->roleId;
    }

    /**
     * Возвращает идентификатор панели.
     * 
     * @return int
     */
    public function getPanelId()
    {
        if ($this->panelId === null) {
            $store = $this->module->getStorage();
            $this->panelId = isset($store->panel['id']) ? (int) $store->panel['id'] : 0;
        }
        return $this->panelId;
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // если выбранная роль входит в панель
            if ((int) $this->available < 1) {
                /** @var array $rolesId Идент. ролей текущего пользователя */
                $myRolesId = Gm::userIdentity()->getRoles()->ids(false);
                $roleId = $this->getRoleId();
                if (in_array($roleId, $myRolesId)) {
                    $this->addError($this->t('It is not possible to remove access for the user role you have selected because you have this role'));
                    return false;    
                }
            }
        }
        return $isValid;
    }


    /**
     * {@inheritdoc}
     */
    protected function insertProcess(array $attributes = null): false|int|string
    {
        if (!$this->beforeSave(true)) {
            return false;
        }

        $columns = [];
        // если выбранная роль входит в панель
        if ((int) $this->available > 0) {
            $columns = [
                'panel_id' => $this->getPanelId(),
                'role_id'  => $this->getRoleId()
            ];

            // проверяем, добавлена ли ранее запись
            $rows = $this->fetchAll(null, ['*'], $columns);
            if (sizeof($rows) === 0) {
                $this->insertRecord($columns);
            }
            // т.к. ключ составной, то при добавлении всегда будет "0"
            $this->result = 1;
        // если выбранная роль не входит в панель
        } else {
            $this->result = $this->deleteRecord([
                'panel_id' => $this->getPanelId(),
                'role_id'  => $this->getRoleId()
            ]);
        }
        $this->afterSave(true, $columns, $this->result, $this->saveMessage(true, (int) $this->result));
        return $this->result;
    }
}
