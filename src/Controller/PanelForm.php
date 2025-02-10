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
use Gm\Helper\Json;
use Gm\Panel\Widget\Form;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtCombo;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер панели виджетов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Controller
 * @since 1.0
 */
class PanelForm extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'PanelForm';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // генерируем DOM ID для формы, чтобы сейчас его использовать
        $window->form->makeViewID();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 500;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->resizable = false;
        $window->title    = '#{panel.title}';
        $window->titleTpl = '#{panel.titleTpl}';

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->router['route'] = Gm::alias('@match', '/panel');
        $window->form->defaults = [
            'labelWidth' => 105,
            'labelAlign' => 'right',
        ];
        $window->form->bodyPadding = 10;
        $window->form->controller = 'gm-be-dashboard-panel';
        $window->form->setStateButtons(
            Form::STATE_INSERT,
            ['help' => ['subject' => 'desktop-form'], 'add', 'cancel']
        );
        $window->form->setStateButtons(
            Form::STATE_UPDATE,
            ['help' => ['subject' => 'desktop-options'], 'reset', 'save', 'delete', 'cancel']
        );
        $window->form->items = [
            [
                'xtype'      => 'textfield',
                'fieldLabel' => '#Name',
                'name'       => 'name',
                'anchor'     => '100%',
                'maxLength'  => 255,
                'value'      => '#Panel',
                'allowBlank' => false
            ],
            [
                'xtype'      => 'numberfield',
                'fieldLabel' => '#Index',
                'tooltip'    => '#Index number',
                'name'       => 'index',
                'width'      => 200,
                'minValue'   => 1,
                'maxValue'   => 20,
                'value'      => 1,
                'editable'   => true
            ],
            ExtCombo::local(
                '#Count', 'count',
                [['1', '1'], ['2', '2'], ['3', '3'], ['4', '4']],
                [
                    'anchor'    => null,
                    'width'     => 200,
                    'value'     => 4,
                    'tooltip'   => '#Columns count',
                    'listeners' => ['select' => 'selectCount'],
                    'disabled'  => $window->form->hasState('update'),
                ]
            ),
            [
                'id'       => $window->form->id . '__fsColumns', // g-dashboard-form__fsColumns
                'xtype'    => 'fieldset',
                'title'    => '#Panel column width',
                'defaults' => [
                    'xtype'      => 'numberfield',
                    'width'      => 190,
                    'minValue'   => 0,
                    'maxValue'   => 100,
                    'value'      => 25,
                    'labelWidth' => 95,
                    'labelAlign' => 'right',
                ],
                'items' => [
                    [
                        'fieldLabel' => '#Column 1',
                        'name'       => 'columns[0]'
                    ],
                    [
                        'fieldLabel' => '#Column 2',
                        'name'       => 'columns[1]'
                    ],
                    [
                        'fieldLabel' => '#Column 3',
                        'name'       => 'columns[2]'
                    ],
                    [
                        'fieldLabel' => '#Column 4',
                        'name'       => 'columns[3]'
                    ]
                ]
            ],
            [
                'xtype'      => 'checkbox',
                'fieldLabel' => '#Enabled',
                'name'       => 'enabled',
                'ui'         => 'switch',
                'value'      => true
            ]
        ];
        $window->addRequire('Gm.be.dashboard.PanelController');
        return $window;
    }

    /**
     * Действие "fix" сохраняет (фиксирует) состояния панели виджетов.
     * 
     * @return Response
     */
    public function fixAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request  = Gm::$app->request;

        /** @var string $widgets Положение виджетов на панеле виджетов */
        $widgets = $request->post('widgets', '');
        if ($widgets === '') {
            $response
                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', ['widgets']));
            return $response;
        }

        /** @var false|array $widgets */
        $widgets = Json::tryDecode($widgets);

        /** @var \Gm\Backend\Dashboard\Model\PanelForm|null $panel Модель данных панели виджетов */
        $panel = $this->getModel($this->defaultModel);
        if ($panel === null) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        // сохранение положения виджетов панели
        if ($panel->fix($widgets))
            $response
                ->meta->cmdPopupMsg($this->t('Panel saved'), $this->t('Panel'), 'accept');
        else
            $response
                ->meta->cmdPopupMsg($this->t('Saving panel error'), $this->t('Panel'), 'error');
        return $response;
    }
}
