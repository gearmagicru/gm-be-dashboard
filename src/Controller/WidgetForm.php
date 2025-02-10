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
use Gm\Panel\Widget\Form;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtCombo;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер свойств интерактивной панели.
 * 
 * Создание и редактирование интерактивной панели.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Controller
 * @since 1.0
 */
class WidgetForm extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'WidgetForm';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->ui = 'install';
        $window->cls = 'g-window_install';
        $window->width = 500;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->resizable = false;
        $window->title    = '#{widget.title}';
        $window->titleTpl = '#{widget.titleTpl}';

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->router['route'] = Gm::alias('@match', '/widget');
        $window->form->defaults = [
            'labelWidth' => 210,
            'labelAlign' => 'right',
        ];
        $window->form->setStateButtons(
            Form::STATE_INSERT,
            ['help' => ['subject' => 'widget-form'], 'add', 'cancel']
        );
        $window->form->setStateButtons(
            Form::STATE_UPDATE,
            ['help' => ['subject' => 'widget-form'], 'reset', 'save', 'delete', 'cancel']
        );
        $window->form->bodyPadding = 10;
        $window->form->items = [];

        // если состояние формы "добавление"
        if ($window->form->hasState('insert')) {
            // идентификатор панели виджетов
            $panelId = Gm::$app->request->getPost('panelId', 0, 'int');
            // идентификатор виджета
            $widgetId = Gm::$app->request->getPost('widgetId', 0, 'int');

            /** @var \Gm\Backend\Dashboard\Model\Dashboard $dashboardAR */
            $dashboardAR = $this->getModel('Dashboard');
            /** @var \Gm\Backend\Dashboard\Model\Dashboard $panelAR */
            $panelAR = $dashboardAR->selectByPk($panelId);
            if ($panelAR === null) {
                $this->getResponse()
                    ->meta->error($this->module->t('Panel not found'));
                return false;
            }

            // выпадающий список, только для указанного коли-а столбцов
            $items = [];
            $count = (int) $panelAR->count;
            for ($i = 0; $i < $count; $i++) $items[] = [$i + 1, $i + 1];
            $window->form->items[] = ExtCombo::local(
                '#Widget panel column', 'column',
                $items,
                [
                    'anchor'     => null,
                    'width'      => 320,
                    'value'      => 1,
                    'allowBlank' => false
                ]
            );

            /** @var \Gm\WidgetManager\WidgetManager $widgets */
            $widgets = Gm::$app->widgets;
            /** @var array|null $widget Параметры установленного виджета*/
            $widget = $widgets->getRegistry()->getAt($widgetId);
            if ($widget === null) {
                $this->getResponse()
                    ->meta->error($this->module->t('Widget not found'));
                return false;
            }

            // проверяем существование настроек параметров виджета
            $hasOptions = $widgets->sourceExists($widget['path'], 'Options/Options');
            // добавляем возможность выбора настроить виджет после добавления
            $window->form->items[] = [
                'xtype'      => 'checkbox',
                'ui'         => 'switch',
                'name'       => 'customize',
                'disabled'   => !$hasOptions,
                'checked'    => $hasOptions,
                'fieldLabel' => '#Customize after adding',
                'autoEl'     => [
                    'tag'       => 'div',
                    'data-qtip' => '#Customize after adding (if widget have params)'
                ]
            ];

            // заголовок: название виджета и его панели
            $window->icon = $widgets->getIcon($widget['path'], 'icon');
            $window->title = sprintf('%s <span>%s</span>',
                $this->module->t('{widget.title}', [$widgets->getName($widgetId)]),
                $this->module->t('{widget.subtitle}', [$panelAR->name])
            );
        } else {
            $widgetId = $panelId = 0;
        }

        $window->form->items[] = [
            'xtype' => 'hidden',
            'name'  => 'panelId',
            'value' => $panelId
        ];
        $window->form->items[] = [
            'xtype' => 'hidden',
            'name'  => 'widgetId',
            'value' => $widgetId
        ];
        return $window;
    }

    /**
     * {@inheritdoc}
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var EditWindow|false $widget */
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
     * Действие "content" выводит интерфейса виджета для интерактивной панели.
     * 
     * @return Response
     */
    public function contentAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var int $widgetId Идентификатор виджета панели */
        $widgetId = (int) Gm::$app->router->get('id');
        if (empty($widgetId)) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Invalid argument "{0}"', ['id']));
            return $response;
        }

        /** @var \Gm\Backend\Dashboard\Model\DashboardWidget|null $widgetAR Виджет */
        $widgetAR = $this->getModel('DashboardWidget');
        if ($widgetAR === null) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', ['Widget']));
            return $response;
        }

        /** @var \Gm\Backend\Dashboard\Model\DashboardWidget|null $widgetAR Виджет */
        $widgetAR = $widgetAR->selectByPk((int) $widgetId);
        if ($widgetAR === null) {
            $response
                ->meta->error($this->t('Widget not found'));
            return $response;
        }

        /** @var array $options Параметры виджета */
        $options = $widgetAR->getOptions(true);

        /** @var \Gm\Panel\Dashboard\Widget|null $widget */
        $widget = Gm::$app->widgets->get($widgetAR->widgetId, $options);
        if ($widget === null) {
            $response
                ->meta->error($this->t('Widget not found'));
            return $response;
        }

        $response
            ->setContent($widget->getData());
        return $response;
    }
}
