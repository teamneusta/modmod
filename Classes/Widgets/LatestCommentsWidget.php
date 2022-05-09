<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Widgets;

use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequireJsModuleInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class LatestCommentsWidget implements WidgetInterface, RequireJsModuleInterface, AdditionalCssInterface
{
    private WidgetConfigurationInterface $configuration;

    private StandaloneView $view;

    private ListDataProviderInterface $dataProvider;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        ListDataProviderInterface $dataProvider,
        StandaloneView $view
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widgets/NewCommentsWidget');
        $this->view->assignMultiple([
            'moduleName'    => 'web_ModmodModerate',
            'items'         => $this->getItems(),
            'configuration' => $this->configuration,
        ]);

        return $this->view->render();
    }

    /**
     * @return string[]
     */
    public function getRequireJsModules(): array
    {
        return [
            'TYPO3/CMS/Backend/ModuleMenu',
            'TYPO3/CMS/Core/Ajax/AjaxRequest',
        ];
    }

    /**
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:modmod/Resources/Public/CSS/list-widget.css',
        ];
    }

    /**
     * @return array|array[]
     */
    protected function getItems(): array
    {
        return $this->dataProvider->getItems();
    }
}
