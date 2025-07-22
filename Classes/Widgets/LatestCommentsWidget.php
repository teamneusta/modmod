<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Widgets;

use Neusta\Modmod\Widgets\Provider\LatestCommentsDataProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\JavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\FluidViewAdapter;

final class LatestCommentsWidget implements WidgetInterface, AdditionalCssInterface, JavaScriptInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private WidgetConfigurationInterface $configuration,
        private ListDataProviderInterface $dataProvider,
        private BackendViewFactory $viewFactory,
        private array $options
    ) {
    }

    public function renderWidgetContent(): string
    {
        /** @var ViewInterface|FluidViewAdapter $view */
        $view = $this->viewFactory->create($this->request);
        $view->getRenderingContext()
            ->getTemplatePaths()
            ->setTemplatePathAndFilename(
                'EXT:modmod/Resources/Private/Templates/Widgets/NewCommentsWidget.html',
            );
        $view->assignMultiple([
            'moduleName'    => 'web_modmod',
            'items'         => $this->getItems(),
            'configuration' => $this->configuration,
        ]);

        return $view->render('Widgets/NewCommentsWidget');
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
            JavaScriptModuleInstruction::create('@typo3/backend/module-menu')->invoke('initialize'),
            JavaScriptModuleInstruction::create('@typo3/core/ajax/ajax-request'),
        ];
    }

    /**
     * @return array|array[]
     */
    protected function getItems(): array
    {
        return $this->dataProvider->getItems();
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
