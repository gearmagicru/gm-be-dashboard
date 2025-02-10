<?php
/**
 * Модуль веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Dashboard;

/**
 * Модуль Информационной панели.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard
 * @since 1.0
 */
class Module extends \Gm\Panel\Module\Module
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.dashboard';

    /**
     * {@inheritdoc}
     */
    public function controllerMap(): array
    {
        return [
            'panels'  => 'Dashboard',
            'panel'   => 'PanelForm',
            'widget'  => 'WidgetForm',
            'options' => 'OptionsForm',
            'roles'   => 'RolesGrid'
        ];
    }
}
