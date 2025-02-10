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
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtForm;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер настройки параметров виджета.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Controller
 * @since 1.0
 */
class OptionsForm extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Dashboard\Module
     */
    public BaseModule $module;

    /**
     * Действие "view" выводит интерфейс окна настроек параметров виджета.
     * 
     * @return Response
     */
    public function viewAction(): Response
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

        /** @var null|array Параметры установленного виджета */
        $widgetParams = Gm::$app->widgets->getRegistry()->getAt($widgetAR->widgetId);
        if ($widgetParams === null) {
            $response
                ->meta->error($this->t('Widget not found'));
            return $response;
        }

        // т.к. виджет самостоятельно не может подключать свою локализацию (в данном случаи делает это модуль), 
        // то добавляем шаблон локализации виджета модулю
        $category = Gm::$app->translator->getCategory($this->module->id);
        $category->patterns['widget'] = [
            'basePath' => Gm::$app->modulePath . $widgetParams['path'] . DS . 'lang',
            'pattern'  => 'text-%s.php',
        ];
        $this->module->addTranslatePattern('widget');

        /** @var \Gm\Panel\Widget\OptionsWindow $widgetOptions */
        $optionsWindow = Gm::$app->widgets->getObject('Options\Options', $widgetAR->widgetId, [
            'options' => $widgetAR->getOptions(true)
        ]);
        if ($optionsWindow === null) {
            $response
                ->meta->error($this->t('Widget options not found'));
            return $response;
        }
        if ($optionsWindow instanceof Gm\Panel\Widget\OptionsWindow) {
            $optionsWindow->form->router->id = $widgetId;
        }

        /** @var string|null $title Заголовов виджета */
        $title = Gm::$app->widgets->getName($widgetAR->widgetId);
        // если ошибка определения заголовка
        if ($title === null) {
            $title = SYMBOL_NONAME;
        }
        $optionsWindow->title = $this->module->t('{options.title}', [$title]);
        $optionsWindow->icon = $this->module->getAssetsUrl() . '/images/icon-cog.svg';
        $optionsWindow->form->buttons = ExtForm::buttons([
            'help' => [
                'subject'   => 'options',
                'component' => 'widget:' . $widgetParams['id']
            ], 'reset', 'save', 'cancel'
        ]);

        $response
            ->setContent($optionsWindow->run())
            ->meta
                ->addWidget($optionsWindow);
        return $response;
    }

    /**
     * Действие "update" изменяет параметры виджета.
     * 
     * @return Response
     */
    public function updateAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

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

        /** @var null|array Параметры установленного виджета */
        $widgetParams = Gm::$app->widgets->getRegistry()->getAt($widgetAR->widgetId);
        if ($widgetParams === null) {
            $response
                ->meta->error($this->t('Widget not found'));
            return $response;
        }

        // т.к. виджет самостоятельно не может подключать свою локализацию (в данном случаи делает это модуль), 
        // то добавляем шаблон локализации виджета модулю
        $category = Gm::$app->translator->getCategory($this->module->id);
        $category->patterns['widget'] = [
            'basePath' => Gm::$app->modulePath . $widgetParams['path'] . DS . 'lang',
            'pattern'  => 'text-%s.php',
        ];
        $this->module->addTranslatePattern('widget');

        /** @var \Gm\Panel\Data\Model\WidgetOptionsModel|null $widgetOptions */
        $widgetOptions = Gm::$app->widgets->getObject('Model\Options', $widgetAR->widgetId, ['module' => $this->module]);
        if ($widgetOptions === null) {
            $response
                ->meta->error($this->t('Widget options not found'));
            return $response;
        }

        // загрузка параметров виджета в модель из запроса
        if (!$widgetOptions->load($request->getPost())) {
            $response
                ->meta->error(Gm::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        // валидация параметров виджета
        if (!$widgetOptions->validate()) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Error filling out form fields: {0}', [$widgetOptions->getError()]));
            return $response;
        }

        // сохранение параметров виджета
        $widgetAR->setOptions($widgetOptions->getAttributes());
        if (!$widgetAR->save()) {
            $response
                ->meta->error(
                    $widgetAR->hasErrors() ? $widgetAR->getError() : Gm::t(BACKEND, 'Could not save data')
                );
            return $response;
        } else {
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->t('Widget options changed successfully'), $this->t('Widget options'), 'accept'
                    )
                    ->command(
                        'callControllerMethod',
                        $this->module->viewId('tabs'), // g-dashboard-tabs
                        'refreshWidgetContent',
                        [
                            $this->module->viewId('widget-' . $widgetId) // g-dashboard-widget-{id}
                        ]
                    );
        }
        return $response;
    }
}
