<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Dashboard\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Panel\Controller\BaseController;
use Gm\Backend\Dashboard\Widget\TabDashboard;

/**
 * Контроллер панелей виджетов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Controller
 * @since 1.0
 */
class Dashboard extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultAction = 'view';

    /**
     * Создаёт вкладку с интерактивной панелью (дашборд).
     * 
     * @return TabDashboard
     */
    public function createWidget(): TabDashboard
    {
        /** @var TabDashboard $tab Вкладка интерактивной панели */
        $tab = new TabDashboard();
        $tab->setMeta($this->getResponse()->meta);

        /** 
         * Определяем все доступные виджеты для интерактивной панели.
         * @var array $items Виджеты интерактивной панели (дашборд)
         */
        $items = Gm::$app->widgets->getRegistry()->getListInfo();
        $count = 0;
        foreach ($items as $item) {
            if ($item['category'] === 'dashboard' && $item['enabled']) {
                $count++;
                $tab->panels->addMenuWidgetItem([
                    'text'        => $item['name'],
                    'handler'     => 'onItemWidgetAdd',
                    'icon'        => $item['smallIcon'],
                    'handlerArgs' => ['rowId' => $item['rowId']]
                ]);
            }
        }

        // если виджетов нет
        if ($count === 0) {
            $tab->panels->buttonWidgets['hidden'] = true;
        }

        /** @var \Gm\Mvc\Module\ModulePermission $permission Права доступа к модулю */
        $permission = $this->module->getPermission();

        /** @var bool $canAny Пользователь имеет полный доступ */
        $canAny = $permission->canAny();
        // перетягивание витжетов
        $tab->panels->draggableWidgets = $canAny;
        // возможность зыкрыть виджеты
        $tab->panels->closableWidgets = $canAny;
        // возможность настроить виджет
        $tab->panels->customizeWidgets = $canAny;
        // возможность просмотреть информацию о виджете
        $tab->panels->infoWidgets = $permission->isAllow('widgetInfo', 'any');
        // если нет полного доступа
        if (!$canAny) {
            $tab->panels->hideTabBar();
        }

        /** 
         * Создаём панели виджетов.
         * @var \Gm\Backend\Dashboard\Model\Dashboard|null $model
         */
        $model = $this->module->getModel('Dashboard');
        if ($model) {
            // все доступные панели виджетов
            $panels = $model->getPanels(!$canAny);
            foreach ($panels as $panel) {
                $panel['closable'] = $canAny;

                // если панель виджетов доступна
                if ($canAny || $panel['enabled'] > 0) {
                    $tab->panels->addPanel($panel);
                }
            }
        }

        // скрываем заголовок (длинный)
        $tab->title = '';
        return $tab;
    }

    /**
     * Действие "view" выводит вкладку с интерактивной панелью.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var TabDashboard|false $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }

    /**
     * Действие "clear" удаляет все рабочии столы.
     * 
     * @return Response
     */
    public function clearAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Backend\Dashboard\Model\Dashboard|null $model */
        $model = $this->module->getModel('Dashboard');
        if ($model->deleteAll() !== false) {
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->t('Panels and their widgets have been successfully removed'), $this->t('Deletion'), 'accept');
        } else  {
            $response
                ->meta->error($this->t('Error deleting panels'));
        }
        return $response;
    }
}
