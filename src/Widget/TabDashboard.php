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
use Gm\Panel\Widget\TabWidget;
use Gm\Panel\Http\Response\JsongMetadata;

/**
 * Вкладка интерактивной панели (панелей виджетов).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Dashboard\Widget
 * @since 1.0
 */
class TabDashboard extends TabWidget
{
    /**
     * Панели виджетов.
     * 
     * @var Panels
     */
    public Panels $panels;

    /**
     * {@inheritdoc}
     */
    public string $namespaceJs = 'Gm.be.dashboard';

    /**
     * {@inheritdoc}
     */
    public array $requires = [
        'Gm.be.dashboard.DashboardController',
        'Gm.be.dashboard.Panel'
    ];

    /**
     * {@inheritdoc}
     */
    public array $css = ['/dashboard.css'];

    /**
     * Метаданные виджета для HTTP-ответа.
     *
     * @var JsongMetadata|null
     */
    protected ?JsongMetadata $meta = null;

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель компонентов Маркетплейс (Gm.be.mp.catalog.View GmJS)
        $this->panels = new Panels(['meta' => $this->getMeta()]);

        $this->title   = '#{name}';
        $this->tooltip = [
            'icon'  => $this->imageSrc('/icon.svg'),
            'title' => '#{name}',
            'text'  => '#{description}'
        ];
        $this->icon  = $this->imageSrc('/icon_small.svg');
        $this->items = [$this->panels];
    }

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
        $this->panels->setMeta($meta);
    }

    /**
     * Возвращает метаданные HTTP-ответа.
     *
     * @return JsongMetadata|null
     */
    public function getMeta(): ?JsongMetadata
    {
        return $this->meta ?? null;
    }
}
