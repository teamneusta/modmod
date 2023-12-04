<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Widgets;

use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\JavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class LatestCommentsWidget implements WidgetInterface, AdditionalCssInterface, JavaScriptInterface
{
    public function __construct(
        private WidgetConfigurationInterface $configuration,
        private ListDataProviderInterface $dataProvider,
        private StandaloneView $view,
        private array $options
    ) {
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widgets/NewCommentsWidget');
        $this->view->assignMultiple([
            'moduleName'    => 'web_modmod',
            'items'         => $this->getItems(),
            'configuration' => $this->configuration,
        ]);

        return $this->view->render();
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

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getJavaScriptModuleInstructions(): array
    {
        return [
            JavaScriptModuleInstruction::create('TYPO3/CMS/Backend/ModuleMenu'),
            JavaScriptModuleInstruction::create('TYPO3/CMS/Core/Ajax/AjaxRequest'),
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
